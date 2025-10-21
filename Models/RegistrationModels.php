<?php
class RegistrationModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // REGISTER
  public function registerUser($name, $email, $password, $phone, $designation, $created_by) {
    $query = "INSERT INTO tbl_registration (name, email, password, phone, designation, created_by, created_at)
              VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $password, $phone, $designation, $created_by);
    $exec = $stmt->execute();
    if ($exec) {
        return $this->conn->insert_id; 
    }
    return false;
}

public function updateUser($id, $name, $email, $phone, $designation, $cv_path, $updated_by, $updated_at) { 
    $sql = "UPDATE tbl_registration SET name=?, email=?, phone=?, designation=?, cv_path=?, updated_by=?, updated_at=? 
    WHERE id=?"; 
    $stmt = $this->conn->prepare($sql); 
    $stmt->bind_param("sssssssi", $name, $email, $phone, $designation, $cv_path, $updated_by, $updated_at, $id); 
    return $stmt->execute();
 }



public function updateCVPath($id, $cv_path) {
    $sql = "UPDATE tbl_registration SET cv_path=? WHERE id=?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("si", $cv_path, $id);
    return $stmt->execute();
}

public function addUser($name, $email, $password, $phone, $designation, $cv_name,$created_by) {
    $sql = "INSERT INTO tbl_registration (name, email, password, phone, designation, cv_path,created_by)
            VALUES (?, ?, ?, ?, ?,?,?)";
    $stmt = $this->conn->prepare($sql);
    if (!$stmt) die("DB Prepare Error: " . $this->conn->error);

    if (!$stmt->bind_param("sssssss", $name, $email, $password, $phone, $designation, $cv_name,$created_by)) {
        die("Bind Param Error: " . $stmt->error);
    }

    if (!$stmt->execute()) die("Execute Error: " . $stmt->error);

     $user_id = $this->conn->insert_id;

        $stmt->close();
        return $user_id;
}

    // CHECK EMAIL
    public function checkEmailExists($email) {
        $query = "SELECT * FROM tbl_registration WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getAllUsers($limit, $offset) {
    $query = "SELECT * FROM tbl_registration LIMIT ? OFFSET ?";
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    return $stmt->get_result();
    }


public function getTotalUsers() {
    $query = "SELECT COUNT(*) as total FROM tbl_registration";
    $result = mysqli_query($this->conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['total'];
}


    
    public function getUserById($id) {
        $query = "SELECT * FROM tbl_registration WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function deleteUser($id) {
        $query = "DELETE FROM tbl_registration WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
