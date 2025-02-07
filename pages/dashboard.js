import { useState, useEffect } from 'react';
import { useTheme } from 'next-themes';
import { useDropzone } from 'react-dropzone';
import { motion, AnimatePresence } from 'framer-motion';

export default function Dashboard() {
  const [files, setFiles] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [uploadProgress, setUploadProgress] = useState(0);
  const [selectedFile, setSelectedFile] = useState(null);
  const { theme, setTheme } = useTheme();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
    fetchFiles();
  }, []);

  const fetchFiles = async () => {
    try {
      const response = await fetch('/api/files');
      const data = await response.json();
      setFiles(data);
    } catch (error) {
      console.error('Error fetching files:', error);
    }
  };

  const onDrop = async (acceptedFiles) => {
    const file = acceptedFiles[0];
    const formData = new FormData();
    formData.append('file', file);

    try {
      setUploadProgress(0);
      const xhr = new XMLHttpRequest();
      
      xhr.upload.onprogress = (event) => {
        if (event.lengthComputable) {
          const progress = (event.loaded / event.total) * 100;
          setUploadProgress(progress);
        }
      };

      xhr.onload = async () => {
        if (xhr.status === 201) {
          const response = JSON.parse(xhr.responseText);
          alert('File uploaded successfully!');
          fetchFiles();
        } else {
          alert('Upload failed: ' + xhr.responseText);
        }
        setUploadProgress(0);
      };

      xhr.open('POST', '/api/upload.php');
      xhr.send(formData);
    } catch (error) {
      alert('Upload failed: ' + error.message);
      setUploadProgress(0);
    }
  };

  const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop });

  const deleteFile = async (key) => {
    if (!confirm('Are you sure you want to delete this file?')) return;

    try {
      const response = await fetch(`/api/delete.php?key=${key}`, {
        method: 'DELETE'
      });
      const data = await response.json();
      
      if (response.ok) {
        alert('File deleted successfully!');
        fetchFiles();
      } else {
        alert('Delete failed: ' + data.message);
      }
    } catch (error) {
      alert('Delete failed: ' + error.message);
    }
  };

  const openFilePreview = (file) => {
    setSelectedFile(file);
  };

  const filteredFiles = files.filter(file => 
    file.file_key.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
      <nav className="bg-white dark:bg-gray-800 shadow-sm">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900 dark:text-white">
              File Dashboard
            </h1>
            <button
              onClick={() => setTheme(theme === 'dark' ? 'light' : 'dark')}
              className="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 transition-colors"
            >
              {mounted && theme === 'dark' ? '🌞' : '🌙'}
            </button>
          </div>
        </div>
      </nav>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Upload Section */}
        <div {...getRootProps()} className="mb-8">
          <input {...getInputProps()} />
          <div className={`p-8 border-2 border-dashed rounded-lg text-center cursor-pointer transition-colors duration-200 ${
            isDragActive 
              ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' 
              : 'border-gray-300 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-500'
          }`}>
            <svg className="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
            </svg>
            <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
              {isDragActive ? 'Drop the file here' : 'Drag and drop a file, or click to select'}
            </p>
          </div>
          {uploadProgress > 0 && (
            <div className="mt-4">
              <div className="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div 
                  className="bg-blue-600 h-2 rounded-full transition-all duration-200"
                  style={{ width: `${uploadProgress}%` }}
                />
              </div>
              <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                Uploading: {Math.round(uploadProgress)}%
              </p>
            </div>
          )}
        </div>

        {/* Search Box */}
        <div className="mb-6">
          <div className="relative">
            <input
              type="text"
              placeholder="Search by file key..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-200"
            />
            <svg className="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
        </div>

        {/* Files Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <AnimatePresence>
            {filteredFiles.map((file) => (
              <motion.div
                key={file.file_key}
                layout
                initial={{ opacity: 0, scale: 0.9 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.9 }}
                className="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200"
              >
                <div className="p-4">
                  <div className="aspect-w-16 aspect-h-9 mb-4">
                    {file.file_type.match(/^(jpg|jpeg|png|gif|webp)$/i) ? (
                      <img
                        src={`/uploads/${file.file_key}.${file.file_type}`}
                        alt={file.file_name}
                        className="rounded-lg object-cover cursor-pointer"
                        onClick={() => openFilePreview(file)}
                      />
                    ) : (
                      <div className="flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg className="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                      </div>
                    )}
                  </div>
                  <div className="space-y-2">
                    <p className="font-medium text-gray-900 dark:text-white truncate">
                      {file.file_name}
                    </p>
                    <p className="text-sm text-gray-500 dark:text-gray-400">
                      {(file.file_size / 1024).toFixed(2)} KB
                    </p>
                    <div className="flex space-x-2">
                      <button
                        onClick={() => openFilePreview(file)}
                        className="flex-1 px-3 py-1 text-sm text-blue-600 dark:text-blue-400 border border-blue-600 dark:border-blue-400 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-200"
                      >
                        View
                      </button>
                      <button
                        onClick={() => deleteFile(file.file_key)}
                        className="flex-1 px-3 py-1 text-sm text-red-600 dark:text-red-400 border border-red-600 dark:border-red-400 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </div>
              </motion.div>
            ))}
          </AnimatePresence>
        </div>

        {/* File Preview Modal */}
        {selectedFile && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white dark:bg-gray-800 rounded-lg p-4 max-w-4xl w-full mx-4">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-medium text-gray-900 dark:text-white">
                  {selectedFile.file_name}
                </h3>
                <button
                  onClick={() => setSelectedFile(null)}
                  className="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                >
                  <svg className="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              {selectedFile.file_type.match(/^(jpg|jpeg|png|gif|webp)$/i) ? (
                <img
                  src={`/uploads/${selectedFile.file_key}.${selectedFile.file_type}`}
                  alt={selectedFile.file_name}
                  className="max-h-[70vh] mx-auto rounded-lg"
                />
              ) : (
                <div className="text-center py-8">
                  <p className="text-gray-500 dark:text-gray-400">
                    No preview available for this file type
                  </p>
                </div>
              )}
            </div>
          </div>
        )}
      </main>
    </div>
  );
} 