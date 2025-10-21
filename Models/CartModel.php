<?php
class CartModel {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ensure a pending order exists for user and return order_id
    private function getOrCreatePendingOrder($user_id) {
        echo "Checking pending order for user: $user_id<br>";

        $q = "SELECT order_id FROM tbl_order WHERE user_id = ? AND status = 'Pending' LIMIT 1";
        
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) return (int)$row['order_id'];

        // create new pending order
        $ins = "INSERT INTO tbl_order (user_id, total_amount, status) VALUES (?, 0, 'Pending')";
        $s = $this->conn->prepare($ins);
        $s->bind_param("i", $user_id);
        $s->execute();
        return $this->conn->insert_id;
    }

    // Add product to cart (order_items). If exists increment quantity
    public function addToCart($user_id, $product_id, $quantity = 1) {
        $order_id = $this->getOrCreatePendingOrder($user_id);

        // check if product already in items
        $q = "SELECT order_item_id, quantity FROM tbl_order_items WHERE order_id = ? AND product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("ii", $order_id, $product_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            // update quantity
            $newQ = $row['quantity'] + $quantity;
            $u = "UPDATE tbl_order_items SET quantity = ? WHERE order_item_id = ?";
            $ust = $this->conn->prepare($u);
            $ust->bind_param("ii", $newQ, $row['item_id']);
            $ust->execute();
        } else {
            // get current price from products table (snapshot)
            $pq = "SELECT product_price FROM tbl_products WHERE product_id = ? LIMIT 1";
            $pstmt = $this->conn->prepare($pq);
            $pstmt->bind_param("i", $product_id);
            $pstmt->execute();
            $pres = $pstmt->get_result();
            $price = ($pr = $pres->fetch_assoc()) ? $pr['product_price'] : 0.00;

            $ins = "INSERT INTO tbl_order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
            $ist = $this->conn->prepare($ins);
            $ist->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $ist->execute();
        }

        // optionally update order total (easy approach: compute fresh)
        $this->recalculateOrderTotal($order_id);
        return true;
    }

    // Fetch all cart items for a user (pending order)
    // public function getCartItems($user_id) {
    //     $q = "SELECT oi.item_id, oi.order_id, oi.product_id, oi.quantity, oi.price,
    //                  p.product_name, p.image_path
    //           FROM tbl_order_items oi
    //           JOIN tbl_order o ON oi.order_id = o.order_id
    //           JOIN tbl_products p ON oi.product_id = p.product_id
    //           WHERE o.user_id = ? AND o.status = 'Pending'";
    //     $stmt = $this->conn->prepare($q);
    //     $stmt->bind_param("i", $user_id);
    //     $stmt->execute();
    //     return $stmt->get_result(); // caller will iterate
    // }



public function getCartItems($user_id) {
    $query = "
        SELECT 
            oi.*, 
            p.product_name, 
            p.product_price, 
            pci.image_path
        FROM 
            tbl_order_items oi
        JOIN 
            tbl_order o ON oi.order_id = o.order_id
        JOIN 
            tbl_products p ON oi.product_id = p.product_id
        LEFT JOIN 
            tbl_category_images pci ON p.product_id = pci.product_id
        WHERE 
            o.user_id = ? 
            AND o.status = 'Pending'
        GROUP BY 
            oi.order_item_id
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}
    // Update quantity (set exact) for an item
    public function setQuantity($item_id, $quantity) {
        $q = "UPDATE tbl_order_items SET quantity = ? WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("ii", $quantity, $item_id);
        $stmt->execute();

        // fetch order_id of this item to recalc total
        $ridQ = "SELECT order_id FROM tbl_order_items WHERE order_item_id = ? LIMIT 1";
        $rstmt = $this->conn->prepare($ridQ);
        $rstmt->bind_param("i", $item_id);
        $rstmt->execute();
        $ridRes = $rstmt->get_result();
        if ($r = $ridRes->fetch_assoc()) $this->recalculateOrderTotal($r['order_id']);
        return true;
    }

    // increment/decrement by 1 (decrement won't go below 1)
    public function changeQuantity($item_id, $direction) {
        if ($direction === 'increment') {
            $q = "UPDATE tbl_order_items SET quantity = quantity + 1 WHERE order_item_id = ?";
        } else {
            $q = "UPDATE tbl_order_items SET quantity = GREATEST(quantity - 1, 1) WHERE order_item_id = ?";
        }
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        // recalc
        $ridQ = "SELECT order_id FROM tbl_order_items WHERE order_item_id = ? LIMIT 1";
        $rstmt = $this->conn->prepare($ridQ);
        $rstmt->bind_param("i", $item_id);
        $rstmt->execute();
        $ridRes = $rstmt->get_result();
        if ($r = $ridRes->fetch_assoc()) $this->recalculateOrderTotal($r['order_id']);
        return true;
    }

    // Remove an item from cart (order_items)
    public function removeItem($item_id) {
        // get order_id first
        $ridQ = "SELECT order_id FROM tbl_order_items WHERE order_item_id = ? LIMIT 1";
        $rstmt = $this->conn->prepare($ridQ);
        $rstmt->bind_param("i", $item_id);
        $rstmt->execute();
        $ridRes = $rstmt->get_result();
        $order_id = null;
        if ($r = $ridRes->fetch_assoc()) $order_id = $r['order_id'];

        $q = "DELETE FROM tbl_order_items WHERE order_item_id = ?";
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("i", $item_id);
        $stmt->execute();

        if ($order_id) $this->recalculateOrderTotal($order_id);
        return true;
    }

    // Recompute order total from items
    private function recalculateOrderTotal($order_id) {
        $q = "SELECT SUM(price * quantity) AS total FROM tbl_order_items WHERE order_id = ?";
        $stmt = $this->conn->prepare($q);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $total = ($r = $res->fetch_assoc()) ? $r['total'] : 0.00;

        $u = "UPDATE tbl_order SET total_amount = ? WHERE order_id = ?";
        $ust = $this->conn->prepare($u);
        $ust->bind_param("di", $total, $order_id);
        $ust->execute();
    }
}
