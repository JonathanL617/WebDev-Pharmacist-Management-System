<?php
require_once __DIR__ . "/../config/conn.php";
require_once __DIR__ . "/password_functions.php";
require_once __DIR__ . "/../model/UserModel.php";

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
                // Start session if not already started
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                session_regenerate_id(true); // Prevent session fixation

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_email'] = $row['user_email'];
                $_SESSION['user_role'] = $row['user_role'];

                // Handle "Remember Me" with cookies
                if (isset($_POST['remember'])) {
                    // Store email only - never store passwords in cookies!
                    setcookie('user_login', $email, time() + (86400 * 30), "/"); // 30 days
                } else {
                    // Clear cookies if remember me is not checked
                    setcookie('user_login', '', time() - 3600, "/");
                }

                // Return JSON response
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success', 
                    'redirect' => BASE_URL . '/app/view/dashboard.php?page=' . $this->getDefaultPage($row['user_role'])
                ]);
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

    private function getDefaultPage($role) {
        $defaultPages = [
            'superadmin' => 'manage_accounts',
            'admin' => 'manage_users',
            'pharmacist' => 'stock_management',
            'doctor' => 'patient_records'
        ];
        
        return $defaultPages[$role] ?? 'manage_users';
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: " . BASE_URL . '/app/view/login_page.php');
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
                if ($userModel->updatePassword($email, $hashedPassword, $user['user_table'])) {
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

// Handle login request if this file is accessed directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $auth = new AuthController($conn);
    $auth->login($_POST['email'], $_POST['password']);
}
?>