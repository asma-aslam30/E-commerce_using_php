<?php
session_start();
include '../config/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

//  User login check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

//  Fetch order items
$query = "SELECT oi.*, p.product_name, p.product_price
          FROM tbl_order_items oi
          JOIN tbl_order o ON oi.order_id = o.order_id
          JOIN tbl_products p ON oi.product_id = p.product_id
          WHERE o.user_id = ? AND o.status = 'Pending'";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$items = $stmt->get_result();

if ($items->num_rows == 0) {
    echo "<script>alert('Your cart is empty!'); window.location.href='cart.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://www.paypal.com/sdk/js?client-id=AT8RSepAgf1gElnBaopMC0reeyi5yyBsIbryIdQG1yAB2lxx_M0OLGncjEn3V4Z_i-V0hDqiND007VUa&currency=EUR"></script>

    <style>
        body { background-color: #f8f9fa; }
        .error { color: red; font-size: 13px; }
    </style>
</head>
<body>

<?php include_once '../web_layout/web_header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h2 class="text-center text-primary fw-bold mb-4">Checkout</h2>

                    <!--  Order Summary -->
                    <div class="table-responsive mb-4">
                        <table class="table align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grandTotal = 0;
                                while ($row = $items->fetch_assoc()):
                                    $sub = $row['product_price'] * $row['quantity'];
                                    $grandTotal += $sub;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td>$<?= number_format($row['product_price'], 2) ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td class="text-success fw-bold">$<?= number_format($sub, 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-5">Total:</td>
                                    <td class="text-success fw-bold fs-5">$<?= number_format($grandTotal, 2) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!--  Checkout Form -->
                    <form id="checkoutForm" action="place_order.php" method="POST" novalidate>
                        <h4 class="mb-3 text-primary fw-semibold">Shipping Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control">
                                <span class="error" id="nameErr"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="checkout_email" class="form-control">
                                <span class="error" id="emailErr"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="contact_number" class="form-control">
                                <span class="error" id="phoneErr"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control">
                                <span class="error" id="cityErr"></span>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Shipping Address</label>
                                <input type="text" name="shipping_address" class="form-control">
                                <span class="error" id="addressErr"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Province</label>
                                <input type="text" name="province" class="form-control">
                                <span class="error" id="provinceErr"></span>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Postal / Zip Code</label>
                                <input type="text" name="postal_code" class="form-control">
                                <span class="error" id="zipErr"></span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!--  Payment Method -->
                        <h4 class="mb-3 text-primary fw-semibold">Select Payment Method</h4>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                            <label class="form-check-label" for="cod">💵 Cash on Delivery</label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="PayPal">
                            <label class="form-check-label" for="paypal">💳 PayPal</label>
                        </div>

                        <div id="paypal-button-container" class="mt-3" style="display:none;"></div>
                        <button type="submit" id="submitBtn" class="btn btn-primary w-100">Place Order</button>
                    </form>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <a href="cart.php" class="btn btn-outline-secondary">← Back to Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../web_layout/web_footer.php'; ?>

<!-- ✅ JavaScript Frontend Validation -->
<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    let valid = true;
    document.querySelectorAll('.error').forEach(el => el.textContent = "");

    const name = document.querySelector('[name="full_name"]').value.trim();
    const email = document.querySelector('[name="checkout_email"]').value.trim();
    const phone = document.querySelector('[name="contact_number"]').value.trim();
    const city = document.querySelector('[name="city"]').value.trim();
    const address = document.querySelector('[name="shipping_address"]').value.trim();
    const province = document.querySelector('[name="province"]').value.trim();
    const zip = document.querySelector('[name="postal_code"]').value.trim();

    if (name === "") { document.getElementById('nameErr').textContent = "*Full name is required"; valid = false; }
    if (email === "") { document.getElementById('emailErr').textContent = "*Email is required"; valid = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        document.getElementById('emailErr').textContent = "*Invalid email format"; valid = false;
    }
    if (phone === "") { document.getElementById('phoneErr').textContent = "*Phone number is required"; valid = false; }
    else if (!/^\d{11}$/.test(phone)) {
        document.getElementById('phoneErr').textContent = "*Enter 11 digits only"; valid = false;
    }
    if (city === "") { document.getElementById('cityErr').textContent = "*City is required"; valid = false; }
    if (address === "") { document.getElementById('addressErr').textContent = "*Address is required"; valid = false; }
    if (province === "") { document.getElementById('provinceErr').textContent = "*Province is required"; valid = false; }
    if (zip === "") { document.getElementById('zipErr').textContent = "*Zip code is required"; valid = false; }

    if (!valid) e.preventDefault();
});

// ✅ PayPal / COD Toggle
document.addEventListener("DOMContentLoaded", () => {
    const codRadio = document.getElementById("cod");
    const paypalRadio = document.getElementById("paypal");
    const submitBtn = document.getElementById("submitBtn");
    const paypalContainer = document.getElementById("paypal-button-container");
    const form = document.getElementById("checkoutForm");

    paypalRadio.addEventListener("change", () => {
        paypalContainer.style.display = "block";
        submitBtn.style.display = "none";
    });
    codRadio.addEventListener("change", () => {
        paypalContainer.style.display = "none";
        submitBtn.style.display = "block";
    });

    paypal.Buttons({
        createOrder: function(data, actions) {
            const total = <?= $grandTotal ?>;
            return actions.order.create({
                purchase_units: [{
                    amount: { value: total.toFixed(2), currency_code: 'USD' }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                alert(' Payment completed by ' + details.payer.name.given_name);
                const paypalField = document.createElement('input');
                paypalField.type = 'hidden';
                paypalField.name = 'paypal_order_id';
                paypalField.value = data.orderID;
                form.appendChild(paypalField);
                form.submit();
            });
        },
        onError: function(err) {
            alert('❌ Payment failed. Try again.');
            console.error(err);
        }
    }).render('#paypal-button-container');
});
</script>

</body>
</html>
