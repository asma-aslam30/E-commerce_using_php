<?php
class CategoryModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }
 
    public function createCategory($category_name) {
        $query = "INSERT INTO tbl_categories (category_name) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category_name);
        return $stmt->execute();
    }
 
    public function getAllCategories() {
        $query = "SELECT * FROM tbl_categories";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
