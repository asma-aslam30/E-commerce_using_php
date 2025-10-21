 <?php
// session_start();
// include '../config/connection.php';

// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../Views/user_login.php");
//     exit;
// }

// $user_id = $_SESSION['user_id'];

// $full_name = $_POST['full_name'] ?? '';
// $shipping_address = $_POST['shipping_address'] ?? '';
// $city = $_POST['city'] ?? '';
// $province = $_POST['province'] ?? '';
// $postal_code = $_POST['postal_code'] ?? '';
// $contact_number = $_POST['contact_number'] ?? '';
// $payment_method = $_POST['payment_method'] ?? 'COD';

// // Fetch pending order
// $query = "SELECT order_id FROM tbl_order WHERE user_id=? AND status='Pending' LIMIT 1";
// $stmt = $connection->prepare($query);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $res = $stmt->get_result();

// if ($res->num_rows == 0) {
//     echo "<script>alert('No pending order found!'); window.location.href='cart.php';</script>";
//     exit;
// }

// $order = $res->fetch_assoc();
// $order_id = $order['order_id'];

// // Update order
// $update_order = "UPDATE tbl_order SET payment_method=?, status='Confirmed' WHERE order_id=?";
// $stmt2 = $connection->prepare($update_order);
// $stmt2->bind_param("si", $payment_method, $order_id);
// $stmt2->execute();

// // Update user info
// $update_user = "UPDATE tbl_profile_info 
//                 SET full_name=?, shipping_address=?, city=?, province=?, postal_code=?, contact_number=? 
//                 WHERE user_id=?";
// $stmt3 = $connection->prepare($update_user);
// $stmt3->bind_param("ssssssi", $full_name, $shipping_address, $city, $province, $postal_code, $contact_number, $user_id);
// $stmt3->execute();

// echo "<script>alert('Order placed successfully!'); window.location.href='order_success.php';</script>";
// exit;













 
session_start();
include '../config/connection.php';

// User login check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Views/user_login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$full_name = $_POST['full_name'] ?? '';
$shipping_address = $_POST['shipping_address'] ?? '';
$city = $_POST['city'] ?? '';
$province = $_POST['province'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'COD';

// Fetch pending order
$query = "SELECT order_id FROM tbl_order WHERE user_id=? AND status='Pending' LIMIT 1";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
    echo "<script>alert('No pending order found!'); window.location.href='cart.php';</script>";
    exit;
}

$order = $res->fetch_assoc();
$order_id = $order['order_id'];

// Update order
$update_order = "UPDATE tbl_order SET payment_method=?, status='Confirmed' WHERE order_id=?";
$stmt2 = $connection->prepare($update_order);
$stmt2->bind_param("si", $payment_method, $order_id);
$stmt2->execute();

// ✅ Check if profile already exists
$check_user = $connection->prepare("SELECT user_id FROM tbl_profile_info WHERE user_id=?");
$check_user->bind_param("i", $user_id);
$check_user->execute();
$result = $check_user->get_result();

if ($result->num_rows > 0) {
    // Update existing profile
    $update_user = "UPDATE tbl_profile_info 
                    SET full_name=?, shipping_address=?, city=?, province=?, postal_code=?, contact_number=? 
                    WHERE user_id=?";
    $stmt3 = $connection->prepare($update_user);
    $stmt3->bind_param("ssssssi", $full_name, $shipping_address, $city, $province, $postal_code, $contact_number, $user_id);
    $stmt3->execute();
} else {
    // Insert new record
    $insert_user = "INSERT INTO tbl_profile_info (user_id, full_name, shipping_address, city, province, postal_code, contact_number) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt4 = $connection->prepare($insert_user);
    $stmt4->bind_param("issssss", $user_id, $full_name, $shipping_address, $city, $province, $postal_code, $contact_number);
    $stmt4->execute();
}

echo "<script>alert('Order placed successfully!'); window.location.href='order_success.php';</script>";
exit;
 





// session_start();
// include '../config/connection.php';
// require 'vendor/autoload.php'; // PHPMailer autoload

// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// // User login check
// if (!isset($_SESSION['user_id'])) {
//     header("Location: ../Views/user_login.php");
//     exit;
// }

// $user_id = $_SESSION['user_id'];

// $full_name = $_POST['full_name'] ?? '';
// $shipping_address = $_POST['shipping_address'] ?? '';
// $city = $_POST['city'] ?? '';
// $province = $_POST['province'] ?? '';
// $postal_code = $_POST['postal_code'] ?? '';
// $contact_number = $_POST['contact_number'] ?? '';
// $payment_method = $_POST['payment_method'] ?? 'COD';
// $user_email = $_POST['email'] ?? ''; // user email input field

