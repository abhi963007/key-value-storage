<!DOCTYPE html>
<html>
<head>
    <title>API Testing Interface</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .response { background: #f5f5f5; padding: 10px; margin-top: 10px; white-space: pre-wrap; }
        button { padding: 10px; margin: 5px; }
        input[type="file"], input[type="text"] { margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Key-Value Storage System API Testing Interface</h1>

    <!-- Upload Test Section -->
    <div class="test-section">
        <h2>1. Test Upload API</h2>
        <form id="uploadForm">
            <input type="file" id="uploadFile" required>
            <button type="submit">Test Upload</button>
        </form>
        <div id="uploadResponse" class="response"></div>
    </div>

    <!-- View Test Section -->
    <div class="test-section">
        <h2>2. Test View API</h2>
        <input type="text" id="viewFileName" placeholder="Enter file key">
        <button onclick="testView()">Test View</button>
        <div id="viewResponse" class="response"></div>
    </div>

    <!-- Rename Test Section -->
    <div class="test-section">
        <h2>3. Test Rename API</h2>
        <input type="text" id="oldFileName" placeholder="Enter file key">
        <input type="text" id="newFileName" placeholder="Enter new name">
        <button onclick="testRename()">Test Rename</button>
        <div id="renameResponse" class="response"></div>
    </div>

    <!-- Delete Test Section -->
    <div class="test-section">
        <h2>4. Test Delete API</h2>
        <input type="text" id="deleteFileName" placeholder="Enter file key">
        <button onclick="testDelete()">Test Delete</button>
        <div id="deleteResponse" class="response"></div>
    </div>

    <script>
        // Helper function to display response
        function displayResponse(elementId, response) {
            const responseElement = document.getElementById(elementId);
            responseElement.textContent = JSON.stringify(response, null, 2);
        }

        // Upload Test
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData();
            const file = document.getElementById('uploadFile').files[0];
            formData.append('file', file);

            try {
                const response = await fetch('api/upload.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                displayResponse('uploadResponse', data);
            } catch (error) {
                displayResponse('uploadResponse', { error: error.message });
            }
        });

        // View Test
        async function testView() {
            const fileKey = document.getElementById('viewFileName').value;
            try {
                const response = await fetch(`api/view.php?key=${encodeURIComponent(fileKey)}`);
                const data = await response.json();
                displayResponse('viewResponse', data);
            } catch (error) {
                displayResponse('viewResponse', { error: error.message });
            }
        }

        // Rename Test
        async function testRename() {
            const key = document.getElementById('oldFileName').value;
            const newName = document.getElementById('newFileName').value;
            
            try {
                const response = await fetch('api/rename.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        key: key,
                        new_name: newName
                    })
                });
                const data = await response.json();
                displayResponse('renameResponse', data);
            } catch (error) {
                displayResponse('renameResponse', { error: error.message });
            }
        }

        // Delete Test
        async function testDelete() {
            const fileKey = document.getElementById('deleteFileName').value;
            try {
                const response = await fetch(`api/delete.php?key=${encodeURIComponent(fileKey)}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                displayResponse('deleteResponse', data);
            } catch (error) {
                displayResponse('deleteResponse', { error: error.message });
            }
        }
    </script>
</body>
</html> 