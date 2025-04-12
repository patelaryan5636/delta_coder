<?php
// session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Created Successfully</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        @import url('https://fonts.cdnfonts.com/css/agbalumo');
        .fam {
            font-family: 'Agbalumo', sans-serif !important;
        }
        body {
            background-color: #ffffff;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='88' height='88' viewBox='0 0 88 88'%3E%3Cg fill='%23c1dedf' fill-opacity='0.08'%3E%3Cpath fill-rule='evenodd' d='M29.42 29.41c.36-.36.58-.85.58-1.4V0h-4v26H0v4h28c.55 0 1.05-.22 1.41-.58h.01zm0 29.18c.36.36.58.86.58 1.4V88h-4V62H0v-4h28c.56 0 1.05.22 1.41.58zm29.16 0c-.36.36-.58.85-.58 1.4V88h4V62h26v-4H60c-.55 0-1.05.22-1.41.58h-.01zM62 26V0h-4v28c0 .55.22 1.05.58 1.41.37.37.86.59 1.41.59H88v-4H62zM18 36c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H20a2 2 0 0 1-2-2zm0 16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H20a2 2 0 0 1-2-2zm16-16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H36a2 2 0 0 1-2-2zm16 16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H52a2 2 0 0 1-2-2zm-8-16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H44a2 2 0 0 1-2-2zm16 16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H60a2 2 0 0 1-2-2zm-8-16c0-1.1.9-2 2-2h10a2 2 0 1 1 0 4H52a2 2 0 0 1-2-2z'/%3E%3C/g%3E%3C/svg%3E");
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-8 max-w-6xl">
        <div class="bg-white rounded-lg shadow-sm p-8 max-w-2xl mx-auto">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ri-check-line text-green-500 text-3xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2 fam">Group Created Successfully!</h1>
                <p class="text-gray-600" id="createdAt"></p>
            </div>

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold mb-3">Group Details</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-600">Group Name</label>
                            <p class="mt-1 text-gray-900" id="groupName"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Description</label>
                            <p class="mt-1 text-gray-900" id="groupDescription"></p>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-semibold mb-3">Group Members</h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <ul class="divide-y divide-gray-200" id="membersList">
                        </ul>
                    </div>
                </div>

                <div class="flex gap-4 justify-center pt-4">
                    <a href="index.php" class="bg-gray-100 text-gray-600 px-6 py-2 rounded hover:bg-gray-200 transition-colors">
                        Back to Home
                    </a>
                    <a href="tripform.php" class="bg-primary text-white px-6 py-2 rounded hover:bg-primary/90 transition-colors">
                        Create Another Group
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get group data from session storage
            const groupData = JSON.parse(sessionStorage.getItem('groupData'));
            
            if (!groupData) {
                // window.location.href = 'index.php';
                return;
            }

            // Format date
            const date = new Date(groupData.created_at);
            const formattedDate = date.toLocaleString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZoneName: 'short'
            });

            // Populate group details
            document.getElementById('createdAt').textContent = `Created on ${formattedDate}`;
            document.getElementById('groupName').textContent = groupData.groupName;
            document.getElementById('groupDescription').textContent = groupData.groupDescription || 'No description provided';

            // Populate members list
            const membersList = document.getElementById('membersList');
            groupData.members.forEach(member => {
                const li = document.createElement('li');
                li.className = 'py-3 flex justify-between items-center';
                li.innerHTML = `
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <i class="ri-user-line text-gray-500"></i>
                        </div>
                        <div>
                            <p class="font-medium">${member.username}</p>
                            <p class="text-sm text-gray-500">${member.role}</p>
                        </div>
                    </div>
                `;
                membersList.appendChild(li);
            });

            // Clear session storage
            // sessionStorage.removeItem('groupData');
        });
    </script>
</body>
</html>