<?php
include "../config/conn.php";
include "password_functions.php";
include "../model/UserModel.php";

class AuthController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login($email, $password) {
        try {
            if (empty($email) || empty($password)) {
                throw new Exception('All fields are required.');
            }

            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }

            $userModel = new UserModel($this->conn);
            $row = $userModel->getUserByEmail($email);

            if ($row && isPasswordCorrect($password, $row['user_password'])) {
                session_start();
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_email'] = $row['user_email'];
                $_SESSION['user_role'] = $row['user_role'];

                // Optional: Handle "Remember Me" with cookies
                if (isset($_POST['remember'])) {
                    setcookie('user_login', $email, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $password, time() + (86400 * 30), "/"); // Note: Store plain text password is insecure; consider token instead
                }

                // Return JSON response instead of redirect
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'redirect' => BASE_URL . '/app/views/dashboard.php']);
                exit();
            }
            
            throw new Exception('Invalid email or password');
        } 
        catch(Exception $e){
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit();
        }
    }

    public function logout() {
        session_start(); // Ensure session is started
        session_destroy();
        header("Location: " . BASE_URL . '/app/views/login_page.php');
        exit();
    }

    public function resetPassword($email, $newPassword, $confirmPassword) {
        try {
            if (empty($email) || empty($newPassword) || empty($confirmPassword)) {
                throw new Exception('All fields are required.');
            }

            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }

            if ($newPassword !== $confirmPassword) {
                throw new Exception('Passwords do not match.');
            }

            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters long.');
            }

            $userModel = new UserModel($this->conn);
            $user = $userModel->getUserByEmail($email);

            if ($user) {
                $hashedPassword = hashPassword($newPassword);
                if ($userModel->updatePassword($email, $hashedPassword)) {
                    return true; // Success
                } else {
                    throw new Exception('Password reset failed. Please try again.');
                }
            } else {
                throw new Exception('Email not found.');
            }
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>