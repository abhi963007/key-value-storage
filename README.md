# 🚀 CloudStore - Next-Gen File Storage System

<div align="center">
    <img src="https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white">
    <img src="https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white">
    <img src="https://img.shields.io/badge/TailwindCSS-3.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white">
    <img src="https://img.shields.io/badge/Author-Abhiram-ff69b4?style=for-the-badge">
</div>

## 📖 Overview

CloudStore is a modern, secure file storage system built with PHP and MySQL, featuring a sleek user interface powered by TailwindCSS. It implements a robust key-value storage architecture for efficient file management and retrieval.

## ✨ Features

### 🔐 Secure File Storage
- Unique key generation for each file
- Encrypted file storage
- Secure file access control

### 📤 File Management
- Drag & drop file uploads
- Multi-file upload support
- Image and video file support
- File preview functionality

### 🔍 Smart Organization
- Automatic file categorization
- Advanced search capabilities
- Intuitive file browsing
- Clean, modern dashboard

### 💫 User Experience
- Responsive design
- Real-time upload progress
- Instant file previews
- Smooth animations

## 🛠️ Technical Architecture

### Key-Value Storage System
```
├── File Upload
│   ├── Generate Unique Key
│   ├── Store Physical File
│   └── Create Database Entry
│
├── Database Schema
│   ├── file_key (Primary Key)
│   ├── file_name
│   ├── file_type
│   ├── file_size
│   └── upload_date
│
└── File Retrieval
    ├── Key-Based Access
    ├── Type Filtering
    └── Search Indexing
```

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (for dependencies)

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/key-value-storage-system.git
   cd key-value-storage-system
   ```

2. **Database Setup**
   ```sql
   CREATE DATABASE storage_db;
   USE storage_db;
   SOURCE database.sql;
   ```

3. **Configure Database Connection**
   ```bash
   # Edit config/database.php with your credentials
   ```

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   ```

5. **Start the Server**
   ```bash
   php -S localhost:8000
   ```

## 📁 Project Structure

```
key-value-storage-system/
├── api/
│   ├── upload.php
│   ├── delete.php
│   └── view.php
├── config/
│   └── database.php
├── dashboard/
│   └── index.php
├── uploads/
├── index.php
└── README.md
```

## 💻 Usage

### File Upload
```php
POST /api/upload.php
Content-Type: multipart/form-data

file: [binary]
```

### File Retrieval
```php
GET /api/view.php?key={file_key}
```

### File Deletion
```php
DELETE /api/delete.php?key={file_key}
```

## 🔒 Security Features

- Unique key generation for each file
- SQL injection prevention
- XSS protection
- File type validation
- Size limit enforcement
- Access control

## 🎯 Performance Optimizations

- Efficient key-value storage
- Optimized database queries
- Lazy loading of images
- Caching mechanisms
- Compressed file storage

## 📱 Responsive Design

The interface is fully responsive and works seamlessly across:
- 💻 Desktop computers
- 💪 Tablets
- 📱 Mobile devices

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🌟 Acknowledgments

- TailwindCSS for the amazing UI framework
- LottieFiles for beautiful animations
- Special thanks to all contributors

---
<div align="center">
    <p>Designed & Developed with ❤️ by <b>Abhiram</b></p>
    <p>© 2025,All rights reserved.</p>
</div> 