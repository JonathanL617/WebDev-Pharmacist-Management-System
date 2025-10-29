<?php
    require_once __DIR__ . "../config/config.php";
    require_once __DIR__ . "/../model/UserModel.php";

    class SuperAdminController {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function getStats() {
            $totalStmt    = $this->conn->query("SELECT COUNT(*) AS total FROM admin");
            $activeStmt   = $this->conn->query("SELECT COUNT(*) AS active FROM admin WHERE admin_status = 'active'");
            $inactiveStmt = $this->conn->query("SELECT COUNT(*) AS inactive FROM admin WHERE admin_status = 'inactive'");

            $total    = $totalStmt->fetch_assoc()['total'];
            $active   = $activeStmt->fetch_assoc()['active'];
            $inactive = $inactiveStmt->fetch_assoc()['inactive'];

            // Admins registered BY the current user (from session)
            $currentUserId = $_SESSION['user_id'] ?? null;

            $registeredByCurrent = 0;
            if ($currentUserId) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM admin WHERE registered_by = ?");
                $stmt->bind_param('s', $currentUserId);
                $stmt->execute();
                $registeredByCurrent = $stmt->get_result()->fetch_assoc()['count'];
                $stmt->close();
            }

            echo json_encode([
                'total'           => $total,
                'active'          => $active,
                'inactive'        => $inactive,
                'registered_by_current'=> $registeredByCurrent
            ]);
        }

        
    }
?>