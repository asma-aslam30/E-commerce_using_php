<?php 
include '../config/connection.php';
include_once '../config/session_check.php';

$nameErr = $emailErr = $passwordErr = $phoneErr = $designationErr = "";
$name = $email = $password = $phone = $designation = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (empty($_POST["name"])) {
        $nameErr = "*Name is required";
        $valid = false;
    } else {
        $name = trim($_POST["name"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "*Email is required";
        $valid = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "*Invalid email format";
        $valid = false;
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "*Password is required";
        $valid = false;
    } else {
        $password = $_POST["password"];
        if (!preg_match("/(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}/", $password)) {
            $passwordErr = "*Password must have at least 6 chars, one letter and one number";
            $valid = false;
        }
    }

    if (empty($_POST["phone"])) {
        $phoneErr = "*Phone is required";
        $valid = false;
    } elseif (!preg_match("/^\d{10,11}$/", $_POST["phone"])) {
        $phoneErr = "*Enter 11 digits only";
        $valid = false;
    } else {
        $phone = trim($_POST["phone"]);
    }

    if (empty($_POST["designation"])) {
        $designationErr = "*Please select a designation";
        $valid = false;
    } else {
        $designation = $_POST["designation"];
    }

    if ($valid) {
        include_once '../Controller/RegisterController.php';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dfe9f3, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .btn-success {
            background-color: #28a745;
            border: none;
            transition: 0.3s;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #28a745;
        }
        .error {
            color: red;
            font-size: 13px;
        }
        .register-title {
            font-weight: 600;
            color: #333;
        }
        .login-link {
            color: #007bff;
            text-decoration: none;
        }
        .login-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 400px;">
        <h3 class="text-center mb-4 register-title">User Registration</h3>
        <form method="POST" action="">
            
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="Enter full name"
                       value="<?php echo htmlspecialchars($name); ?>">
                <span class="error"><?php echo $nameErr; ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($email); ?>">
                <span class="error"><?php echo $emailErr; ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Create password">
                <span class="error"><?php echo $passwordErr; ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="Enter phone number"
                       value="<?php echo htmlspecialchars($phone); ?>">
                <span class="error"><?php echo $phoneErr; ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">Designation</label>
                <select name="designation" class="form-select">
                    <option value="">Select Designation</option>
                    <option value="Manager" <?php if ($designation=="Manager") echo "selected"; ?>>Manager</option>
                    <option value="Developer" <?php if ($designation=="Developer") echo "selected"; ?>>Developer</option>
                    <option value="Designer" <?php if ($designation=="Designer") echo "selected"; ?>>Designer</option>
                </select>
                <span class="error"><?php echo $designationErr; ?></span>
            </div>

            <button type="submit" name="register" class="btn btn-success w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <p>Already have an account? <a href="Login.php" class="login-link">Login</a></p>
        </div>
    </div>
</div>

</body>
</html>
