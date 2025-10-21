<?php
session_start();
include_once '../config/connection.php';
include_once '../Models/CartModel.php';

//  User must be logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ../Views/user_login.php");
    exit;
}

$cartModel = new CartModel($connection);
$user_id = (int)$_SESSION['user_id'];

//  Action detection (GET ya POST dono se)
$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {

    // 🛒 ADD PRODUCT TO CART
    case 'add':
        // URL example: CartController.php?action=add&id=PRODUCT_ID
        $product_id = intval($_GET['id'] ?? 0);
        if ($product_id > 0) {
            $cartModel->addToCart($user_id, $product_id, 1);
        }
        //  Redirect back to cart view
        header("Location: ../Views/cart.php");
        exit;


    //  INCREMENT QUANTITY
    case 'inc':
        $item_id = intval($_GET['id'] ?? 0);
        if ($item_id > 0) {
            $cartModel->changeQuantity($item_id, 'increment');
        }
        header("Location: ../Views/cart.php");
        exit;


    //  DECREMENT QUANTITY
    case 'dec':
        $item_id = intval($_GET['id'] ?? 0);
        if ($item_id > 0) {
            $cartModel->changeQuantity($item_id, 'decrement');
        }
        header("Location: ../Views/cart.php");
        exit;


    //  REMOVE ITEM FROM CART
    case 'remove':
        $item_id = intval($_GET['id'] ?? 0);
        if ($item_id > 0) {
            $cartModel->removeItem($item_id);
        }
        header("Location: ../Views/cart.php");
        exit;


    //  UPDATE ITEM QUANTITY MANUALLY
    // case 'update':
    //     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //         $item_id  = intval($_POST['item_id'] ?? 0);
    //         $quantity = max(1, intval($_POST['quantity'] ?? 1));
    //         if ($item_id > 0) {
    //             $cartModel->setQuantity($item_id, $quantity);
    //         }
    //     }
    //     header("Location: ../Views/cart.php");
    //     exit;


    //  DEFAULT — just show cart
    default:
        header("Location: ../Views/cart.php");
        exit;
}
