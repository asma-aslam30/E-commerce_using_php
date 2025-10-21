<?php
// class ProductModel {
//     private $conn;

//     public function __construct($connection) {
//         $this->conn = $connection;
//     }

//     public function getConnection() {
//     return $this->conn;
// }


//     public function createProduct($name, $description, $price, $isHotSale, $isActive, $category_id, $created_by) {
//         $query = "INSERT INTO tbl_products 
//                   (product_name, product_description, product_price, isHotSale, isActive, category_id, product_created_by, product_created_at)
//                   VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("ssdiiii", $name, $description, $price, $isHotSale, $isActive, $category_id, $created_by);
//         return $stmt->execute();
//     }

    

//     public function deactivateProduct($id) {
//         $query = "UPDATE tbl_products SET isActive = 0 WHERE product_id = ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $id);
//         return $stmt->execute();
//     }

//     public function getProductById($id) {
//         $query = "SELECT * FROM tbl_products WHERE product_id = ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $id);
//         $stmt->execute();
//         return $stmt->get_result()->fetch_assoc();
//     }
 

 

// public function getAllProducts($limit, $offset) {
//     $query = "SELECT * FROM tbl_products ORDER BY product_id DESC LIMIT ? OFFSET ?";
//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("ii", $limit, $offset);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
// }


 
 
// public function getHotSaleProducts() {
//     $query = "
//         SELECT 
//             p.product_id,
//             p.product_name,
//             p.product_price,
//             p.isHotSale,
//             c.category_name,
//             ci.image_path
//         FROM 
//             tbl_products p
//         JOIN 
//             tbl_categories c ON p.category_id = c.category_id
//         JOIN 
//             tbl_category_images ci ON c.category_id = ci.category_id
//         WHERE 
//             p.isHotSale = 1
//         GROUP BY 
//             p.product_id
//     ";

//     $result = $this->conn->query($query);
//     return $result;
// }
// public function getHotSaleByCategory($categoryId) {
//     $query = "
//         SELECT 
//             p.product_id,
//             p.product_name,
//             p.product_price,
//             c.category_name,
//             ci.image_path
//         FROM 
//             tbl_products p
//         JOIN 
//             tbl_categories c ON p.category_id = c.category_id
//         JOIN 
//             tbl_category_images ci ON c.category_id = ci.category_id
//         WHERE 
//             p.isHotSale = 1 AND p.category_id = ?
//     ";

//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $categoryId);
//     $stmt->execute();
//     return $stmt->get_result();
// }

// public function getProductsByCategory($categoryId) {
//     $products = [];

//     $query = "
//         SELECT 
//             p.product_id,
//             p.product_name,
//             p.product_price,
//             c.category_name,
//             ci.image_path
//         FROM 
//             tbl_products p
//         JOIN 
//             tbl_categories c ON p.category_id = c.category_id
//         LEFT JOIN 
//             tbl_category_images ci ON c.category_id = ci.category_id
//         WHERE 
//             p.category_id = ?
//         GROUP BY 
//             p.product_id
//     ";

//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $categoryId);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     while ($row = $result->fetch_assoc()) {
//         $products[] = $row;
//     }

//     return $products;
// }


// public function getAllCategoriesWithProducts() {
//     $categories = [];
    
//     $conn = $this->conn->getConnection();  

//     $query = "SELECT category_id, category_name FROM tbl_categories ORDER BY category_id ASC LIMIT 8";
//     $result = $conn->query($query);

//     if ($result && $result->num_rows > 0) {
//         while ($cat = $result->fetch_assoc()) {
//             $catId = $cat['category_id'];
//             $cat['products'] = $this->conn->getProductsByCategory($catId);
//             $categories[] = $cat;
//         }
//     }

//     return $categories;
// }



// public function getTotalProducts() {
//     $query = "SELECT COUNT(*) as total FROM tbl_products";
//     $result = mysqli_query($this->conn, $query);
//     $row = mysqli_fetch_assoc($result);
//     return $row['total'];
// }



//    public function updateProduct($id, $name, $description, $price, $isHotSale, $isActive,  $updated_by) {
//         $query = "UPDATE tbl_products 
//                   SET product_name=?, product_description=?, product_price=?, 
//                       isHotSale=?, isActive=?,   product_updated_by=?, product_updated_at=NOW() 
//                   WHERE product_id=?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("ssdiiii", $name, $description, $price, $isHotSale, $isActive,  $updated_by, $id);
//         return $stmt->execute();
//     }

 

// public function updateStatus($id, $status) {
//     $stmt = $this->conn->prepare("UPDATE tbl_products SET isActive = ? WHERE product_id = ?");
//     $stmt->bind_param("ii", $status, $id);
//     return $stmt->execute();
// }
// public function getProductsByCategoryDetails($category_id, $excludeId = null) {
//     if ($excludeId) {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ? AND product_id != ? LIMIT 5";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("ii", $category_id, $excludeId);
//     } else {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ? LIMIT 5";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $category_id);
//     }

//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

 


