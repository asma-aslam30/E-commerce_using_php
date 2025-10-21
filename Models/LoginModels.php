<?php
class LoginModel {
    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function validateUser($email, $password) {
        $query = "SELECT * FROM tbl_registration WHERE email = ?";
        $result = mysqli_prepare($this->connection, $query);

        if (!$result) {
            die("Prepare failed: " . mysqli_error($this->connection));
        }

        mysqli_stmt_bind_param($result, "s", $email);
        mysqli_stmt_execute($result);
        $result_final = mysqli_stmt_get_result($result);
 
        if ($result_final && mysqli_num_rows($result_final) === 1) {
            $user = mysqli_fetch_assoc($result_final);
            $hashedPassword = $user['password'];  
            if (password_verify($password, $hashedPassword)) { 
                return $user;
            } else { 
                return false;
            }
        } else {
            return false;
        }
    }
}
?>
