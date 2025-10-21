<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/user_login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
<title>Your Cart</title>
<style>
body { font-family: Arial; background: #f5f5f5; }
.container { width: 80%; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; }
table { width: 100%; border-collapse: collapse; }
th, td { border-bottom: 1px solid #ddd; padding: 10px; text-align: center; }
button { background: #28a745; color: white; border: none; padding: 10px 15px; border-radius: 5px; cursor: pointer; }
button:hover { background: #218838; }
</style>
 <?php include_once '../web_layout/web_header.php'; ?>
    <?php include_once '../layout/header_links.php'; ?>
</head>
<body>
<div class="container">
    <h2>Your Shopping Cart</h2>

    <?php if (empty($cart)): ?>
        <p>Your cart is empty!</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($cart as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td>$<?= number_format($subtotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h3>Total: $<?= number_format($total, 2) ?></h3>

        <form method="POST" action="checkout.php">
            <button type="submit" name="checkout">Proceed to Checkout</button>
        </form>
    <?php endif; ?>
</div>
 <?php include_once '../web_layout/web_footer.php'; ?>
    <?php include_once '../layout/footer_links.php'; ?>
</body>
</html>
