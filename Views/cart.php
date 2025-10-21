<?php
session_start();
include_once '../config/connection.php';
include_once '../Models/CartModel.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../Views/user_login.php");
    exit;
}

$cartModel = new CartModel($connection);
$user_id = (int)$_SESSION['user_id'];
$items = $cartModel->getCartItems($user_id);

$grandTotal = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
     <?php include_once '../web_layout/web_header.php'; ?>
    <?php include_once '../layout/header_links.php'; ?>
</head>
<body style="background-color: #f8f9fa;">

<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h2 class="text-center mb-4 fw-bold text-primary"> My Shopping Cart</h2>

            <div class="table-responsive">
                <table class="table align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $items->fetch_assoc()): 
                        $subtotal = $row['price'] * $row['quantity'];
                        $grandTotal += $subtotal;
                    ?>
                        <tr>
                            <td class="fw-semibold"><?= htmlspecialchars($row['product_name']) ?></td>
                            <td>
                                <img src="../<?= htmlspecialchars($row['image_path'] ?? 'uploads/no-image.png') ?>" 
                                     alt="Product Image" 
                                     class="rounded shadow-sm" 
                                     style="width: 80px; height: 70px; object-fit: cover;">
                            </td>
                            <td class="text-success fw-semibold">$<?= number_format($row['price'],2) ?></td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a href="../Controller/CartController.php?action=dec&id=<?= $row['order_item_id'] ?>" class="btn btn-outline-dark btn-sm px-2">−</a>
                                    <span class="fw-bold"><?= $row['quantity'] ?></span>
                                    <a href="../Controller/CartController.php?action=inc&id=<?= $row['order_item_id'] ?>" class="btn btn-outline-dark btn-sm px-2">+</a>
                                </div>
                            </td>
                            <td class="fw-bold">$<?= number_format($subtotal,2) ?></td>
                            <td>
                                <a href="../Controller/CartController.php?action=remove&id=<?= $row['order_item_id'] ?>" 
                                   class="btn btn-danger btn-sm"> Remove</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold fs-5">Grand Total:</td>
                            <td colspan="2" class="text-success fw-bold fs-5">$<?= number_format($grandTotal,2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="ecommerce.php" class="btn btn-outline-secondary">← Continue Shopping</a>
                <a href="checkout.php" class="btn btn-primary btn-lg text-white fw-bold px-4">Proceed to Checkout →</a>
            </div>
        </div>
    </div>
</div>
 <?php include_once '../web_layout/web_footer.php'; ?>
    <?php include_once '../layout/footer_links.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
