<?php
session_start();
include '../config/connection.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ---------- CONFIG ----------
define('PAYPAL_CLIENT_ID', 'AXJRg47uqcWc9x-KSQYRj6t5e9g4eASYQ4sFMUz18vcTslQHhmqNyA1GBdH8UH-9TfWD0st5uySphdmR');
define('PAYPAL_SECRET', 'EIRSj8hnzrgD6uazlDDyUFU5FbDlWZNDB3T6m-qhHM1lFlBs2BMRhqhw6uhdXHxadYFu_FX4pHKc04IB');
define('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com'); // ✅ Correct endpoint

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'aslamasma486@gmail.com');
define('SMTP_PASS', 'btaw dgjo dsjq wpmh');
define('SMTP_PORT', 587);
define('SMTP_FROM_EMAIL', 'aslamasma486@gmail.com');
define('SMTP_FROM_NAME', 'Smart Stores');

// ---------- CHECK LOGIN ----------
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/user_login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// ---------- PROFILE INFO ----------
$full_name = $_POST['full_name'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';
$paypal_order_id = $_POST['paypal_order_id'] ?? null;

// ---------- GET/UPDATE PROFILE ----------
$checkProfile = $connection->prepare("SELECT user_info_id FROM tbl_profile_info WHERE user_id=?");
$checkProfile->bind_param("i", $user_id);
$checkProfile->execute();
$resProfile = $checkProfile->get_result();

if ($resProfile->num_rows > 0) {
    $profile = $resProfile->fetch_assoc();
    $user_info_id = $profile['user_info_id'];

    $updateProfile = $connection->prepare("UPDATE tbl_profile_info SET full_name=?, contact_number=?, shipping_address=?, city=?, province=?, postal_code=?, updated_at=NOW() WHERE user_info_id=?");
    $updateProfile->bind_param("ssssssi", $full_name, $contact_number, $shipping_address, $city, $province, $postal_code, $user_info_id);
    $updateProfile->execute();
} else {
    $insertProfile = $connection->prepare("INSERT INTO tbl_profile_info (user_id, full_name, contact_number, shipping_address, city, province, postal_code) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertProfile->bind_param("issssss", $user_id, $full_name, $contact_number, $shipping_address, $city, $province, $postal_code);
    $insertProfile->execute();
    $user_info_id = $connection->insert_id;
}

// ---------- FETCH ORDER ----------
$orderQuery = "SELECT order_id FROM tbl_order WHERE user_id=? AND status='Pending' LIMIT 1";
$stmtOrder = $connection->prepare($orderQuery);
$stmtOrder->bind_param("i", $user_id);
$stmtOrder->execute();
$resOrder = $stmtOrder->get_result();

if ($resOrder->num_rows == 0) {
    echo "<script>alert('No pending order found!'); window.location='cart.php';</script>";
    exit;
}
$order = $resOrder->fetch_assoc();
$order_id = (int)$order['order_id'];

// ---------- FETCH ORDER ITEMS ----------
$items_q = "SELECT oi.order_item_id, oi.quantity, p.product_name, p.product_price
            FROM tbl_order_items oi
            JOIN tbl_products p ON oi.product_id = p.product_id
            WHERE oi.order_id = ?";
$stmt_items = $connection->prepare($items_q);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$res_items = $stmt_items->get_result();

$grandTotal = 0;
$order_items_html = '<table style="width:100%; border-collapse: collapse;">';
$order_items_html .= '<tr><th>Product</th><th>Qty</th><th>Price</th></tr>';
while ($it = $res_items->fetch_assoc()) {
    $sub = $it['product_price'] * $it['quantity'];
    $grandTotal += $sub;
    $order_items_html .= "<tr>
        <td>{$it['product_name']}</td>
        <td style='text-align:center;'>{$it['quantity']}</td>
        <td style='text-align:right;'>$" . number_format($sub, 2) . "</td>
    </tr>";
}
$order_items_html .= "</table>";

// ---------- FUNCTION: Get PayPal Access Token ----------
function getPaypalAccessToken()
{
    if (isset($_SESSION['paypal_token']) && time() < $_SESSION['paypal_token_expiry']) {
        return $_SESSION['paypal_token'];
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v1/oauth2/token");

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resp = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($resp, true);
    $accessToken = $tokenData['access_token'] ?? null;

    if ($accessToken) {
        $_SESSION['paypal_token'] = $accessToken;
        $_SESSION['paypal_token_expiry'] = time() + 36000;  
    }

    return $accessToken;
}

// ---------- PAYPAL VERIFICATION ----------
$payment_status = 'Pending';
$paypal_verified = false;

if ($payment_method === 'PayPal' && $paypal_order_id) {
    $accessToken = getPaypalAccessToken();

    if (!$accessToken) {
        die("Unable to get PayPal token (Session may have expired). Please retry.");
    }

    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, PAYPAL_BASE . "/v2/checkout/orders/{$paypal_order_id}");
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$accessToken}"
    ]);
    $resp2 = curl_exec($ch2);
    curl_close($ch2);

    $orderDetails = json_decode($resp2, true);

    if (isset($orderDetails['status']) && $orderDetails['status'] === 'COMPLETED') {
        $capturedAmount = $orderDetails['purchase_units'][0]['amount']['value'] ?? 0;
        if (number_format($capturedAmount, 2) === number_format($grandTotal, 2)) {
            $payment_status = 'Paid';
            $paypal_verified = true;
        }
    } else {
        // Token might have expired -> Retry once
        $accessToken = getPaypalAccessToken();
        if ($accessToken) {
            $ch3 = curl_init();
            curl_setopt($ch3, CURLOPT_URL, PAYPAL_BASE . "/v2/checkout/orders/{$paypal_order_id}");
            curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch3, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization: Bearer {$accessToken}"
            ]);
            $resp3 = curl_exec($ch3);
            curl_close($ch3);

            $orderDetails = json_decode($resp3, true);
            if (isset($orderDetails['status']) && $orderDetails['status'] === 'COMPLETED') {
                $capturedAmount = $orderDetails['purchase_units'][0]['amount']['value'] ?? 0;
                if (number_format($capturedAmount, 2) === number_format($grandTotal, 2)) {
                    $payment_status = 'Paid';
                    $paypal_verified = true;
                }
            }
        }
    }
}
echo $paypal_order_id;
// ---------- UPDATE ORDER ----------
$update_order = "UPDATE tbl_order SET user_info_id=?, total_amount=?, payment_method=?, payment_status=?, status='Completed', updated_at=NOW() WHERE order_id=?";
$stmt_up = $connection->prepare($update_order);
$stmt_up->bind_param("idssi", $user_info_id, $grandTotal, $payment_method, $payment_status, $order_id);
$stmt_up->execute();

