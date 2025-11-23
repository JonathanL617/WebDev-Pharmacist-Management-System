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

            echo json_encode([
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive,
                'registered_by_current' => $registeredByCurrent
            ]);
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
            $username = trim($data['username'] ?? '');
            $email    = trim($data['email'] ?? '');
            $dob      = $data['dob'] ?? '';
            $password = $data['password'] ?? '';
            $regBy    = $data['registered_by'] ?? 0;

            if (!$username || !$email || !$dob || !$password || !$regBy) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            //duplicate check
            $chk = $this->conn->prepare(
                "SELECT admin_id FROM admin WHERE admin_username = ? OR admin_email = ?"
            );
            $chk->bind_param('ss', $username, $email);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $chk->close();
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            $chk->close();

            
            $hash = password_hash($password, PASSWORD_ARGON2I);

            $ins = $this->conn->prepare("
                INSERT INTO admin 
                    (admin_username, admin_email, admin_dob, admin_password, admin_login_status, registered_by)
                VALUES 
                    (?, ?, ?, ?, 'active', ?)
            ");
            $ins->bind_param('sssss', $username, $email, $dob, $hash, $regBy);
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

        // Staff Request Management
        public function getPendingStaff() {
            $sql = "SELECT s.*, a.admin_username as requester_name 
                    FROM staff s 
                    LEFT JOIN admin a ON s.registered_by = a.admin_id 
                    WHERE s.staff_status = 'Pending' 
                    ORDER BY s.staff_id DESC";
            
            $result = $this->conn->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        public function updateStaffStatus($staffId, $status) {
            // Allowed statuses
            if (!in_array($status, ['Active', 'Rejected'])) {
                return false;
            }

            $stmt = $this->conn->prepare("UPDATE staff SET staff_status = ? WHERE staff_id = ?");
            $stmt->bind_param('ss', $status, $staffId);
            $success = $stmt->execute();
            $stmt->close();

            return $success;
        }
    }
?>