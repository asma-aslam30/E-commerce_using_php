<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/RegistrationModels.php';

class RegistrationController {
    private $model;

    public function __construct($connection) {
        $this->model = new RegistrationModel($connection);
    }

//  register user 
    public function registerUser() {
        if (isset($_POST['register'])) {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $phone = trim($_POST['phone']);
            $designation = trim($_POST['designation']);
            
            
            $created_by = $name;  

            $errors = [];

            // Validation
            if (!preg_match("/^[A-Za-z\s]{3,50}$/", $name)) {
                $errors[] = "Name must be 3-50 letters and spaces only.";
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password)) {
                $errors[] = "Password must be at least 6 characters, with 1 letter and 1 number.";
            }
            // /^[0-9]{10}+$/
            if (!preg_match("/^[0-9]{11}+$/", $phone)) {
                $errors[] = "Phone number must be 10-15 digits.";
            }
            if (empty($designation)) {
                $errors[] = "Please select a designation.";
            }
            if ($this->model->checkEmailExists($email)) {
                $errors[] = "Email already exists!";
            }

            if (!empty($errors)) {
                $msg = implode("\\n", $errors);
                echo "<script>alert('$msg'); window.history.back();</script>";
                return;
            }
    
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Register user
            $newUserId = $this->model->registerUser($name, $email, $hashedPassword, $phone, $designation, $created_by);

            if ($newUserId) {
                //  Session set karenge for auto-login
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                header("Location: ../Views/dashboard.php");
                exit();
            } else {
                echo "<script>alert('Error during registration!'); window.history.back();</script>";
            }
        }
    }


 

