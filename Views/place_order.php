 <?php
session_start();
include '../config/connection.php';



require '../vendor/phpmailer/phpmailer/src/Exception.php';
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

<<<<<<< HEAD
=======
// ---------- CONFIG ----------
define('PAYPAL_CLIENT_ID', 'AXbmCprgHhbefFilx3Oy-8KocEGMBhNqOj01iirOVY1hdbpNG9ZcGmmi_Cw7AmeKHl7yA6veLp26SCSF');
define('PAYPAL_SECRET', 'EPXDFfZyWJ1jN6VtXtA2xCTKAlKHx_tuZI3EF_6ZfseJT95GPt7KUYaa8FjuBO4GVQ0bvyAYp37u5qo2');
define('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com'); // ✅ Correct endpoint
>>>>>>> 9050adbac01ff0aee4a36042e3750379e284efef

// ---------- CONFIG ----------
define('PAYPAL_CLIENT_ID', 'AXbmCprgHhbefFilx3Oy-8KocEGMBhNqOj01iirOVY1hdbpNG9ZcGmmi_Cw7AmeKHl7yA6veLp26SCSF');
define('PAYPAL_SECRET', 'EPXDFfZyWJ1jN6VtXtA2xCTKAlKHx_tuZI3EF_6ZfseJT95GPt7KUYaa8FjuBO4GVQ0bvyAYp37u5qo2');

define('PAYPAL_BASE', 'https://api-m.sandbox.paypal.com');

// Email Config
define('SMTP_USER', 'aslamasma486@gmail.com');
define('SMTP_PASS', 'btaw dgjo dsjq wpmh');

// ---------- CHECK LOGIN ----------
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/user_login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// ---------- GET FORM DATA ----------
$full_name = $_POST['full_name'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';
$paypal_order_id = $_POST['paypal_order_id'] ?? '';

// ---------- FETCH ORDER ----------
$order_q = $connection->prepare("SELECT order_id FROM tbl_order WHERE user_id=? AND status='Pending' LIMIT 1");
$order_q->bind_param("i", $user_id);
$order_q->execute();
$order_r = $order_q->get_result();

if ($order_r->num_rows == 0) {
    echo "<script>alert('No pending order found!'); window.location='cart.php';</script>";
    exit;
}
$order_id = $order_r->fetch_assoc()['order_id'];

// ---------- FETCH ITEMS ----------
$items_q = $connection->prepare("SELECT oi.quantity, p.product_name, p.product_price 
                                FROM tbl_order_items oi 
                                JOIN tbl_products p ON oi.product_id = p.product_id 
                                WHERE oi.order_id=?");
$items_q->bind_param("i", $order_id);
$items_q->execute();
$res_items = $items_q->get_result();

$grandTotal = 0;
while ($item = $res_items->fetch_assoc()) {
    $grandTotal += $item['product_price'] * $item['quantity'];
}

// ---------- PAYPAL VERIFICATION ----------
$payment_status = 'Pending';
if ($payment_method == 'PayPal' && !empty($paypal_order_id)) {

    // Get Access Token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PAYPAL_BASE . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: application/json",
        "Accept-Language: en_US"
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ✅ add this for localhost

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }

    $access_token = json_decode($response, true);
    curl_close($ch);
    
    if ($access_token && isset($access_token['access_token']) && !empty($access_token['access_token'])) {
        $access =$access_token['access_token'];
       $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, PAYPAL_BASE . "/v2/checkout/orders/" . $paypal_order_id);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $access",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // for localhost only

        $resp2 = curl_exec($ch2);

        if (curl_errno($ch2)) {
            die("cURL Error (Order): " . curl_error($ch2));
        }
        
        $orderData = json_decode($resp2, true);
        curl_close($ch2);
        if (isset($orderData['status']) && $orderData['status'] == 'COMPLETED') {
            $payment_status = 'Paid';
        } else {
            echo "<script>alert('PayPal payment not verified!'); window.location='cart.php';</script>";
            exit;
        }
       

    } else {
        echo "<script>alert('Failed to get PayPal access token!'); window.location='cart.php';</script>";
        exit;
    }
} else {
    $payment_status = 'COD - Unpaid';
}

// ---------- UPDATE ORDER ----------
$status = ($payment_status == 'Paid') ? 'Completed' : 'Pending';
$update = $connection->prepare("UPDATE tbl_order SET total_amount=?, payment_method=?, payment_status=?, status=?, updated_at=NOW() WHERE order_id=?");
$update->bind_param("dsssi", $grandTotal, $payment_method, $payment_status, $status, $order_id);
$update->execute();

// ---------- EMAIL ----------
// ---------- EMAIL ----------
$u = $connection->prepare("SELECT email FROM tbl_registration WHERE id=?");
$u->bind_param("i", $user_id);
$u->execute();
$u_res = $u->get_result();
$user_email = $u_res->fetch_assoc()['email'] ?? '';

if (!empty($user_email)) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom(SMTP_USER, 'Smart Stores');
        $mail->addAddress($user_email, $full_name);
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - Smart Stores";

        // Build order items table
        $items_q->execute();
        $res_items = $items_q->get_result();
        $items_html = "";
        while ($item = $res_items->fetch_assoc()) {
            $total_price = $item['product_price'] * $item['quantity'];
            $items_html .= "
                <tr>
                    <td style='padding:8px;border:1px solid #ddd;'>{$item['product_name']}</td>
                    <td style='padding:8px;border:1px solid #ddd;'>{$item['quantity']}</td>
                    <td style='padding:8px;border:1px solid #ddd;'>$".number_format($item['product_price'], 2)."</td>
                    <td style='padding:8px;border:1px solid #ddd;'>$".number_format($total_price, 2)."</td>
                </tr>
            ";
        }

        // Email Body
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;line-height:1.6;color:#333;'>
            <h2 style='color:#1a73e8;'>Hello {$full_name},</h2>
            <p>Thank you for shopping with <b>Smart Stores!</b> 🎉</p>
            <p>We’ve successfully received your order. Below are your order details:</p>

            <table style='border-collapse:collapse;width:100%;margin:15px 0;'>
                <thead>
                    <tr style='background-color:#f2f2f2;'>
                        <th style='padding:8px;border:1px solid #ddd;'>Product</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Quantity</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Price</th>
                        <th style='padding:8px;border:1px solid #ddd;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$items_html}
                    <tr style='background-color:#f9f9f9;'>
                        <td colspan='3' style='padding:8px;border:1px solid #ddd;text-align:right;'><b>Grand Total:</b></td>
                        <td style='padding:8px;border:1px solid #ddd;'><b>$".number_format($grandTotal, 2)."</b></td>
                    </tr>
                </tbody>
            </table>

            <p><b>Payment Method:</b> {$payment_method}<br>
               <b>Status:</b> {$status}<br>
               <b>Shipping Address:</b> {$shipping_address}, {$city}, {$province} - {$postal_code}<br>
               <b>Contact:</b> {$contact_number}</p>

            <p>We’ll notify you once your order has been shipped.  
            For any queries, feel free to reach out to our support team.</p>

            <p style='margin-top:25px;'>Warm regards,<br>
            <b>Smart Stores Team</b><br>
            <a href='https://smartstores.example.com' style='color:#1a73e8;text-decoration:none;'>www.smartstores.com</a></p>
        </div>";

        $mail->send();
    } catch (Exception $e) {
        // ignore email failure silently
    }
}

// ---------- REDIRECT ----------
echo "<script>  window.location='order_success.php';</script>";
exit;
?>
<<<<<<< HEAD
=======
 
>>>>>>> 9050adbac01ff0aee4a36042e3750379e284efef
