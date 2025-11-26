<?php

    class SuperAdminModel {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function getStats() {
            $totalStmt    = $this->conn->query("SELECT COUNT(*) AS total FROM admin");
            $activeStmt   = $this->conn->query("SELECT COUNT(*) AS active FROM admin WHERE admin_login_status = 'active'");
            $inactiveStmt = $this->conn->query("SELECT COUNT(*) AS inactive FROM admin WHERE admin_login_status = 'inactive'");

            $total    = $totalStmt->fetch_assoc()['total'];
            $active   = $activeStmt->fetch_assoc()['active'];
            $inactive = $inactiveStmt->fetch_assoc()['inactive'];

            // Admins registered BY the current user (from session)
            $superId = $_SESSION['super_admin_id'] ?? null;

            $registeredByCurrent = 0;
            if ($superId) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM admin WHERE registered_by = ?");
                $stmt->bind_param('s', $superId);
                $stmt->execute();
                $registeredByCurrent = $stmt->get_result()->fetch_assoc()['count'];
                $stmt->close();
            }

            return [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'registered_by_current' => $registeredByCurrent
            ];
        }

        public function getAllAdmin(){
            $sql = "SELECT a.admin_id AS id, 
                           a.admin_username AS username, 
                           a.admin_email AS email, 
                           DATE_FORMAT(a.admin_dob, '%Y-%m-%d') AS dob, 
                           a.admin_login_status AS status,
                           a.registered_by
                           FROM admin a
                           ORDER BY a.admin_id DESC";
            
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function createAdmin(array $data): array {
            $adminId  = trim($data['admin_id'] ?? '');
            $username = trim($data['username'] ?? '');
            $email    = trim($data['email'] ?? '');
            $dob      = $data['dob'] ?? '';
            $password = $data['password'] ?? '';
            $regBy    = $data['registered_by'] ?? '';

            if (empty($adminId) || empty($username) || empty($email) || empty($dob) || empty($password)) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            if (empty($regBy)) {
                return ['success' => false, 'message' => 'Registered by field is required. Please log out and log back in.'];
            }

            //duplicate check
            $chk = $this->conn->prepare(
                "SELECT admin_id FROM admin WHERE admin_id = ? OR admin_username = ? OR admin_email = ?"
            );
            $chk->bind_param('sss', $adminId, $username, $email);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $chk->close();
                return ['success' => false, 'message' => 'Admin ID, username or email already exists'];
            }
            $chk->close();

            
            $hash = password_hash($password, PASSWORD_ARGON2I);

            $ins = $this->conn->prepare("
                INSERT INTO admin 
                    (admin_id, admin_username, admin_email, admin_dob, admin_password, admin_login_status, registered_by)
                VALUES 
                    (?, ?, ?, ?, ?, 'active', ?)
            ");
            $ins->bind_param('ssssss', $adminId, $username, $email, $dob, $hash, $regBy);
            $ok = $ins->execute();
            $ins->close();

            return [
                'success' => $ok,
                'message' => $ok ? 'Admin created' : 'Failed to create admin'
            ];
        }

        public function deleteAdmin($adminId){
            $stmt = $this->conn->prepare("DELETE FROM admin WHERE admin_id = ?");
            $stmt->bind_param('s', $adminId);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        }

        public function toggleStatus($adminId, $newStatus){
            if(!in_array($newStatus, ['active', 'inactive'])) return false;

            $stmt = $this->conn->prepare("UPDATE admin SET admin_login_status = ? WHERE admin_id = ?");
            $stmt->bind_param('ss', $newStatus, $adminId);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        }

        public function getPendingStaff() {
            $sql = "SELECT 
                        ps.*, 
                        a.admin_username as requester_name,
                        ua.approval_id,
                        ua.status as approval_status
                    FROM pending_staff ps
                    LEFT JOIN admin a ON ps.registered_by = a.admin_id
                    LEFT JOIN user_approval ua ON ps.staff_id = ua.approved_user_id
                    WHERE ua.status = 'pending'
                    ORDER BY ps.created_at DESC";
            
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function updateStaffStatus($staffId, $status) {
            // Allowed statuses - lowercase to match database
            if (!in_array($status, ['approved', 'rejected'])) {
                return false;
            }

            // Get pending staff data
            $getPending = $this->conn->prepare("SELECT * FROM pending_staff WHERE staff_id = ?");
            $getPending->bind_param('s', $staffId);
            $getPending->execute();
            $pendingData = $getPending->get_result()->fetch_assoc();
            $getPending->close();

            if (!$pendingData) {
                return false;
            }

            // Update user_approval status
            $updateApproval = $this->conn->prepare("
                UPDATE user_approval 
                SET status = ?, 
                    approval_date = NOW(),
                    approver_id = ? 
                WHERE approved_user_id = ? AND status = 'pending'
            ");
            $superAdminId = $_SESSION['super_admin_id'] ?? 'SA001';
            $updateApproval->bind_param('sss', $status, $superAdminId, $staffId);
            $updateApproval->execute();
            $updateApproval->close();

            if ($status === 'approved') {
                // Insert into staff table with active status
                $defaultPass = password_hash('password123', PASSWORD_DEFAULT);
                $activeStatus = 'active';
                
                $insertStaff = $this->conn->prepare("
                    INSERT INTO staff 
                    (staff_id, staff_name, staff_dob, staff_specialization, staff_role, staff_phone, staff_email, staff_password, registered_by, staff_status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $insertStaff->bind_param('ssssssssss',
                    $pendingData['staff_id'],
                    $pendingData['staff_name'],
                    $pendingData['staff_dob'],
                    $pendingData['staff_specialization'],
                    $pendingData['staff_role'],
                    $pendingData['staff_phone'],
                    $pendingData['staff_email'],
                    $defaultPass,
                    $pendingData['registered_by'],
                    $activeStatus
                );
                
                $success = $insertStaff->execute();
                $insertStaff->close();
                
                if ($success) {
                    // Delete from pending_staff table
                    $deletePending = $this->conn->prepare("DELETE FROM pending_staff WHERE staff_id = ?");
                    $deletePending->bind_param('s', $staffId);
                    $deletePending->execute();
                    $deletePending->close();
                }
                
                return $success;
            } else {
                // Rejected - just delete from pending_staff
                $deletePending = $this->conn->prepare("DELETE FROM pending_staff WHERE staff_id = ?");
                $deletePending->bind_param('s', $staffId);
                $success = $deletePending->execute();
                $deletePending->close();
                
                return $success;
            }
        }
    }
?>