// // ✅ Fetch pending order
// $query = "SELECT order_id FROM tbl_order WHERE user_id=? AND status='Pending' LIMIT 1";
// $stmt = $connection->prepare($query);
// $stmt->bind_param("i", $user_id);
// $stmt->execute();
// $res = $stmt->get_result();

// if ($res->num_rows == 0) {
//     echo "<script>alert('No pending order found!'); window.location.href='cart.php';</script>";
//     exit;
// }

// $order = $res->fetch_assoc();
// $order_id = $order['order_id'];

// // ✅ Update order
// $update_order = "UPDATE tbl_order SET payment_method=?, status='Confirmed' WHERE order_id=?";
// $stmt2 = $connection->prepare($update_order);
// $stmt2->bind_param("si", $payment_method, $order_id);
// $stmt2->execute();

// // ✅ Check profile info
// $res_check = $connection->query("SELECT * FROM tbl_profile_info WHERE user_id='$user_id'");

// if($res_check->num_rows == 0){
//     $insert_user = "INSERT INTO tbl_profile_info (user_id, full_name, shipping_address, city, province, postal_code, contact_number)
//                     VALUES (?, ?, ?, ?, ?, ?, ?)";
//     $stmt_insert = $connection->prepare($insert_user);
//     $stmt_insert->bind_param("issssss", $user_id, $full_name, $shipping_address, $city, $province, $postal_code, $contact_number);
//     if(!$stmt_insert->execute()){
//         echo "Error inserting profile info: " . $stmt_insert->error;
//         exit;
//     }
// } else {
//     $update_user = "UPDATE tbl_profile_info 
//                     SET full_name=?, shipping_address=?, city=?, province=?, postal_code=?, contact_number=? 
//                     WHERE user_id=?";
//     $stmt3 = $connection->prepare($update_user);
//     $stmt3->bind_param("ssssssi", $full_name, $shipping_address, $city, $province, $postal_code, $contact_number, $user_id);
//     if(!$stmt3->execute()){
//         echo "Error updating profile info: " . $stmt3->error;
//         exit;
//     }
// }

// // ✅ Fetch order items for email
// $order_items_query = "SELECT p.product_name, p.product_price, oi.quantity 
//                       FROM tbl_order_items oi
//                       JOIN tbl_products p ON oi.product_id = p.product_id
//                       WHERE oi.order_id=?";
// $stmt_items = $connection->prepare($order_items_query);
// $stmt_items->bind_param("i", $order_id);
// $stmt_items->execute();
// $res_items = $stmt_items->get_result();

// $order_items_html = "<ul>";
// while($item = $res_items->fetch_assoc()){
//     $order_items_html .= "<li>{$item['product_name']} - {$item['quantity']} x {$item['product_price']}</li>";
// }
// $order_items_html .= "</ul>";

// // ✅ Send email using PHPMailer
// $mail = new PHPMailer(true);

// try {
//     $mail->isSMTP();
//     $mail->Host       = 'smtp.gmail.com';      // SMTP server
//     $mail->SMTPAuth   = true;
//     $mail->Username   = 'aslamasma486@gmail.com'; // Your Gmail
//     $mail->Password   = 'pdjq gcez nzua tjtp';   // Gmail App Password
//     $mail->SMTPSecure = 'tls';
//     $mail->Port       = 587;

//     $mail->setFrom('yourgmail@gmail.com', 'Your Shop Name');
//     $mail->addAddress($user_email, $full_name);

//     $mail->isHTML(true);
//     $mail->Subject = "Order Confirmation - Order #$order_id";
//     $mail->Body    = "<h3>Thank you for your order, $full_name!</h3>
//                       <p>Your order #$order_id has been confirmed.</p>
//                       <p><strong>Shipping Address:</strong> $shipping_address, $city, $province, $postal_code</p>
//                       <p><strong>Contact Number:</strong> $contact_number</p>
//                       <p><strong>Order Details:</strong></p>
//                       $order_items_html
//                       <p><strong>Payment Method:</strong> $payment_method</p>
//                       <p>We will notify you once your order is shipped!</p>";

//     $mail->send();
// } catch (Exception $e) {
//     // Email failed, but order still placed
//     error_log("Email could not be sent. Error: " . $mail->ErrorInfo);
// }

// // ✅ Final redirect
// echo "<script>alert('Order placed successfully! Confirmation email sent.'); window.location.href='order_success.php';</script>";
// exit;
?>

