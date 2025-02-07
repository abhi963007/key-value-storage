<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check table structure
    $query = "DESCRIBE uploads";
    $stmt = $db->prepare($query);
    $stmt->execute();
    echo "<h2>Table Structure:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Check existing records
    $query = "SELECT * FROM uploads";
    $stmt = $db->prepare($query);
    $stmt->execute();
    echo "<h2>Existing Records:</h2>";
    echo "<pre>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
    }
    echo "</pre>";
    
    // Check uploads directory
    $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    echo "<h2>Uploads Directory:</h2>";
    echo "Path: " . $upload_dir . "<br>";
    echo "Exists: " . (file_exists($upload_dir) ? 'Yes' : 'No') . "<br>";
    if (file_exists($upload_dir)) {
        echo "Writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "<br>";
        echo "Contents:<br>";
        $files = scandir($upload_dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "- " . $file . "<br>";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 