// ---------- EMAIL CONFIRMATION ----------
$getEmail = $connection->prepare("SELECT email FROM tbl_registration WHERE id=? LIMIT 1");
$getEmail->bind_param("i", $user_id);
$getEmail->execute();
$resEmail = $getEmail->get_result();
$user_email = '';
if ($rowE = $resEmail->fetch_assoc()) $user_email = $rowE['email'];

if ($user_email) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($user_email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Order #ORD-2345{$order_id}";
        $body = "<h2>Thank you for your order, {$full_name}!</h2>";
        $body .= "<p>Your order is being processed. You’ll receive an update soon!</p>";
        $body .= "<p><strong>Shipping Address:</strong><br>{$shipping_address}, {$city}, {$province} - {$postal_code}</p>";
        $body .= "<p><strong>Contact:</strong> {$contact_number}</p>";
        $body .= "<h4>Order Details</h4>";
        $body .= $order_items_html;
        $body .= "<p><strong>Total:</strong> $" . number_format($grandTotal, 2) . "</p>";
        $body .= "<p><strong>Payment Method:</strong> {$payment_method} - <em>{$payment_status}</em></p>";
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
    }
}

// ---------- REDIRECT ----------
if ($payment_method === 'PayPal' && !$paypal_verified) {
    echo "<script>alert('Your PayPal session expired or payment not verified. Please try again.'); window.location='cart.php';</script>";
} else {
    echo "<script>alert('Order placed successfully! Confirmation email sent.'); window.location='order_success.php';</script>";
}
?>
 