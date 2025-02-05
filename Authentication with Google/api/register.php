<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once './config/database.php';
include_once './models/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(
    !empty($data->username) &&
    !empty($data->email) &&
    !empty($data->password)
){
    $user->username = $data->username;
    $user->email = $data->email;
    $user->password = $data->password;

    if($user->emailExists()){
        http_response_code(400);
        echo json_encode(array("message" => "Email already exists."));
        exit();
    }

    try {
        if($user->create()){
            http_response_code(201);
            echo json_encode(array("message" => "User was created."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Unable to create user.", "error" => $user->getLastError()));
        }
    } catch (Exception $e) {
        http_response_code(503);
        echo json_encode(array("message" => "Unable to create user.", "error" => $e->getMessage()));
    }
} else {
    http_response_code(400);
    $missing = array();
    if(empty($data->username)) $missing[] = "username";
    if(empty($data->email)) $missing[] = "email";
    if(empty($data->password)) $missing[] = "password";
    echo json_encode(array(
        "message" => "Unable to create user. Data is incomplete.",
        "missing_fields" => $missing,
        "received_data" => $data
    ));
}
?> 