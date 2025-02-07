<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    include_once '../config/database.php';

    $database = new Database();
    $db = $database->getConnection();

    // Fetch all uploads
    $query = "SELECT * FROM uploads ORDER BY upload_date DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Store files in array
    $files = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $files[] = $row;
    }

    // Debug information
    error_log("Database connection status: " . ($db ? "Connected" : "Not connected"));
    error_log("Number of files found: " . count($files));

    // Check uploads directory
    $upload_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads';
    error_log("Upload directory: " . $upload_dir);
    error_log("Upload directory exists: " . (file_exists($upload_dir) ? "Yes" : "No"));
    error_log("Upload directory is writable: " . (is_writable($upload_dir) ? "Yes" : "No"));

    if (file_exists($upload_dir)) {
        $dir_contents = scandir($upload_dir);
        error_log("Upload directory contents: " . implode(", ", array_diff($dir_contents, array('.', '..'))));
    }

} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $error_message = $e->getMessage();
    $files = [];
}

// Function to get file URL
function getFileUrl($file_key, $file_type) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = dirname(dirname($_SERVER['PHP_SELF']));
    return $protocol . '://' . $host . $baseUrl . '/uploads/' . $file_key . '.' . $file_type;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudStore Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Architects+Daughter&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tsparticles@2.12.0/tsparticles.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Architects Daughter', 'Inter', sans-serif;
            background-color: #0f172a;
        }
        .architects-font {
            font-family: 'Architects Daughter', cursive;
        }
        .gradient-text {
            background: linear-gradient(135deg, #818cf8 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            background: rgba(30, 41, 59, 0.8);
            transform: translateY(-2px);
        }
        .dashboard-gradient {
            background: radial-gradient(circle at top right, rgba(129, 140, 248, 0.1) 0%, transparent 60%),
                        radial-gradient(circle at bottom left, rgba(167, 139, 250, 0.1) 0%, transparent 60%);
        }
        .upload-drop-zone {
            border: 2px dashed rgba(148, 163, 184, 0.4);
            transition: all 0.3s ease-in-out;
            background: rgba(15, 23, 42, 0.6);
        }
        .upload-drop-zone:hover {
            border-color: #818cf8;
            background: rgba(129, 140, 248, 0.1);
        }
        .file-card {
            transition: all 0.3s ease;
        }
        .file-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        .search-input {
            background: rgba(15, 23, 42, 0.6);
        }
        .file-preview {
            width: 100%;
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.6);
            border-radius: 0.5rem;
            overflow: hidden;
            padding: 1rem;
        }
        .file-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .preview-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.2s ease-in-out;
        }
        .preview-image:hover {
            transform: scale(1.05);
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .upload-ready {
            animation: pulse 2s infinite;
        }
        .preview-item {
            position: relative;
            aspect-ratio: 1;
            overflow: hidden;
            border-radius: 0.5rem;
            background: rgba(15, 23, 42, 0.6);
        }
        .preview-item img,
        .preview-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s ease-in-out;
        }
        .preview-item:hover img,
        .preview-item:hover video {
            transform: scale(1.05);
        }
        .preview-item .remove-btn {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(239, 68, 68, 0.9);
            color: white;
            padding: 0.25rem;
            border-radius: 9999px;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .preview-item:hover .remove-btn {
            opacity: 1;
        }
        .preview-item .file-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 0.5rem;
            background: rgba(15, 23, 42, 0.8);
            color: white;
            font-size: 0.75rem;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        .preview-item:hover .file-info {
            opacity: 1;
        }
        .preview-video {
            background: #000;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .file-preview video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: rgba(0, 0, 0, 0.1);
        }
        .file-card:hover .preview-video {
            opacity: 0.8;
        }
        /* Video controls styling */
        video::-webkit-media-controls {
            background-color: rgba(0, 0, 0, 0.5);
        }
        .video-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
        }
        .video-placeholder svg {
            width: 48px;
            height: 48px;
            color: rgba(255, 255, 255, 0.7);
        }
        /* Loading indicator for video thumbnails */
        .video-loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
        #previewGrid {
            width: 100%;
            height: 100%;
            padding: 1rem;
        }
        .grid-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .auto-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 300px));
            gap: 1.5rem;
            width: 100%;
            max-width: 1200px;
            justify-content: center;
        }
        @media (max-width: 640px) {
            .auto-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 280px));
            }
        }
        .col-span-full {
            grid-column: 1 / -1;
        }
        .section-header {
            padding: 0 1rem;
            margin-bottom: 1.5rem;
            width: 100%;
        }
        #tsparticles {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen dashboard-gradient">
    <!-- Particles Container -->
    <div id="tsparticles"></div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 h-full w-64 bg-slate-900/95 border-r border-slate-800 backdrop-blur-lg z-50 transform transition-transform duration-300 ease-in-out" id="sidebar">
            <div class="p-6">
                <div class="flex items-center space-x-2 mb-8">
                    <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-xl font-bold gradient-text architects-font">CloudStore</span>
                </div>
                <nav class="space-y-4">
                    <a href="#upload" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-indigo-500/10 text-indigo-400 hover:bg-indigo-500/20 transition-colors architects-font">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span>Upload Files</span>
                    </a>
                    <a href="#images" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-slate-800/50 transition-colors architects-font">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Images</span>
                    </a>
                    <a href="#videos" class="flex items-center space-x-2 px-4 py-2 rounded-lg hover:bg-slate-800/50 transition-colors architects-font">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span>Videos</span>
                    </a>
                </nav>
            </div>
            <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-slate-800">
                <a href="../" class="flex items-center justify-center space-x-2 px-4 py-2 rounded-lg border border-slate-700 hover:border-indigo-500 text-slate-300 hover:text-indigo-400 transition-colors architects-font">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Back to Home</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="ml-64 min-h-screen">
            <!-- Top Bar -->
            <div class="fixed top-0 right-0 left-64 bg-slate-900/80 backdrop-blur-md border-b border-slate-800 z-40">
                <div class="px-8 py-4">
                    <div class="relative">
                        <input type="text" id="searchKey" placeholder="Search files by name..." 
                            class="w-full px-4 py-3 pl-12 search-input border border-slate-700 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500 transition-colors architects-font">
                        <svg class="absolute left-4 top-3.5 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="pt-24 px-8 pb-12">
                <?php if (isset($error_message)): ?>
                    <div class="mb-8 p-4 bg-red-500/20 border border-red-500 rounded-lg text-red-200">
                        <p class="architects-font">Error: <?php echo htmlspecialchars($error_message); ?></p>
                        <p class="text-sm mt-2 architects-font">Please make sure:</p>
                        <ul class="list-disc list-inside text-sm ml-4 architects-font">
                            <li>The database server is running</li>
                            <li>The database 'storage_db' exists</li>
                            <li>The database credentials are correct</li>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Upload Section -->
                <section id="upload" class="mb-12">
                    <div class="glass-card rounded-2xl p-8">
                        <h2 class="text-2xl font-bold mb-6 gradient-text architects-font flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Upload Files
                        </h2>
                        <form id="uploadForm" class="space-y-4" enctype="multipart/form-data">
                            <div id="dropZone" class="upload-drop-zone rounded-xl p-6 cursor-pointer relative min-h-[300px] flex items-center justify-center">
                                <input type="file" id="file" name="file[]" class="hidden" required accept="image/*,video/*" multiple>
                                
                                <!-- Default Upload State -->
                                <div id="defaultState" class="transition-opacity duration-200 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-16 w-16 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-slate-300 text-lg architects-font">
                                            <span class="font-medium">Click to upload</span> or drag and drop<br>
                                            <span class="text-sm text-slate-400">multiple images and videos allowed</span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Preview Grid -->
                                <div id="previewGrid" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 w-full">
                                    <!-- Preview items will be added here dynamically -->
                                </div>
                            </div>

                            <!-- Upload Button - Hidden by default -->
                            <button type="submit" id="uploadButton" class="hidden w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                                <span class="upload-text">Upload Files</span>
                                <div class="upload-progress hidden">
                                    <div class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Uploading...</span>
                                    </div>
                                    <div class="progress-bar mt-2 h-1 bg-indigo-200 rounded-full overflow-hidden">
                                        <div class="progress h-full bg-white transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>
                </section>

                <!-- Files Sections -->
                <div class="space-y-12">
                    <!-- Images Section -->
                    <section id="images" class="hidden">
                        <h2 class="text-2xl font-bold gradient-text architects-font mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Images
                        </h2>
                        <div class="grid-container">
                            <div class="auto-grid">
                                <?php
                                $has_images = false;
                                foreach ($files as $file):
                                    $file_url = '/key-value-storage-system/uploads/' . $file['file_key'] . '.' . $file['file_type'];
                                    $is_image = in_array(strtolower($file['file_type']), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    if ($is_image):
                                        $has_images = true;
                                ?>
                                    <div class="file-card glass-card rounded-2xl overflow-hidden" data-filename="<?php echo strtolower(htmlspecialchars($file['file_name'])); ?>" data-type="image">
                                        <div class="file-preview">
                                            <img src="<?php echo htmlspecialchars($file_url); ?>" 
                                                class="preview-image cursor-pointer w-full h-full object-contain" 
                                                alt="<?php echo htmlspecialchars($file['file_name']); ?>"
                                                onclick="window.open('<?php echo htmlspecialchars($file_url); ?>', '_blank')">
                                        </div>
                                        <div class="p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-medium text-white truncate rename-display" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                    <?php echo htmlspecialchars($file['file_name']); ?>
                                                </h3>
                                                <button onclick="startRename(this, '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo $file['file_key']; ?>')"
                                                        class="p-1 text-slate-400 hover:text-indigo-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <form class="rename-form hidden">
                                                <div class="flex items-center space-x-2">
                                                    <input type="text" class="rename-input flex-1 px-2 py-1 bg-slate-800 border border-slate-700 rounded text-white text-sm focus:outline-none focus:border-indigo-500">
                                                    <button type="submit" class="p-1 text-green-400 hover:text-green-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="p-1 text-red-400 hover:text-red-300" onclick="cancelRename(this)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-slate-400"><?php echo number_format($file['file_size'] / 1024, 2) . ' KB'; ?></span>
                                                <span class="text-slate-400"><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                                            </div>
                                            <div class="flex space-x-2 pt-2">
                                                <a href="<?php echo htmlspecialchars($file_url); ?>" 
                                                   target="_blank"
                                                   class="flex-1 px-4 py-2 text-center text-indigo-400 border border-indigo-400 rounded-lg hover:bg-indigo-400/10 transition-colors">
                                                    View
                                                </a>
                                                <button onclick="deleteFile('<?php echo $file['file_key']; ?>')"
                                                        class="flex-1 px-4 py-2 text-red-400 border border-red-400 rounded-lg hover:bg-red-400/10 transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    endif;
                                endforeach;
                                if (!$has_images):
                                ?>
                                    <div class="col-span-full text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-slate-400">No images uploaded yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>

                    <!-- Videos Section -->
                    <section id="videos" class="hidden">
                        <h2 class="text-2xl font-bold gradient-text architects-font mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            Videos
                        </h2>
                        <div class="grid-container">
                            <div class="auto-grid">
                                <?php
                                $has_videos = false;
                                foreach ($files as $file):
                                    $file_url = '/key-value-storage-system/uploads/' . $file['file_key'] . '.' . $file['file_type'];
                                    $is_video = in_array(strtolower($file['file_type']), ['mp4', 'webm', 'ogg']);
                                    if ($is_video):
                                        $has_videos = true;
                                ?>
                                    <div class="file-card glass-card rounded-2xl overflow-hidden" data-filename="<?php echo strtolower(htmlspecialchars($file['file_name'])); ?>" data-type="video">
                                        <div class="file-preview">
                                            <video 
                                                src="<?php echo htmlspecialchars($file_url); ?>"
                                                class="preview-video cursor-pointer w-full h-full object-contain"
                                                onclick="window.open('<?php echo htmlspecialchars($file_url); ?>', '_blank')"
                                                controls
                                                preload="metadata">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="p-4 space-y-3">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-medium text-white truncate rename-display" title="<?php echo htmlspecialchars($file['file_name']); ?>">
                                                    <?php echo htmlspecialchars($file['file_name']); ?>
                                                </h3>
                                                <button onclick="startRename(this, '<?php echo htmlspecialchars($file['file_name']); ?>', '<?php echo $file['file_key']; ?>')"
                                                        class="p-1 text-slate-400 hover:text-indigo-400 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <form class="rename-form hidden">
                                                <div class="flex items-center space-x-2">
                                                    <input type="text" class="rename-input flex-1 px-2 py-1 bg-slate-800 border border-slate-700 rounded text-white text-sm focus:outline-none focus:border-indigo-500">
                                                    <button type="submit" class="p-1 text-green-400 hover:text-green-300">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="p-1 text-red-400 hover:text-red-300" onclick="cancelRename(this)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-slate-400"><?php echo number_format($file['file_size'] / 1024, 2) . ' KB'; ?></span>
                                                <span class="text-slate-400"><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                                            </div>
                                            <div class="flex space-x-2 pt-2">
                                                <a href="<?php echo htmlspecialchars($file_url); ?>" 
                                                   target="_blank"
                                                   class="flex-1 px-4 py-2 text-center text-indigo-400 border border-indigo-400 rounded-lg hover:bg-indigo-400/10 transition-colors">
                                                    View
                                                </a>
                                                <button onclick="deleteFile('<?php echo $file['file_key']; ?>')"
                                                        class="flex-1 px-4 py-2 text-red-400 border border-red-400 rounded-lg hover:bg-red-400/10 transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php 
                                    endif;
                                endforeach;
                                if (!$has_videos):
                                ?>
                                    <div class="col-span-full text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-slate-400">No videos uploaded yet</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Particle configuration
        tsParticles.load("tsparticles", {
            fpsLimit: 60,
            particles: {
                number: {
                    value: 50,  // Reduced number for better dashboard performance
                    density: {
                        enable: true,
                        value_area: 1000
                    }
                },
                color: {
                    value: ["#818cf8", "#a78bfa", "#6366f1"]
                },
                shape: {
                    type: ["circle", "triangle"],
                    stroke: {
                        width: 1,
                        color: "#6366f1"
                    }
                },
                opacity: {
                    value: 0.3,  // Reduced opacity for less visual distraction
                    random: {
                        enable: true,
                        minimumValue: 0.1
                    },
                    animation: {
                        enable: true,
                        speed: 0.3,
                        minimumValue: 0.1,
                        sync: false
                    }
                },
                size: {
                    value: 3,
                    random: {
                        enable: true,
                        minimumValue: 1
                    },
                    animation: {
                        enable: true,
                        speed: 2,
                        minimumValue: 1,
                        sync: false
                    }
                },
                links: {
                    enable: true,
                    distance: 150,
                    color: "#6366f1",
                    opacity: 0.2,  // Reduced opacity for links
                    width: 1,
                    triangles: {
                        enable: true,
                        opacity: 0.05  // Reduced opacity for triangles
                    }
                },
                move: {
                    enable: true,
                    speed: 0.8,  // Slower movement
                    direction: "none",
                    random: true,
                    straight: false,
                    outModes: {
                        default: "bounce"
                    },
                    attract: {
                        enable: true,
                        rotateX: 1200,
                        rotateY: 1200
                    }
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onHover: {
                        enable: true,
                        mode: ["grab"]  // Simplified hover effect
                    },
                    onClick: {
                        enable: true,
                        mode: "push"
                    },
                    resize: true
                },
                modes: {
                    grab: {
                        distance: 180,
                        links: {
                            opacity: 0.5
                        }
                    },
                    push: {
                        quantity: 2  // Reduced quantity for less distraction
                    }
                }
            },
            background: {
                color: "transparent"
            },
            detectRetina: true,
            themes: [
                {
                    name: "night",
                    default: {
                        value: true,
                        mode: "dark"
                    }
                }
            ]
        });

        // Section Navigation
        const sections = ['upload', 'images', 'videos'];
        const navLinks = document.querySelectorAll('nav a');

        function showSection(sectionId) {
            // Hide all sections
            sections.forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            // Show selected section
            document.getElementById(sectionId).classList.remove('hidden');
            
            // Update active state in navigation
            navLinks.forEach(link => {
                const href = link.getAttribute('href').substring(1);
                if (href === sectionId) {
                    link.classList.add('bg-indigo-500/10', 'text-indigo-400');
                } else {
                    link.classList.remove('bg-indigo-500/10', 'text-indigo-400');
                    link.classList.add('hover:bg-slate-800/50');
                }
            });
        }

        // Add click event listeners to navigation links
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const sectionId = link.getAttribute('href').substring(1);
                showSection(sectionId);
                history.pushState(null, '', `#${sectionId}`);
            });
        });

        // Handle initial load and browser back/forward
        function handleHash() {
            const hash = window.location.hash.substring(1);
            if (hash && sections.includes(hash)) {
                showSection(hash);
            } else {
                showSection('upload'); // Default to upload section
            }
        }

        window.addEventListener('hashchange', handleHash);
        handleHash(); // Handle initial load

        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file');
        const defaultState = document.getElementById('defaultState');
        const previewGrid = document.getElementById('previewGrid');
        const uploadButton = document.getElementById('uploadButton');
        const selectedFiles = new Set();

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function createPreviewItem(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'preview-item';
                
                let previewContent = '';
                if (file.type.startsWith('image/')) {
                    previewContent = `<img src="${e.target.result}" alt="${file.name}" class="preview-image">`;
                } else if (file.type.startsWith('video/')) {
                    previewContent = `
                        <video class="preview-video w-full h-full object-cover" preload="metadata">
                            <source src="${e.target.result}" type="${file.type}">
                            Your browser does not support the video tag.
                        </video>`;
                }
                
                previewItem.innerHTML = `
                    ${previewContent}
                    <button type="button" class="remove-btn hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <div class="file-info">
                        <div class="truncate">${file.name}</div>
                        <div>${formatFileSize(file.size)}</div>
                    </div>
                `;

                const removeBtn = previewItem.querySelector('.remove-btn');
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedFiles.delete(file);
                    previewItem.remove();
                    updateUploadButton();
                    if (selectedFiles.size === 0) {
                        resetUploadState();
                    }
                });

                previewGrid.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        }

        function handleFiles(files) {
            if (files.length > 0) {
                defaultState.classList.add('hidden');
                previewGrid.classList.remove('hidden');
                dropZone.classList.add('has-files');
                
                Array.from(files).forEach(file => {
                    if (file.type.startsWith('image/') || file.type.startsWith('video/')) {
                        selectedFiles.add(file);
                        createPreviewItem(file);
                    }
                });
                
                updateUploadButton();
            }
        }

        function updateUploadButton() {
            if (selectedFiles.size > 0) {
                uploadButton.classList.remove('hidden');
                uploadButton.classList.add('upload-ready');
                uploadButton.querySelector('.upload-text').textContent = 
                    `Upload ${selectedFiles.size} ${selectedFiles.size === 1 ? 'File' : 'Files'}`;
            } else {
                uploadButton.classList.add('hidden');
                uploadButton.classList.remove('upload-ready');
            }
        }

        function resetUploadState() {
            defaultState.classList.remove('hidden');
            previewGrid.classList.add('hidden');
            dropZone.classList.remove('has-files');
            previewGrid.innerHTML = '';
            selectedFiles.clear();
            fileInput.value = '';
            updateUploadButton();
        }

        // Event Listeners
        dropZone.addEventListener('click', (e) => {
            if (!e.target.closest('.remove-btn')) {
                fileInput.click();
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('drag-over');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', () => {
            handleFiles(fileInput.files);
        });

        // Form submission with progress
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData();
            selectedFiles.forEach(file => {
                formData.append('file[]', file);
            });

            try {
                uploadButton.disabled = true;
                uploadButton.querySelector('.upload-text').classList.add('hidden');
                uploadButton.querySelector('.upload-progress').classList.remove('hidden');
                
                const response = await fetch('../api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (response.ok) {
                    alert('Files uploaded successfully!');
                    location.reload();
                } else {
                    throw new Error(data.message || 'Upload failed');
                }
            } catch (error) {
                alert('Upload failed: ' + error.message);
                uploadButton.disabled = false;
                uploadButton.querySelector('.upload-text').classList.remove('hidden');
                uploadButton.querySelector('.upload-progress').classList.add('hidden');
            }
        });

        // Search functionality
        document.getElementById('searchKey').addEventListener('input', (e) => {
            const searchValue = e.target.value.toLowerCase();
            document.querySelectorAll('.file-card').forEach(card => {
                const filename = card.dataset.filename;
                const matches = filename.includes(searchValue);
                card.style.display = matches ? '' : 'none';
                
                // Show/hide "No files" message in each section
                const section = card.closest('.grid');
                const noFilesMessage = section.querySelector('.col-span-full');
                if (noFilesMessage) {
                    const visibleCards = Array.from(section.querySelectorAll('.file-card')).some(c => c.style.display !== 'none');
                    noFilesMessage.style.display = visibleCards ? 'none' : 'block';
                }
            });
        });

        // Delete file
        async function deleteFile(key) {
            if (!confirm('Are you sure you want to delete this file?')) return;
            
            try {
                const response = await fetch(`../api/delete.php?key=${key}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                
                if (response.ok) {
                    alert('File deleted successfully!');
                    location.reload();
                } else {
                    alert('Delete failed: ' + data.message);
                }
            } catch (error) {
                alert('Delete failed: ' + error.message);
            }
        }

        // Add these new functions for rename functionality
        function startRename(button, currentName, fileKey) {
            const card = button.closest('.file-card');
            const displayElement = card.querySelector('.rename-display');
            const formElement = card.querySelector('.rename-form');
            const inputElement = formElement.querySelector('.rename-input');
            
            // Hide display, show form
            displayElement.classList.add('hidden');
            formElement.classList.remove('hidden');
            
            // Set current name in input (without extension)
            const nameWithoutExt = currentName.substring(0, currentName.lastIndexOf('.'));
            inputElement.value = nameWithoutExt;
            inputElement.focus();
            
            // Add form submit handler
            formElement.onsubmit = async (e) => {
                e.preventDefault();
                const newName = inputElement.value.trim();
                if (!newName) return;
                
                try {
                    const extension = currentName.substring(currentName.lastIndexOf('.'));
                    const response = await fetch('../api/rename.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            key: fileKey,
                            new_name: newName + extension
                        })
                    });
                    
                    const data = await response.json();
                    if (response.ok) {
                        // Update display
                        displayElement.textContent = newName + extension;
                        displayElement.title = newName + extension;
                        cancelRename(formElement.querySelector('button[type="button"]'));
                    } else {
                        throw new Error(data.message || 'Rename failed');
                    }
                } catch (error) {
                    alert('Failed to rename file: ' + error.message);
                }
            };
        }

        function cancelRename(button) {
            const card = button.closest('.file-card');
            const displayElement = card.querySelector('.rename-display');
            const formElement = card.querySelector('.rename-form');
            
            // Show display, hide form
            displayElement.classList.remove('hidden');
            formElement.classList.add('hidden');
        }
    </script>
</body>
</html> 