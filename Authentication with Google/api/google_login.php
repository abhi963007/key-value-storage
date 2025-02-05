<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'vendor/autoload.php';
include_once './config/database.php';
include_once './models/User.php';
include_once './config/jwt_utils.php';

// Initialize Google Client
$client = new Google_Client(['client_id' => '124938168618-chnchutbuof1fs3nj69280bpcicc23nm.apps.googleusercontent.com']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->google_token)) {
    try {
        // Verify the Google token
        $payload = $client->verifyIdToken($data->google_token);
        
        if ($payload) {
            $google_id = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            
            // Check if user exists
            $user->email = $email;
            if(!$user->emailExists()) {
                // Create new user
                $user->username = $name;
                $user->password = password_hash(bin2hex(random_bytes(10)), PASSWORD_BCRYPT); // Random password
                $user->create();
            }
            
            // Generate JWT token
            $token = JWTUtils::generateToken($user->id, $user->username);
            
            http_response_code(200);
            echo json_encode(array(
                "message" => "Login successful.",
                "token" => $token,
                "user" => array(
                    "id" => $user->id,
                    "username" => $user->username,
                    "email" => $email
                )
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Invalid Google token."));
        }
    } catch(Exception $e) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Error processing Google sign-in.",
            "error" => $e->getMessage()
        ));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Missing Google token."));
}
?> 