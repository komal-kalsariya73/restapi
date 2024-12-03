<?php
include "db.php";
header("Content-Type: application/json");
$method=$_SERVER['REQUEST_METHOD'];
if ($method == "POST") {
    handleAddToCart($conn);
}

function handleAddToCart($conn) {
    
 
    if (isset($_POST['product_id']) && isset($_POST['quantity'])) {

        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
    
        $query = "INSERT INTO cart (product_id, quantity) values ('$product_id', '$quantity')";
        if (mysqli_query($conn, $query)) {
    
            echo json_encode(["status" => "Success", "message" => "Cart added successfully", "id" => mysqli_insert_id($conn)]);
        } else {
            echo json_encode(["status" => "False", "message" => "Failed to add cart"]);
        }
    } else {
         echo json_encode(["error" => "Invalid input"]);
    }
}


if($method == "GET"){
    handleGet($conn,$_GET);
}
function handleGet($conn){
    $sql = "SELECT cart.id, product.name AS product_name, product.price, cart.quantity, 
    (product.price * cart.quantity) AS total_price
FROM cart
INNER JOIN product ON cart.product_id = product.id"
;

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

