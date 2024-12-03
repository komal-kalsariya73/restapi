<?php
include "db.php";
header("Content-Type: application/json");
$method=$_SERVER['REQUEST_METHOD'];

if($method=="GET"){
    handleGet($conn,$_GET);
}

function handleGet($conn, $request)
{
    if (isset($request['id'])) {
        
        $id = intval($request['id']);
        $sql = "SELECT * FROM product WHERE id = $id";
    } else {
        $sql = "SELECT * FROM product";
    }
     $result = mysqli_query($conn, $sql);
    if ($result) {
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
     if (isset($request['id']) && empty($users)) {
            echo json_encode(['status' => 'fail', 'message' => 'product not found.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $users]);
        }
    } else {
        
        echo json_encode(['status' => 'fail', 'message' => 'Failed to fetch product data.', 'error' => mysqli_error($conn)]);
    }
}

if($method == "POST"){
    handlePost($conn);
}

function handlePost($conn){

    
    if(isset($_POST['name']) && isset($_POST['price']) && isset($_POST['description']) && isset($_FILES['image'])){
        $name=mysqli_real_escape_string($conn,$_POST['name']);
        $price=floatval($_POST['price']);
        $description=mysqli_real_escape_string($conn,$_POST['description']);

        $uploadDir="uploads/";
        $image=$_FILES['image'];
        $imagename=time() . "-".basename($image['name']);
        $iamgepath=$uploadDir . $imagename;

        if(move_uploaded_file($image['tmp_name'],$iamgepath)){
            $sql = "INSERT INTO product(name, price,description, image) VALUES ('$name', '$price','$description', '$imagename')";

            if(mysqli_query($conn,$sql)){
                echo json_encode(["status"=>"success","message"=>"product added succefully..."]);
            }
            else{
                echo json_encode(["status"=>"fail","message"=>"fail to insert product"]);
            }

        }
        else{
            echo json_encode(["status"=>"fail","message"=>"fail to upload image"]);
        }
    }
    else{
        echo json_encode(["status"=>"fail","message"=>"missing name price and image"]);
    }
}


?>