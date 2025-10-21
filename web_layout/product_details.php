<?php
include_once '../config/session_check.php';
include_once '../Controller/ProductController.php';

$productController = new ProductController($connection);

// ✅ Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

if (!isset($_GET['id'])) {
    echo "<h2>Product not found!</h2>";
    exit;
}

$id = intval($_GET['id']);
$product = $productController->getProductById($id);

if (!$product) {
    echo "<h2>No product details found!</h2>";
    exit;
}

// ✅ Product Images
$images = $productController->getProductImages($product['product_id']);

// ✅ Category & related products
$category_id = $product['category_id'];
$relatedProducts = $productController->getProductsByCategoryDetails($category_id, $id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_name']) ?> - Product Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .product-img-main {
            border-radius: 12px;
            width: 100%;
            max-height: 400px;
            object-fit: contain;
        }
        .thumbs img {
            width: 90px;
            height: 90px;
            border-radius: 10px;
            object-fit: contain;
            margin: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .thumbs img:hover {
            transform: scale(1.1);
        }
        .price-tag {
            color: #28a745;
            font-size: 24px;
            font-weight: 600;
        }
        .btn-cart {
            background: #ff6b00;
            color: white;
            border-radius: 25px;
            font-weight: 500;
            transition: 0.3s;
        }
        .btn-cart:hover {
            background: #e65a00;
        }
        .related-card {
            transition: all 0.3s ease;
        }
        .related-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
    </style>
    <?php include_once '../layout/header_links.php'; ?>
     <?php include_once '../web_layout/web_header.php'; ?>
    
</head>
<body>

<div class="container py-5">
    <div class="product-card">
        <div class="row align-items-center">
            <!-- Left: Product Images -->
            <div class="col-md-6 text-center">
                <?php if (!empty($images)): ?>
                    <img src="../<?= htmlspecialchars($images[0]['image_path']) ?>" 
                         alt="<?= htmlspecialchars($product['product_name']) ?>" 
                         class="product-img-main mb-3" id="mainImage">
                <?php else: ?>
                    <img src="../uploads/no-image.png" class="product-img-main mb-3" alt="No Image">
                <?php endif; ?>

                <!-- Thumbnail Images -->
                <div class="thumbs d-flex justify-content-center flex-wrap">
                    <?php foreach ($images as $img): ?>
                        <img src="../<?= htmlspecialchars($img['image_path']) ?>" 
                             onclick="document.getElementById('mainImage').src=this.src;">
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product['product_name']) ?></h2>
                <p class="text-muted"><?= htmlspecialchars($product['product_description']) ?></p>
                <p class="price-tag mb-3">$<?= htmlspecialchars($product['product_price']) ?></p>

                <?php if ($isLoggedIn): ?>
                    <a href="../Controller/CartController.php?action=add&id=<?= $product['product_id'] ?>" 
                       class="btn btn-cart px-4 py-2 bg-dark">
                       <i class="fa-solid fa-cart-plus me-2 "></i> Add to Cart
                    </a>
                <?php else: ?>
                    <a href="../Views/user_login.php" class="btn btn-cart px-4 py-2 bg-dark">
                        <i class="fa-solid fa-right-to-bracket me-2"></i> Login to Add to Cart
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="text-center mb-4">More from this Category</h3>
        <div class="row justify-content-center">
            <?php if (!empty($relatedProducts)): ?>
                <?php foreach ($relatedProducts as $item): 
                    $relatedImages = $productController->getProductImages($item['product_id']);
                ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="card related-card p-3 text-center">
                            <?php if (!empty($relatedImages)): ?>
                                <img src="../<?= htmlspecialchars($relatedImages[0]['image_path']) ?>" 
                                     class="img-fluid rounded mb-3" style="height:180px; object-fit:contain;">
                            <?php else: ?>
                                <img src="../uploads/no-image.png" 
                                     class="img-fluid rounded mb-3" style="height:180px; object-fit:contain;">
                            <?php endif; ?>
                            <h6><?= htmlspecialchars($item['product_name']) ?></h6>
                            <p class="text-success fw-bold">$<?= htmlspecialchars($item['product_price']) ?></p>
                            <a href="product_details.php?id=<?= $item['product_id'] ?>" class="btn btn-outline-dark btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-muted">No related products found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
 <?php include_once '../web_layout/web_footer.php'; ?>
    <?php include_once '../layout/footer_links.php'; ?>

</body>
</html>
