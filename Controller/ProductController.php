<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/ProductModel.php';
include_once '../Models/CategoryImageModel.php';

class ProductController {
    public $productModel;
    private $imageModel;

    public function __construct($connection) {
        $this->productModel = new ProductModel($connection);
        $this->imageModel = new CategoryImageModel($connection);
    }

    // ------------------- Add Product -------------------
    public function addProduct() {
        if (isset($_POST['add_product'])) {
            $name = trim($_POST['product_name']);
            $description = trim($_POST['product_description']);
            $price = (float)$_POST['product_price'];
            $isHotSale = isset($_POST['isHotSale']) ? 1 : 0;
            $isActive = isset($_POST['isActive']) ? 1 : 0;
            $category_id = (int)$_POST['category_id'];
            // $created_by = $_SESSION['user_id'] ?? 'Admin';
            // $user_id = $_SESSION['user_id'] ?? '1';

            $created_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
            $user_id = $created_by;


            // 1️⃣ Insert Product
            $success = $this->productModel->createProduct($name, $description, $price, $isHotSale, $isActive, $category_id, $created_by);

            if ($success) {
                // 2️⃣ Get last inserted product_id
                $conn = $this->productModel->getConnection();
                $product_id = $conn->insert_id;

                // 3️⃣ Handle Images Upload
                if (!empty($_FILES['images']['name'][0])) {
                    $uploadDir = "../uploads/product_$product_id/";
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                        $fileName = basename($_FILES['images']['name'][$key]);
                        $targetPath = $uploadDir . $fileName;

                        if (move_uploaded_file($tmpName, $targetPath)) {
                            $relativePath = str_replace("../", "", $targetPath);

                            // 4️⃣ Insert each image with correct product_id
                            $this->imageModel->addCategoryImages($product_id, $relativePath);
                        }
                    }
                }

                // 5️⃣ Success message & redirect
                echo "<script>alert('Product added successfully!'); window.location.href='../Views/products.php';</script>";
                exit();
            } else {
                echo "<script>alert('Failed to add product!'); window.history.back();</script>";
                exit();
            }
        }
    }

    // ------------------- Other Methods -------------------
    public function showHotSaleProducts() {
        return $this->productModel->getHotSaleProducts();
    }

    public function showHotSaleByCategory($categoryId) {
        return $this->productModel->getHotSaleByCategory($categoryId);
    }

    // public function getAllCategoriesWithProducts() {
    //     $categories = [];
    //     $query = "SELECT c.category_id, c.category_name 
    //               FROM tbl_categories c
    //               ORDER BY c.category_id ASC LIMIT 8";  

    //     $conn = $this->productModel->getConnection();
    //     $result = $conn->query($query);

    //     if ($result->num_rows > 0) {
    //         while ($cat = $result->fetch_assoc()) {
    //             $catId = $cat['category_id'];
    //             $cat['products'] = $this->productModel->getProductsByCategory($catId);
    //             $categories[] = $cat;
    //         }
    //     }
    //     return $categories;
    // }

public function getAllCategoriesWithProducts() {
    $categories = [];
    $conn = $this->productModel->getConnection(); // ✅ existing connection use karo

    $categoryQuery = "SELECT * FROM tbl_categories";
    $categoryResult = $conn->query($categoryQuery);

    if ($categoryResult && $categoryResult->num_rows > 0) {
        while ($category = $categoryResult->fetch_assoc()) {
            $categoryId = $category['category_id'];

             
            $productQuery = "SELECT * FROM tbl_products WHERE category_id = ?";
            $stmt = $conn->prepare($productQuery);
            $stmt->bind_param("i", $categoryId);
            $stmt->execute();
            $productResult = $stmt->get_result();

            $products = [];
            while ($product = $productResult->fetch_assoc()) {
                $productId = $product['product_id'];

                
                $imageQuery = "SELECT image_path FROM tbl_category_images WHERE product_id = ?";
                $imgStmt = $conn->prepare($imageQuery);
                $imgStmt->bind_param("i", $productId);
                $imgStmt->execute();
                $imgResult = $imgStmt->get_result();

                if ($imgRow = $imgResult->fetch_assoc()) {
                    $product['image_path'] = $imgRow['image_path'];
                }  

                $products[] = $product;
            }

            $category['products'] = $products;
            $categories[] = $category;
        }
    }

    return $categories;
}
       



    public function updateProduct($postData, $fileData) {
        $id = intval($postData['category_id']);
        $name = $postData['product_name'];
        $description = $postData['product_description'];
        $price = $postData['product_price'];
        $isHotSale = isset($postData['isHotSale']) ? 1 : 0;
        $isActive = isset($postData['isActive']) ? 1 : 0;
        // $updated_by = $_SESSION['user_id'] ?? '1';

        $updated_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
          

        $product = $this->productModel->getProductById($id);
        $attachment = $product['attachment'] ?? null;

        if (!empty($fileData['attachment']['name'])) {
            $filename = basename($fileData['attachment']['name']);
            $targetDir = "../uploads/$id/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($fileData['attachment']['tmp_name'], $targetFile)) {
                $attachment = $filename;
            }
        }
        $success = $this->productModel->updateProduct($id, $name, $description, $price, $isHotSale, $isActive, $attachment, $updated_by);

        if ($success) {
            echo "<script>alert('Product updated successfully!'); window.location.href='../Views/Products.php';</script>";
        } else {
            echo "<script>alert('Failed to update product!'); window.history.back();</script>";
        }
    }

//     $updated_by = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
// // $this->productModel->updateProduct($id, $name, $description, $price, $isHotSale, $isActive, $category_id, $updated_by);


    public function changeStatus($id, $status) {
        $success = $this->productModel->updateStatus($id, $status);
        if ($success) {
            echo "<script>alert('Product status updated successfully!'); window.location.href='../Views/Products.php';</script>";
        } else {
            echo "<script>alert('Failed to update product status!'); window.history.back();</script>";
        }
    }

    public function getProductsByCategoryDetails($category_id, $excludeId = null) {
        return $this->productModel->getProductsByCategoryDetails($category_id, $excludeId);
    }

    public function getProductImages($product_id) {
        return $this->productModel->getProductImages($product_id);
    }

    public function getProductById($id) {
        return $this->productModel->getProductById($id);
    }
}
// ------------------- Controller Triggers -------------------
$controller = new ProductController($connection);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action == 'deactivate') {
        $controller->changeStatus($id, 0);
    } elseif ($action == 'activate') {
        $controller->changeStatus($id, 1);
    }
}

if (isset($_POST['add_product'])) {
    $controller->addProduct();
}

if (isset($_POST['update_product'])) {
    $controller->updateProduct($_POST, $_FILES);
}
?>

 