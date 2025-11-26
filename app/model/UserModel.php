<?php
class UserModel {
    private $conn;

    public function __construct($conn){
        $this->conn = $conn;
    }

    public function getUserByEmail($email){
        // Check super_admin table first
        $stmt = $this->conn->prepare("SELECT super_admin_id AS user_id, super_admin_email AS user_email, super_admin_password AS user_password, 'superadmin' AS user_role, 'super_admin' AS user_table, 'active' AS user_status FROM super_admin WHERE super_admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            return $row;
        }

        // Check admin table
        $stmt = $this->conn->prepare("SELECT admin_id AS user_id, admin_email AS user_email, admin_password AS user_password, 'admin' AS user_role, 'admin' AS user_table, admin_login_status AS user_status FROM admin WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            return $row;
        }

        // Check staff table (for doctors and pharmacists)
        $stmt = $this->conn->prepare("SELECT staff_id AS user_id, staff_email AS user_email, staff_password AS user_password, staff_role AS user_role, 'staff' AS user_table, staff_status AS user_status FROM staff WHERE staff_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ?: null;
    }

    public function updatePassword($email, $hashedPassword, $table){
        // Define the correct field names for each table
        if($table === 'super_admin'){
            $emailField = 'super_admin_email';
            $passwordField = 'super_admin_password';
        }
        elseif($table === 'admin'){
            $emailField = 'admin_email';
            $passwordField = 'admin_password';
        }
        elseif($table === 'staff'){
            $emailField = 'staff_email';
            $passwordField = 'staff_password';
        }
        else {
            return false; // Invalid table
        }
        
        // Use prepared statement to prevent SQL injection
        $sql = "UPDATE `$table` SET `$passwordField` = ? WHERE `$emailField` = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $email);
        $success = $stmt->execute();
        $stmt->close(); 

        return $success;
    }
}
?>