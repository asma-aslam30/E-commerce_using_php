<?php
include_once '../config/session_check.php';
include_once '../config/connection.php';
include_once '../Models/CategoryModel.php';

$categoryModel = new CategoryModel($connection);
$categories = $categoryModel->getAllCategories();

// Initialize variables
$product_nameErr = $product_descriptionErr = $product_priceErr = $categoryErr = $imagesErr = "";
$product_name = $product_description = $product_price = $category_id = "";
$isHotSale = $isActive = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $valid = true;

    // Product Name
    if (empty($_POST['product_name'])) {
        $product_nameErr = "*Product Name is required";
        $valid = false;
    } else {
        $product_name = trim($_POST['product_name']);
    }

    // Product Description
    if (empty($_POST['product_description'])) {
        $product_descriptionErr = "*Product Description is required";
        $valid = false;
    } else {
        $product_description = trim($_POST['product_description']);
    }

    // Product Price
    if (empty($_POST['product_price'])) {
        $product_priceErr = "*Product Price is required";
        $valid = false;
    } elseif (!is_numeric($_POST['product_price']) || $_POST['product_price'] <= 0) {
        $product_priceErr = "*Enter a valid positive number";
        $valid = false;
    } else {
        $product_price = floatval($_POST['product_price']);
    }

    // Category
    if (empty($_POST['category_id'])) {
        $categoryErr = "*Please select a category";
        $valid = false;
    } else {
        $category_id = intval($_POST['category_id']);
    }

    // Checkboxes
    $isHotSale = isset($_POST['isHotSale']) ? 1 : 0;
    $isActive = isset($_POST['isActive']) ? 1 : 0;

    // Images
    if (!isset($_FILES['images'])) {
        $imagesErr = "*Please upload 5 images";
        $valid = false;
    } else {
        $files = $_FILES['images'];
        if (count($files['name']) != 5) {
            $imagesErr = "*Please upload exactly 5 images";
            $valid = false;
        }
    }

    // If everything is valid, call controller
    if ($valid) {
        include_once '../Controller/ProductController.php';
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>Add Product</title>
<?php include('../layout/header_links.php'); ?>
</head>
<body id="page-top">
<div id="wrapper">
<?php include('../layout/sidebar.php'); ?>
<div id="content-wrapper" class="d-flex flex-column">
<div id="content">
<?php include('../layout/header.php'); ?>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h3 class="text-center mb-4">Add New Product</h3>

        <form method="POST" enctype="multipart/form-data">

            <!-- Product Name -->
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product_name) ?>">
                <small class="text-danger"><?= $product_nameErr ?></small>
            </div>

            <!-- Product Description -->
            <div class="form-group">
                <label>Product Description</label>
                <textarea name="product_description" class="form-control" rows="3"><?= htmlspecialchars($product_description) ?></textarea>
                <small class="text-danger"><?= $product_descriptionErr ?></small>
            </div>

            <!-- Product Price -->
            <div class="form-group">
                <label>Product Price (PKR)</label>
                <input type="number" name="product_price" step="0.01" class="form-control" value="<?= htmlspecialchars($product_price) ?>">
                <small class="text-danger"><?= $product_priceErr ?></small>
            </div>

            <!-- Category -->
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['category_id'] ?>" <?= ($category_id == $category['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-danger"><?= $categoryErr ?></small>
            </div>

            <!-- Checkboxes -->
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="isHotSale" id="hotSale" <?= $isHotSale ? 'checked' : '' ?>>
                <label class="form-check-label" for="hotSale">Hot Sale</label>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="isActive" id="active" <?= $isActive ? 'checked' : '' ?>>
                <label class="form-check-label" for="active">Active</label>
            </div>

            <!-- Multiple Images (exactly 5) -->
            <div class="form-group">
                <label>Upload 5 Images</label>
                <input type="file" name="images[]" class="form-control-file" accept=".jpg,.jpeg,.png,.webp" multiple>
                <small class="text-danger"><?= $imagesErr ?></small>
            </div>

            <input type="hidden" name="product_created_by" value="1">

            <button type="submit" name="add_product" class="btn btn-primary btn-block">Add Product</button>
        </form>
    </div>
</div>

<?php include('../layout/footer.php'); ?>
<?php include('../layout/footer_links.php'); ?>

</div>
</div>
</body>
</html>