//  delete user
    public function deleteUser($id) {
    if ($this->model->deleteUser($id)) {
        echo "<script>alert('User deleted successfully!'); window.location.href='../Views/Dashboard_final.php';</script>";
    } else {
        echo "<script>alert('Failed to delete user!'); window.history.back()';</script>";
    }
}


 
public function updateUser() {
    if (isset($_POST['update_user'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $designation = $_POST['designation'];
        $updated_by = $_SESSION['name'] ?? 'Admin';
        $updated_at = date('Y-m-d H:i:s'); 
        $cv_path = $_FILES['cv_path']['name'] ?? '';
        $upload_dir = "../Uploads/$id/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        if (!empty($cv_path)) {
            $target_file = $upload_dir . time() . "_" . basename($cv_path);
            move_uploaded_file($_FILES['cv_path']['tmp_name'], $target_file);
        } else {
            $user = $this->model->getUserById($id);
            $target_file = $user['cv_path'];
        } 
        $update = $this->model->updateUser(
            $id, $name, $email, $phone, $designation,
            $target_file, $updated_by, $updated_at
        ); 
        if ($update) {
            echo "<script>alert('User updated successfully!'); window.location.href='../Views/Dashboard_final.php';</script>";
        } else {
            echo "<script>alert('Update failed!'); window.history.back();</script>";
        }
    }
}

// public function addUser() {
//     if (!isset($_POST['add_user'])) return false;

//     $name = trim($_POST['name'] ?? '');
//     $email = trim($_POST['email'] ?? '');
//     $phone = trim($_POST['phone'] ?? '');
//     $designation = trim($_POST['designation'] ?? '');
//     $password_plain = $_POST['password'] ?? '';
//     $created_by = $_SESSION['name'] ?? 'Admin'; // jo login user add kar raha h
//     $errors = [];

//     if (!preg_match("/^[A-Za-z\s]{3,50}$/", $name)) {
//                 $errors[] = "Name must be 3-50 letters and spaces only.";
//             }
//             if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                 $errors[] = "Invalid email format.";
//             }
//             if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password)) {
//                 $errors[] = "Password must be at least 6 characters, with 1 letter and 1 number.";
//             }
//             // /^[0-9]{10}+$/
//             if (!preg_match("/^[0-9]{11}+$/", $phone)) {
//                 $errors[] = "Phone number must be 10-15 digits.";
//             }
//             if (empty($designation)) {
//                 $errors[] = "Please select a designation.";
//             }
//             if ($this->model->checkEmailExists($email)) {
//                 $errors[] = "Email already exists!";
//             }

//             if (!empty($errors)) {
//                 $msg = implode("\\n", $errors);
//                 echo "<script>alert('$msg'); window.history.back();</script>";
//                 return;
//             }

//     if ($this->model->checkEmailExists($email)) {
//         die("Email already exists!");
//     }
//     $password = password_hash($password_plain, PASSWORD_DEFAULT);

//     $user_id = $this->model->addUser($name, $email, $password, $phone, $designation, '', $created_by);

//     if (!$user_id) {
//         die("Failed to add user to database!");
//     }

//     $cv_name = '';
//     if (isset($_FILES['cv_path']) && $_FILES['cv_path']['error'] === UPLOAD_ERR_OK) {
//         $tmp_name = $_FILES['cv_path']['tmp_name'];
//         $upload_dir = __DIR__ . "/../Uploads/" . $user_id . "/";

//         if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

//         $cv_name = time() . "_" . basename($_FILES['cv_path']['name']);
//         $target = $upload_dir . $cv_name;

//         if (!move_uploaded_file($tmp_name, $target)) {
//             die("File upload failed!");
//         }

      
//         $this->model->updateCVPath($user_id, $cv_name);
//     }

     
//     echo "<script>alert('User added successfully!'); window.location.href='../Views/dashboard.php';</script>";
// }

public function addUser() {
    if (!isset($_POST['add_user'])) return false;

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $designation = trim($_POST['designation'] ?? '');
    $password_plain = $_POST['password'] ?? '';
    $created_by = $_SESSION['name'] ?? 'Admin';
    $errors = [];

    // Validations
    if (!preg_match("/^[A-Za-z\s]{3,50}$/", $name)) {
        $errors[] = "Name must be 3-50 letters and spaces only.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/", $password_plain)) {
        $errors[] = "Password must be at least 6 characters, with 1 letter and 1 number.";
    }
    if (!preg_match("/^[0-9]{10,15}$/", $phone)) {
        $errors[] = "Phone number must be 10-15 digits.";
    }
    if (empty($designation)) {
        $errors[] = "Please select a designation.";
    }
    if ($this->model->checkEmailExists($email)) {
        $errors[] = "Email already exists!";
    }

    if (!empty($errors)) {
        $msg = implode("\\n", $errors);
        echo "<script>alert('$msg'); window.history.back();</script>";
        return;
    }

    // Password Hash
    $password = password_hash($password_plain, PASSWORD_DEFAULT);
    $user_id = $this->model->addUser($name, $email, $password, $phone, $designation, '', $created_by);

    if (!$user_id) {
        die("Failed to add user to database!");
    }

    // File Upload
    if (isset($_FILES['cv_path']) && $_FILES['cv_path']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['pdf'];
        $ext = strtolower(pathinfo($_FILES['cv_path']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            die("Only PDF files are allowed!");
        }

        $tmp_name = $_FILES['cv_path']['tmp_name'];
        $upload_dir = __DIR__ . "/../Uploads/" . $user_id . "/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $cv_name = time() . "_" . basename($_FILES['cv_path']['name']);
        $target = $upload_dir . $cv_name;

        if (!move_uploaded_file($tmp_name, $target)) {
            die("File upload failed!");
        }

        $this->model->updateCVPath($user_id, $cv_name);
    }

    echo "<script>alert('User added successfully!'); window.location.href='../Views/Dashboard_final.php';</script>";
}


}




$controller = new RegistrationController($connection);

if (isset($_POST['add_user'])) {
    $controller->addUser();
}

if (isset($_POST['update_user'])) {
    $controller->updateUser();
}
if(isset($_POST['register'])){
    $controller->registerUser();
}

if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $controller->deleteUser($_GET['id']);
}

?>
