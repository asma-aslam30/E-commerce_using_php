<?php
class CategoryImageModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    //  Get all images for a specific product
    public function getProductImages($product_id) {
        $query = "SELECT * FROM tbl_category_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    //  Add one or multiple images for a product
    public function addCategoryImages($product_id, $imagePaths) {
        // Agar sirf ek image path string me mila ho
        if (!is_array($imagePaths)) {
            $imagePaths = [$imagePaths];
        }

        $query = "INSERT INTO tbl_category_images (product_id, image_path, uploaded_at)
                  VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($query);

        foreach ($imagePaths as $path) {
            $stmt->bind_param("is", $product_id, $path);
            $stmt->execute();
        }

        return true;
    }
}
?>
