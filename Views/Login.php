<?php
include_once '../config/session_check.php';
include '../config/connection.php';

$emailErr = $passwordErr = $invalid = "";
$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valid = true;

    if (empty($_POST["email"])) {
        $emailErr = "*Email is required";
        $valid = false;
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $emailErr = "*Invalid email format";
        $valid = false;
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
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

    if ($valid) {
        $query = "SELECT * FROM tbl_registration WHERE email = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                header("Location: dashboard.php");
                exit;
            } else {
                $invalid = "*Invalid email or password";
            }
        } else {
            $invalid = "*Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <!-- ✅ Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e3f2fd, #ffffff);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: none;
        }
        .error {
            color: red;
            font-size: 13px;
        }
        .login-title {
            font-weight: 600;
            color: #333;
        }
        .register-link {
            color: #28a745;
            text-decoration: none;
        }
        .register-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4" style="width: 400px;">
        <h3 class="text-center mb-4 login-title">User Login</h3>
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($email); ?>">
                <span class="error"><?php echo $emailErr; ?></span>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter password">
                <span class="error"><?php echo $passwordErr; ?></span>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            <div class="text-center mt-2">
                <span class="error"><?php echo $invalid; ?></span>
            </div>
        </form>

        <div class="text-center mt-3">
            <p>Don’t have an account? <a href="Registration.php" class="register-link">Register</a></p>
        </div>
    </div>
</div>

</body>
</html>
