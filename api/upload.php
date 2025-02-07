<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed");
    }

    if (!isset($_FILES['file'])) {
        throw new Exception("No file uploaded");
    }

    // Create database connection
    $database = new Database();
    $db = $database->getConnection();

    // Set up upload directory
    $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception("Failed to create uploads directory");
        }
    }

    if (!is_writable($upload_dir)) {
        throw new Exception("Uploads directory is not writable");
    }

    $uploaded_files = [];
    $errors = [];

    // Restructure files array if multiple files
    $files = [];
    $file_count = is_array($_FILES['file']['name']) ? count($_FILES['file']['name']) : 1;
    
    for ($i = 0; $i < $file_count; $i++) {
        if (is_array($_FILES['file']['name'])) {
            $files[] = [
                'name' => $_FILES['file']['name'][$i],
                'type' => $_FILES['file']['type'][$i],
                'tmp_name' => $_FILES['file']['tmp_name'][$i],
                'error' => $_FILES['file']['error'][$i],
                'size' => $_FILES['file']['size'][$i],
            ];
        } else {
            $files[] = $_FILES['file'];
        }
    }

    // Process each file
    foreach ($files as $file) {
        try {
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Upload error: " . $file['error']);
            }

            // Generate file information
            $file_name = $file['name'];
            $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_size = $file['size'];
            $file_key = uniqid() . '_' . time();

            // Move uploaded file
            $target_file = $upload_dir . DIRECTORY_SEPARATOR . $file_key . '.' . $file_type;
            if (!move_uploaded_file($file['tmp_name'], $target_file)) {
                throw new Exception("Failed to move uploaded file");
            }

            // Set proper file permissions
            chmod($target_file, 0644);

            // Insert into database
            $query = "INSERT INTO uploads (file_name, file_key, file_type, file_size, upload_date) 
                     VALUES (:file_name, :file_key, :file_type, :file_size, NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(":file_name", $file_name);
            $stmt->bindParam(":file_key", $file_key);
            $stmt->bindParam(":file_type", $file_type);
            $stmt->bindParam(":file_size", $file_size);

            if (!$stmt->execute()) {
                // If database insert fails, remove the uploaded file
                unlink($target_file);
                throw new Exception("Failed to store file information in database");
            }

            $uploaded_files[] = [
                "file_name" => $file_name,
                "file_key" => $file_key,
                "file_type" => $file_type,
                "file_size" => $file_size,
                "file_path" => "uploads/" . $file_key . "." . $file_type
            ];

        } catch (Exception $e) {
            $errors[] = [
                "file_name" => $file['name'],
                "error" => $e->getMessage()
            ];
        }
    }

    // Prepare response
    if (empty($errors)) {
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => count($uploaded_files) . " file(s) uploaded successfully",
            "data" => $uploaded_files
        ]);
    } else if (!empty($uploaded_files)) {
        http_response_code(207);
        echo json_encode([
            "status" => "partial_success",
            "message" => "Some files were uploaded successfully, but others failed",
            "successful_uploads" => $uploaded_files,
            "failed_uploads" => $errors
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "All uploads failed",
            "errors" => $errors
        ]);
    }

} catch (Exception $e) {
    error_log("Upload error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?> 