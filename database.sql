-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS storage_db;
USE storage_db;

-- Create the uploads table if it doesn't exist
CREATE TABLE IF NOT EXISTS uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_key VARCHAR(100) NOT NULL UNIQUE,
    file_type VARCHAR(50) NOT NULL,
    file_size BIGINT NOT NULL,
    upload_date DATETIME NOT NULL,
    INDEX idx_file_key (file_key),
    INDEX idx_upload_date (upload_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 