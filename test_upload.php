<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once 'config/database.php';

try {
    // Create uploads directory if it doesn't exist
    $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception("Failed to create uploads directory");
        }
    }

    if (!is_writable($upload_dir)) {
        throw new Exception("Uploads directory is not writable");
    }

    echo "<h2>Directory Check:</h2>";
    echo "Upload directory: " . $upload_dir . "<br>";
    echo "Exists: Yes<br>";
    echo "Writable: " . (is_writable($upload_dir) ? "Yes" : "No") . "<br>";
    echo "Permissions: " . substr(sprintf('%o', fileperms($upload_dir)), -4) . "<br>";

    // Test database connection
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>Database Connection:</h2>";
    echo "Connected successfully<br>";
    
    // Test query execution
    $query = "SELECT COUNT(*) as count FROM uploads";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h2>Database Content:</h2>";
    echo "Number of files in database: " . $row['count'] . "<br>";
    
    // Display PHP configuration
    echo "<h2>PHP Configuration:</h2>";
    echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
    echo "post_max_size: " . ini_get('post_max_size') . "<br>";
    echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
    
    // Display form for testing
    echo "<h2>Test Upload:</h2>";
    echo '<form action="api/upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="file" required>
            <input type="submit" value="Upload">
          </form>';
    
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo $e->getMessage();
}
?> 