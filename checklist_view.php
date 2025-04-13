    <?php
    include 'includes/scripts/connection.php';
    session_start();

    if (!isset($_SESSION['pacpal_logedin_user_id'])) {
        die("Unauthorized access");
    }

    $user_id = $_SESSION['pacpal_logedin_user_id'];
    $group_id = $_GET['group_id'] ?? null;

    if (!$group_id) {
        die("Group ID missing.");
    }

    // Check if user is owner/admin of group
    $checkRole = $conn->prepare("SELECT role FROM user_group_roles WHERE group_id = ? AND user_id = ?");
    $checkRole->bind_param("ii", $group_id, $user_id);
    $checkRole->execute();
    $roleResult = $checkRole->get_result();
    $userRole = $roleResult->fetch_assoc();

    if (!$userRole || !in_array($userRole['role'], ['Owner', 'admin'])) {
        header("Access denied.");
    }

    $sql = "SELECT ci.*, um.user_name AS user_name, cc.category_name
            FROM checklist_items ci
            LEFT JOIN user_master um ON ci.assigned_to = um.user_id
            LEFT JOIN checklist_categories cc ON ci.category_id = cc.cc_id
            WHERE ci.category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();


    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Task Management Dashboard</title>
        <script src="https://cdn.tailwindcss.com/3.4.16"></script>
        <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#f97316'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
        <style>
            @import url('https://fonts.cdnfonts.com/css/agbalumo');
            :where([class^="ri-"])::before { content: "\f3c2"; }
            body {
                font-family: 'Inter', sans-serif;
            }
            .status-pill {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
            }
            .status-on-track {
                background-color: rgba(34, 197, 94, 0.1);
                color: rgb(22, 163, 74);
            }
            .status-at-risk {
                background-color: rgba(245, 158, 11, 0.1);
                color: rgb(217, 119, 6);
            }
            .task-icon {
                width: 24px;
                height: 24px;
                border-radius: 4px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
            }
            .task-row:hover {
                background-color: rgba(243, 244, 246, 0.5);
            }

            .title{
                font-size: 3vw;
                font-family: 'Agbalumo', sans-serif;
                text-align: center;
            }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto p-4 sm:p-6">
            <h1 class="title pb-4">Tack Your Tasks</h1>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-white">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                                    Category
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-sm font-medium text-gray-500">
                                    Assign-To
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-sm font-medium text-gray-500">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Task 1 -->
                            <?php 
                            $categories = $conn->prepare("SELECT * FROM checklist_categories WHERE group_id = ?");
                            $categories->bind_param("i", $group_id);
                            $categories->execute();
                            $cat_result = $categories->get_result();
                            
                            while ($cat = $cat_result->fetch_assoc()) {
                                $cc_id = $cat['cc_id'];

                            $items = $conn->prepare("SELECT ci.*, um.user_name FROM checklist_items ci 
                            LEFT JOIN user_master um ON ci.assigned_to = um.user_id
                            WHERE ci.category_id = ?");
                            $items->bind_param("i", $cc_id);
                            $items->execute();
                            $item_result = $items->get_result();
                            while ($item = $item_result->fetch_assoc()) {
                                
                            ?>

                            <tr class="task-row">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-gray-900"><?php echo $item['item_name'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm text-gray-900"><?php echo $cat['category_name'] ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden mr-3">
                                            <img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20male%20manager%20with%20short%20dark%20hair%2C%20business%20attire%2C%20neutral%20background%2C%20professional%20lighting&width=100&height=100&seq=1&orientation=squarish" alt="Dave Jung" class="h-full w-full object-cover">
                                        </div>
                                        <div class="text-sm text-gray-900"><?php echo  $item['user_name'] ?? 'Unassigned' ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    
                                    <div class="status-pill status-at-risk">
                                        <div class="w-2 h-2 bg-orange-500 rounded-full mr-2"></div>
                                        <span><?php echo $item['status'] ?></span>
                                    </div>
                                </td>
                            </tr>
                            <?php }  
                            }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add functionality for row hover effects
                const taskRows = document.querySelectorAll('.task-row');
                taskRows.forEach(row => {
                    row.addEventListener('click', function() {
                        console.log('Row clicked');
                    });
                });
            });
        </script>
    </body>
    </html>