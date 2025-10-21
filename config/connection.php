<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "employee_management_system";

$connection = mysqli_connect($servername, $username, $password, $database);

if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
else{
    // echo"database connect successfully";
}
?>
