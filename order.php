<?php
include "db.php";
header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    handleGet($conn);
}

function handleGet($conn) {
    $sql = "SELECT orders.id, product.name AS product_name, product.price, cart.quantity, 
                   (product.price * cart.quantity) AS total_price, orders.order_date
            FROM orders
            INNER JOIN cart ON orders.cart_id = cart.id
             INNER JOIN product on cart.product_id = product.id";
           

    $result = mysqli_query($conn, $sql);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            echo json_encode(["status" => "success", "data" => $data]);
        } else {
            echo json_encode(["status" => "fail", "message" => "No records found."]);
        }
    } else {
        echo json_encode(["status" => "fail", "message" => "Failed to fetch product data.", "error" => mysqli_error($conn)]);
    }
}
?>
