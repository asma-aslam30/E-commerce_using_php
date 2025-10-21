<?php
session_start();
include_once '../config/connection.php';
include_once '../Models/LoginModels.php';

class LoginController {
    private $model;

    public function __construct($connection) {
        $this->model = new LoginModel($connection);
    }

    public function loginUser() {
        if (isset($_POST['login'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
          
            $user = $this->model->validateUser($email, $password);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];

            
                header("Location: ../Views/dashboard.php");
                exit();
            } else {
 
                header("Location: ../Views/Login.php?error=1");
                exit();
            }
        }
    }
 

}

// Controller ka object aur function call
$controller = new LoginController($connection);
$controller->loginUser();
?>
