<?php


$servername="localhost";
$username="root";
$password="";
$dbname="users";

$conn=mysqli_connect("$servername","$username","$password","$dbname");

if(!$conn){
    die("connection fail...".mysqli_connect_error());
}

  


function validateToken($authHeader, $valid_token = "h12jkdsh23sdsssd435") {
    if ($authHeader == '') {
        echo json_encode(['status' => 'fail', 'message' => 'Authorization header is missing.']);
        exit();
    }
     if ($authHeader !== $valid_token) {
        echo json_encode(['status' => 'fail', 'message' => 'Invalid token. Access denied.']);
        exit();
    }
}

?>