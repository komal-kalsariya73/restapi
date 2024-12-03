<?php
include "db.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    handlePlaceOrder($conn);
}
if($method == "GET"){
    handleGetOrders($conn);
}
function handlePlaceOrder($conn) {
    
    if (isset($_POST['cart_id'])) {
        $cart_id = intval($_POST['cart_id']);
        
        $cartSql = "SELECT * FROM cart WHERE id = $cart_id";
        $cartResult = mysqli_query($conn, $cartSql);

        if ($cartResult && mysqli_num_rows($cartResult) > 0) {

            $orderSql = "INSERT INTO orders (cart_id, order_date) VALUES ($cart_id, NOW())";

            if (mysqli_query($conn, $orderSql)) {
                echo json_encode(["status" => "success", "message" => "Order placed successfully."]);
            } else {
                echo json_encode(["status" => "fail", "message" => "Failed to place the order.", "error" => mysqli_error($conn)]);
            }

            
            $clearCartSql = "DELETE FROM cart WHERE id = $cart_id";
            mysqli_query($conn, $clearCartSql);
        } else {
            echo json_encode(["status" => "fail", "message" => "No items in the cart."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Missing cart_id in the request."]);
    }
}

function handleGetOrders($conn) {
    $sql = "SELECT orders.id AS order_id, orders.cart_id, orders.order_date, 
                   cart.product_id, cart.quantity, product.name AS product_name, 
                   product.price, (product.price * cart.quantity) AS total_price
            FROM orders
            INNER JOIN cart ON orders.cart_id = cart.id
            INNER JOIN product ON cart.product_id = product.id";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "fail", "message" => "No orders found."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Failed to fetch order data.", "error" => mysqli_error($conn)]);
    }
}
?>
