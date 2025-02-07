<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if (!isset($_GET['key'])) {
        http_response_code(400);
        echo json_encode(array("message" => "Missing file key."));
        exit();
    }

    $file_key = $_GET['key'];
    
    // First get the file information
    $query = "SELECT * FROM uploads WHERE file_key = :file_key";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(":file_key", $file_key);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $file_path = "../uploads/" . $file_key . "." . $row['file_type'];
            
            // Delete from database first
            $delete_query = "DELETE FROM uploads WHERE file_key = :file_key";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->bindParam(":file_key", $file_key);
            
            if ($delete_stmt->execute()) {
                // Then delete the file
                if (file_exists($file_path) && unlink($file_path)) {
                    http_response_code(200);
                    echo json_encode(array("message" => "File deleted successfully."));
                } else {
                    http_response_code(500);
                    echo json_encode(array("message" => "File deleted from database but not from storage."));
                }
            } else {
                http_response_code(500);
                echo json_encode(array("message" => "Failed to delete file from database."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "File not found."));
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