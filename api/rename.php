<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['key']) || !isset($data['new_name'])) {
        throw new Exception('Missing required parameters');
    }

    $file_key = $data['key'];
    $new_name = $data['new_name'];

    // Validate new name
    if (empty($new_name)) {
        throw new Exception('New name cannot be empty');
    }

    // Remove any directory traversal attempts
    $new_name = basename($new_name);

    // Include database connection
    include_once '../config/database.php';
    $database = new Database();
    $db = $database->getConnection();

    // Get current file info
    $query = "SELECT * FROM uploads WHERE file_key = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$file_key]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        throw new Exception('File not found');
    }

    // Update filename in database
    $update_query = "UPDATE uploads SET file_name = ? WHERE file_key = ?";
    $update_stmt = $db->prepare($update_query);
    $result = $update_stmt->execute([$new_name, $file_key]);

    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'File renamed successfully',
            'new_name' => $new_name
        ]);
    } else {
        throw new Exception('Failed to rename file');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 