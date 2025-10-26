<?php
    class UserModel {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function getUserByEmail($email){
            $stmt = $this->conn->prepare("SELECT user_id, user_email, user_password, user_role FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row ?: null;
        }

        public function updatePassword($email, $hashedPassword){
            $stmt = $this->conn->prepare("UPDATE users SET user_password = ? WHERE user_email = ?");
            $stmt->bind_param("ss", $hashedPassword, $email);
            $success = $stmt->execute();
            $stmt->close(); 

            return $success;
        }
    }
?>