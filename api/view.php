<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['key'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Missing file key."));
        exit();
    }

    $file_key = $_GET['key'];
    
    // Query database
    $query = "SELECT * FROM uploads WHERE file_key = :file_key";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(":file_key", $file_key);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $file_path = "../uploads/" . $file_key . "." . $row['file_type'];
            
            if (file_exists($file_path)) {
                $file_url = "http://" . $_SERVER['HTTP_HOST'] . 
                           dirname($_SERVER['PHP_SELF']) . "/../uploads/" . 
                           $file_key . "." . $row['file_type'];
                
                http_response_code(200);
                echo json_encode(array(
                    "file_name" => $row['file_name'],
                    "file_type" => $row['file_type'],
                    "file_size" => $row['file_size'],
                    "upload_date" => $row['upload_date'],
                    "file_url" => $file_url
                ));
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "File not found on server."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "File key not found."));
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Database error: " . $e->getMessage()));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method not allowed."));
}
?> 