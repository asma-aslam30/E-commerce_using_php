<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/RegistrationModels.php';

$nameErr = $emailErr = $phoneErr = $designationErr = $passwordErr =$fileErr= "";
$name = $email = $phone = $designation = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    // Name validation
    if (empty($_POST["name"])) {
        $nameErr = "*Name is required";
        $valid = false;
    } else {
        $name = trim($_POST["name"]);
    }

    // Email validation
    if (empty($_POST["email"])) {
        $emailErr = "*Email is required";
        $valid = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "*Invalid email format";
        $valid = false;
    } else {
        $email = trim($_POST["email"]);
    }

    // Phone validation
    if (empty($_POST["phone"])) {
        $phoneErr = "*Phone number is required";
        $valid = false;
    } elseif (!preg_match("/^\d{11}$/", $_POST["phone"])) {
        $phoneErr = "*Phone must be 11 digits";
        $valid = false;
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Designation validation
    if (empty($_POST["designation"])) {
        $designationErr = "*Designation is required";
        $valid = false;
    } else {
        $designation = trim($_POST["designation"]);
    }

    // Password validation
    if (empty($_POST["password"])) {
        $passwordErr = "*Password is required";
        $valid = false;
    } elseif (!preg_match("/(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}/", $_POST["password"])) {
        $passwordErr = "*Password must have at least 6 chars, one letter & one number";
        $valid = false;
    } else {
        $password = $_POST["password"];
    }
    // file validation

   if (!isset($_FILES['cv_path']) || $_FILES['cv_path']['error'] == UPLOAD_ERR_NO_FILE) {
        $fileErr = "*Please upload a file";
        $valid = false;
    } else {
        $file = $_FILES['cv_path'];
        $allowedTypes = ['application/pdf'];
        $maxSize = 500000;  

        if (!in_array($file['type'], $allowedTypes)) {
            $fileErr = "*Only PDF files are allowed";
            $valid = false;
        } elseif ($file['size'] > $maxSize) {
            $fileErr = "*File size must be less than 2MB";
            $valid = false;
        }
    }

    // If all valid, go to controller
    if ($valid) {
        include_once '../Controller/RegisterController.php';
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
     <?php include('../layout/header_links.php'); ?>
    <style>
        body {
            font-family: Arial, sans-serif !important;
            background: #f5f5f5 !important;
            margin: 0 !important;
        }

        .body {
            display: flex !important;
            justify-content: center !important; 
            align-items: center !important; 
            height: 100vh !important; 
            background-color: #f4f6f8 !important;
        }

        form {
            width: 400px !important;
            background: #fff !important;
            padding: 25px !important;
            border-radius: 12px !important;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
        }

        label {
            font-weight: bold !important;
        }

        .input {
            /* width: 100%; */
            padding: 10px !important;
            margin: 5px 0 !important;
            border: 1px solid #ccc !important;
            border-radius: 5px !important;
        }

        .error {
            color: red !important;
            font-size: 13px !important;
        }

        button {
            background: #007bff !important;
            color: white !important;
            padding: 10px !important;
            border: none !important;
            width: 100% !important;
            cursor: pointer !important;
            border-radius: 5px !important;
            font-size: 16px !important;
        }

        button:hover {
            background: #0056b3 !important;
        }

        h2 {
            text-align: center !important;
            margin-bottom: 20px !important;
        }

         /* body { font-family: Arial; background: #f5f5f5; } */
        .form-container { width: 400px; margin: 80px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
        .input, .select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
        .input[type=submit] { background: #28a745; color: white; cursor: pointer; border: none; }
        .input[type=submit]:hover { background: #218838; }
        a { text-decoration: none; color: #007bff; }
        .error { color: red; font-size: 12px; }
    </style>

   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>

<body>
<div id="wrapper">
    <?php include('../layout/sidebar.php'); ?>

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include('../layout/header.php'); ?>
        </div>

        <div class="body">
            <form method="POST" enctype="multipart/form-data">
                <h2>Add New User</h2>

                <label>Name:</label>
                <input type="text" name="name" class="input" value="<?php echo $name; ?>" placeholder="Enter name"><br>
                <span class="error"><?php echo $nameErr; ?></span><br>

                <label>Email:</label>
                <input type="email" name="email" class="input" value="<?php echo $email; ?>" placeholder="Enter email"><br>
                <span class="error"><?php echo $emailErr; ?></span><br>

                <label>Phone:</label>
                <input type="text" name="phone" class="input" value="<?php echo $phone; ?>" placeholder="Enter phone number"><br>
                <span class="error"><?php echo $phoneErr; ?></span><br>

                <label>Designation:</label>
                <input type="text" name="designation" class="select" value="<?php echo $designation; ?>" placeholder="Enter designation"><br>
                <span class="error"><?php echo $designationErr; ?></span><br>

                <label>Password:</label>
                <input type="password" name="password" class="input" placeholder="Enter password"><br>
                <span class="error"><?php echo $passwordErr; ?></span><br>

                <label>Upload File :</label>
                <input type="file" name="cv_path" class="input" accept=".pdf">
                <span class="error"><?php echo $fileErr ; ?></span><br>


              

                <button type="submit" name="add_user">Add User</button>
            </form>
        </div>

        <?php include('../layout/footer.php'); ?>
        <?php include('../layout/footer_links.php'); ?>
    </div>
</div>


</body>
</html>
