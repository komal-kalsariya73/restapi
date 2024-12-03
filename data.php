<?php
$headers = getallheaders();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';


include "db.php";
validateToken($authHeader);

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];
$request = json_decode(file_get_contents('php://input'), true);

if ($method == "POST") {
handlePost($conn, $_POST);
}

function handlePost($conn, $request)
{
    if (isset($request['id'])) {

        $id = intval($request['id']);
        $name = mysqli_real_escape_string($conn, $request['name']);
        $email = mysqli_real_escape_string($conn, $request['email']);

        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(["status" => "success", "message" => "User updated successfully."]);
        } else {
            echo json_encode(["status" => "fail", "message" => "Failed to update user.", "error" => mysqli_error($conn)]);
        }
    } else {

        if (isset($request['name']) && isset($request['email'])) {
            $name = mysqli_real_escape_string($conn, $request['name']);
            $email = mysqli_real_escape_string($conn, $request['email']);

            $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";

            if (mysqli_query($conn, $sql)) {
                echo json_encode(["status" => "success", "message" => "User created successfully."]);
            } else {
                echo json_encode(["status" => "fail", "message" => "Failed to create user.", "error" => mysqli_error($conn)]);
            }
        } else {
            echo json_encode(["status" => "fail", "message" => "Missing name or email in the request."]);
        }
    }
}
if ($method == "GET") {
    handleGet($conn, $_GET);
}

function handleGet($conn, $request)
{
    if (isset($request['id'])) {
        
        $id = intval($request['id']);
        $sql = "SELECT * FROM users WHERE id = $id";
    } else {
        $sql = "SELECT * FROM users";
    }
     $result = mysqli_query($conn, $sql);
    if ($result) {
        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
     if (isset($request['id']) && empty($users)) {
            echo json_encode(['status' => 'fail', 'message' => 'User not found.']);
        } else {
            echo json_encode(['status' => 'success', 'data' => $users]);
        }
    } else {
        
        echo json_encode(['status' => 'fail', 'message' => 'Failed to fetch user data.', 'error' => mysqli_error($conn)]);
    }
}


if ($method == "DELETE") {
    handleDelete($conn, $_GET);
}

function handleDelete($conn, $id)
{

    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $id = intval($_GET['id']);

        $checkselect="Select * from users where id=$id";
        $result=mysqli_query($conn,$checkselect);

        if($result && mysqli_num_rows($result)>0){
            $sql = "DELETE FROM users WHERE id=$id";


            if (mysqli_query($conn, $sql)) {
                echo json_encode(['status'=>'success','message' => 'User deleted successfully']);
            } else {
                echo json_encode(['status'=>'fail','message' => 'Failed to delete user', 'error' => mysqli_error($conn)]);
            }
        }
        else{
            echo json_encode(["status"=>"fail","message"=>"user not found"]);
        }
       
    } else {
        echo json_encode(["status"=>'fail','message' => 'Invalid or missing ID in the request']);
    }
}



if ($method == "PUT") {
    handleUpdate($conn, json_decode(file_get_contents('php://input'), true));
}

function handleUpdate($conn, $data)
{
    if (isset($data['id']) && isset($data['name']) && isset($data['email'])) {
        $id = intval($data['id']);
        $name = mysqli_real_escape_string($conn, $data['name']);
        $email = mysqli_real_escape_string($conn, $data['email']);

        $sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status'=>'success',"message" => "Update successful"]);
        } else {
            echo json_encode(['status'=>'fail',"message" => "Failed to update", "error" => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['status'=>'fail',"message" => "Missing id, name, or email in the request body"]);
    }
}