// public function getProductImages($product_id) {
//     $query = "SELECT image_path FROM tbl_category_images WHERE product_id = ? LIMIT 5";
//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $product_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

// public function getProductsByCategoryDetails($category_id, $excludeId = null) {
//     if ($excludeId) {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ? AND product_id != ? LIMIT 5";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("ii", $category_id, $excludeId);
//     } else {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ? LIMIT 5";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $category_id);
//     }

//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }
// public function getProductsByCategoryDetails($category_id, $excludeId = null) {
//     if ($excludeId) {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ? AND product_id != ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("ii", $category_id, $excludeId);
//     } else {
//         $query = "SELECT * FROM tbl_products WHERE category_id = ?";
//         $stmt = $this->conn->prepare($query);
//         $stmt->bind_param("i", $category_id);
//     }

//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }


// public function getProductImages($product_id) {
   
//     $query = "SELECT image_path FROM tbl_category_images WHERE product_id = ? LIMIT 5";

//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $product_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_all(MYSQLI_ASSOC);
// }

// public function addProduct($name, $price, $category_id) {
//     $stmt = $this->connection->prepare("INSERT INTO tbl_products (product_name, price, category_id) VALUES (?, ?, ?)");
//     $stmt->bind_param("sdi", $name, $price, $category_id);
//     $stmt->execute();

//     // ✅ Ye line important hai
//     return $this->connection->insert_id;
// }


// public function getProductImages($product_id) {
//     $query = "SELECT * FROM tbl_category_images WHERE product_id = ?";
//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $product_id);
//     $stmt->execute();
//     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
// }



// public function getProductById($id) {
//     $query = "SELECT * FROM tbl_products WHERE product_id = ?";
//     $stmt = $this->conn->prepare($query);
//     $stmt->bind_param("i", $id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_assoc();
// }


// // }
//  <?php
class ProductModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function createProduct($name, $description, $price, $isHotSale, $isActive, $category_id, $created_by) {
        $query = "INSERT INTO tbl_products 
                  (product_name, product_description, product_price, isHotSale, isActive, category_id, product_created_by, product_created_at)
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssdiiii", $name, $description, $price, $isHotSale, $isActive, $category_id, $created_by);

        if ($stmt->execute()) {
            return $this->conn->insert_id; // ✅ returns new product_id
        } else {
            return false;
        }
    }

    public function deactivateProduct($id) {
        $query = "UPDATE tbl_products SET isActive = 0 WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getProductById($id) {
        $query = "SELECT * FROM tbl_products WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getAllProducts($limit, $offset) {
        $query = "SELECT * FROM tbl_products ORDER BY product_id DESC LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Updated joins using product_id
    public function getHotSaleProducts() {
        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                p.isHotSale,
                c.category_name,
                ci.image_path
            FROM 
                tbl_products p
            JOIN 
                tbl_categories c ON p.category_id = c.category_id
            LEFT JOIN 
                tbl_category_images ci ON p.product_id = ci.product_id
            WHERE 
                p.isHotSale = 1
            GROUP BY 
                p.product_id
        ";
        return $this->conn->query($query);
    }

    public function getHotSaleByCategory($categoryId) {
        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                c.category_name,
                ci.image_path
            FROM 
                tbl_products p
            JOIN 
                tbl_categories c ON p.category_id = c.category_id
            LEFT JOIN 
                tbl_category_images ci ON p.product_id = ci.product_id
            WHERE 
                p.isHotSale = 1 AND p.category_id = ?
            GROUP BY 
                p.product_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getProductsByCategory($categoryId) {
        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                c.category_name,
                ci.image_path
            FROM 
                tbl_products p
            JOIN 
                tbl_categories c ON p.category_id = c.category_id
            LEFT JOIN 
                tbl_category_images ci ON p.product_id = ci.product_id
            WHERE 
                p.category_id = ?
            GROUP BY 
                p.product_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalProducts() {
        $query = "SELECT COUNT(*) as total FROM tbl_products";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function updateProduct($id, $name, $description, $price, $isHotSale, $isActive, $updated_by) {
        $query = "UPDATE tbl_products 
                  SET product_name=?, product_description=?, product_price=?, 
                      isHotSale=?, isActive=?, product_updated_by=?, product_updated_at=NOW() 
                  WHERE product_id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssdiiii", $name, $description, $price, $isHotSale, $isActive, $updated_by, $id);
        return $stmt->execute();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE tbl_products SET isActive = ? WHERE product_id = ?");
        $stmt->bind_param("ii", $status, $id);
        return $stmt->execute();
    }

    public function getProductsByCategoryDetails($category_id, $excludeId = null) {
        if ($excludeId) {
            $query = "SELECT * FROM tbl_products WHERE category_id = ? AND product_id != ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ii", $category_id, $excludeId);
        } else {
            $query = "SELECT * FROM tbl_products WHERE category_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $category_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getProductImages($product_id) {
        $query = "SELECT image_path FROM tbl_category_images WHERE product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>



 
 
