<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/ProductModel.php';
include_once '../Controller/ProductController.php';

// check ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid Product ID!'); window.location.href='Products.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$productModel = new ProductModel($connection);
// $productImages = $productModel->getProductImages($id);
$product = $productModel->getProductById($id);

if (!$product) {
    echo "<script>alert('Product not found!'); window.location.href='Products.php';</script>";
    exit;
}

// Initialize error variables
$nameErr = $descErr = $priceErr = $attachmentErr = "";
$product_name = $product['product_name'];
$product_description = $product['product_description'];
$product_price = $product['product_price'];
$isHotSale = $product['isHotSale'];
$isActive = $product['isActive'];


$controller = new ProductController($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid = true;

    // Product Name
    if (empty($_POST['product_name'])) {
        $nameErr = "*Product Name is required";
        $valid = false;
    } else {
        $product_name = trim($_POST['product_name']);
    }

    // Product Description
    if (empty($_POST['product_description'])) {
        $descErr = "*Product Description is required";
        $valid = false;
    } else {
        $product_description = trim($_POST['product_description']);
    }

    // Product Price
    if (empty($_POST['product_price'])) {
        $priceErr = "*Product Price is required";
        $valid = false;
    } elseif (!is_numeric($_POST['product_price']) || $_POST['product_price'] <= 0) {
        $priceErr = "*Enter a valid positive number";
        $valid = false;
    } else {
        $product_price = floatval($_POST['product_price']);
    }

    // Checkboxes
    $isHotSale = isset($_POST['isHotSale']) ? 1 : 0;
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    // Attachment validation (optional)
    if (!empty($_FILES['attachment']['name'])) {
        $allowed = ['jpg','jpeg','png'];
        $ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), $allowed)) {
            $attachmentErr = "*Invalid file type. Only jpg, jpeg, png allowed.";
            $valid = false;
        }
    }



    
    // If all valid, update product
    if ($valid) {
        $controller->updateProduct($_POST, $_FILES);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Product</title>
    <?php include('../layout/header_links.php'); ?>
</head>
<body>
<div id="wrapper">
    <?php include('../layout/sidebar.php'); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include('../layout/header.php'); ?>
            <div class="container mt-4">
                <h4>Edit Product</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['product_id']; ?>">

                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product_name); ?>">
                        <small class="text-danger"><?= $nameErr ?></small>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="product_description" class="form-control"><?= htmlspecialchars($product_description); ?></textarea>
                        <small class="text-danger"><?= $descErr ?></small>
                    </div>

                    <div class="form-group">
                        <label>Price (PKR)</label>
                        <input type="number" name="product_price" class="form-control" value="<?= htmlspecialchars($product_price); ?>">
                        <small class="text-danger"><?= $priceErr ?></small>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="isHotSale" <?= $isHotSale ? 'checked' : ''; ?>> Hot Sale</label>
                    </div>

                    <div class="form-group">
                        <label><input type="checkbox" name="isActive" <?= $isActive ? 'checked' : ''; ?>> Active</label>
                    </div>

                    <!-- <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="attachment" class="form-control">
                        <small class="text-danger"><?= $attachmentErr ?></small>
                        <?php if (!empty($product['attachment'])): ?>
                            <p class="mt-2">
                                Current: 
                                <a href="../uploads/<?= $product['product_id']; ?>/<?= $product['attachment']; ?>" target="_blank"><?= $product['attachment']; ?></a>
                            </p>
                        <?php endif; ?>
                    </div> -->

                    <div class="form-group">
  <!-- <label>Existing Product Images</label>
  <div class="d-flex flex-wrap">
    <?php foreach ($productImages as $img): ?>
      <div class="m-2 text-center">
        <img src="../uploads/<?= $id ?>/<?= $img['image_name']; ?>" width="100" height="100" class="border rounded">
        <br>
        <input type="checkbox" name="keep_images[]" value="<?= $img['image_name']; ?>" checked> Keep
      </div>
    <?php endforeach; ?>
  </div> -->
</div>

<div class="form-group mt-3">
  <label>Upload New Images (optional)</label>
  <input type="file" name="attachments[]" class="form-control" multiple>
</div>


                    <button type="submit" class="btn btn-success">Update Product</button>
                    <a href="Products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
        <?php include('../layout/footer.php'); ?>
        <?php include('../layout/footer_links.php'); ?>
    </div>
</div>
</body>
</html>
