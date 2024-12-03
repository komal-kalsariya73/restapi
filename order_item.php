<?php
include "db.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    handlePlaceOrder($conn,$_GET);
}

function handlePlaceOrder($conn) {
    

    if (isset($_GET['cart_id'])) {
        $cart_id = intval($_GET['cart_id']);
        
    
        $cartSql = "SELECT * FROM cart WHERE id = $cart_id";
        $cartResult = mysqli_query($conn, $cartSql);

        if ($cartResult && mysqli_num_rows($cartResult) > 0) {
        
            while ($cartRow = mysqli_fetch_assoc($cartResult)) {
                $product_id = $cartRow['product_id'];
                $quantity = $cartRow['quantity'];

                
                $orderSql = "INSERT INTO orders (cart_id, order_date) 
                             VALUES ($cart_id, NOW())";

                if (mysqli_query($conn, $orderSql)) {
                    echo json_encode(["status" => "success", "message" => "Order placed successfully."]);
                } else {
                    echo json_encode(["status" => "fail", "message" => "Failed to place the order.", "error" => mysqli_error($conn)]);
                }
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
?>
