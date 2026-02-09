<?php

require_once 'models/User.php';

class AuthController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    
    public function showRegister() {
        require 'views/register.php';
    }

    
    public function register() {
        if ($_POST) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $phone = trim($_POST['phone'] ?? '');

            
            if ($this->user->findByEmail($email)) {
                $_SESSION['error'] = "این ایمیل قبلاً ثبت شده است.";
            } elseif ($this->user->register($name, $email, $password, $phone ? $phone : null)) {
                $_SESSION['success'] = "ثبت‌نام با موفقیت انجام شد! حالا وارد شوید.";
                ob_clean();
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['error'] = "خطا در ثبت‌نام. لطفاً دوباره تلاش کنید.";
            }
        }
        $this->showRegister();
    }

    
    public function showLogin() {
        require 'views/login.php';
    }

    
    public function login() {
        if ($_POST) {
            $email = trim($_POST['email']);
            $password = $_POST['password'];

            $user = $this->user->login($email, $password);

            if ($user) {
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'] ?? 'user';
                $_SESSION['cart_count'] = 0;

                
                ob_clean();

                if ($_SESSION['role'] === 'admin') {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $_SESSION['error'] = "ایمیل یا رمز عبور اشتباه است.";
            }
        }
        $this->showLogin();
    }

    
    public function logout() {
        session_destroy();
        ob_clean();
        header("Location: index.php");
        exit;
    }
}
?>