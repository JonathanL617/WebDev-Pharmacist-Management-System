<?php
    include "../config/conn.php";
    include "password_functions.php";

    class AuthController {
        private $conn;

        public function __construct($conn){
            $this->conn = $conn;
        }

        public function login($email, $password){
            try {
                if(empty($email) || empty($password)){
                    throw new Exception('All feilds are required.');
                }

                //check for username or email in database
                $sql = "SELECT user_id, user_email, user_password, user_role FROM user_info WHERE user_email = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if($result->num_rows === 1){
                    $row = $result->fetch_assoc();

                    if(isPasswordCorrect($password, $row['user_password'])){
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['user_email'] = $row['user_email'];
                        $_SESSION['user_role'] = $row['user_role'];

                        $defaultPages = [
                            'superadmin' => 'manage_accounts',
                            'admin' => 'manage_users',
                            'pharmacist' => 'stock_management',
                            'doctor' => 'patient_records'
                        ];

                        $defaultRoute = '/dashboard?page=' . ($defaultPages[$_SESSION['user_role']]);
                        header("Location: " . $defaultRoute);
                        exit();
                    }
                }
                throw new Exception('Invalid email or password');
            }
            catch(Exception $e){
                throw $e;
            }
        }

        public function logout(){
            session_destroy();
            header("Location: /login");
            exit();
        }

        public function resetPassword($email, $newPassword, $confirmPassword){
            try {
                // Get POST values
                $reset_email = isset($_POST['reset_email']) ? trim($_POST['reset_email']) : '';
                $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
                $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

                // Validate inputs
                if (empty($reset_email) || empty($newPassword) || empty($confirmPassword)) {
                    throw new Exception('All fields are required.');
                }

                if (!filter_var($reset_email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Invalid email format.');
                }

                if ($newPassword !== $confirmPassword) {
                    throw new Exception('Passwords do not match.');
                }

                // Check if email exists
                $stmt = $this->conn->prepare("SELECT user_id FROM user_info WHERE user_email = ?");
                $stmt->bind_param("s", $reset_email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    throw new Exception('Email not found.');
                }

                // Hash the password (use password_hash for security)
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

                // Update the password
                $update = $this->conn->prepare("UPDATE user_info SET hashed_password = ? WHERE user_email = ?");
                $update->bind_param("ss", $hashedPassword, $reset_email);

                if ($update->execute()) {
                    $message = "Password reset successful! You can now log in.";
                    $showCard = "success";
                } else {
                    throw new Exception('Password reset failed. Please try again.');
                }
            } 
            catch (Exception $e) {
                $error = $e->getMessage();
                $showCard = "reset";
            } 
            finally {
                if (isset($stmt)) $stmt->close();
                if (isset($update)) $update->close();
            }
        }
        
    }
?>