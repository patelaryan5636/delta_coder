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

// if (!$userRole || !in_array($userRole['role'], ['Owner', 'admin'])) {
//     die("Access denied.");
// }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Checklist Tracker</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 960px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #0a657a;
        }
        .category-box {
            background: #fff;
            border-left: 5px solid #0a657a;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .task {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .task:last-child {
            border-bottom: none;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: #fff;
            display: inline-block;
        }
        .Not\ Started { background: #6c757d; }
        .Packed { background: #ffc107; }
        .Delivered { background: #28a745; }

        .progress-container {
            margin: 10px 0 15px;
            background: #e9ecef;
            height: 10px;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 100%;
            background: #0a657a;
        }
        .note {
            font-size: 14px;
            color: #555;
            margin-top: 4px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Checklist Progress Tracker</h2>

    <?php
    // Get all categories
    $categories = $conn->prepare("SELECT * FROM checklist_categories WHERE group_id = ?");
    $categories->bind_param("i", $group_id);
    $categories->execute();
    $cat_result = $categories->get_result();

    while ($cat = $cat_result->fetch_assoc()) {
        $cc_id = $cat['cc_id'];
        echo "<div class='category-box'>";
        echo "<h3>{$cat['category_name']}</h3>";

        // Count progress
        $count = $conn->prepare("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Delivered' THEN 1 ELSE 0 END) as completed
            FROM checklist_items WHERE category_id = ?");
        $count->bind_param("i", $cc_id);
        $count->execute();
        $count_result = $count->get_result()->fetch_assoc();
        $total = $count_result['total'];
        $completed = $count_result['completed'];
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        echo "<div class='progress-container'><div class='progress-bar' style='width: {$percentage}%;'></div></div>";
        echo "<small>{$percentage}% Complete</small>";

        // Fetch items
        $items = $conn->prepare("SELECT ci.*, um.user_name FROM checklist_items ci 
            LEFT JOIN user_master um ON ci.assigned_to = um.user_id
            WHERE ci.category_id = ?");
        $items->bind_param("i", $cc_id);
        $items->execute();
        $item_result = $items->get_result();

        while ($item = $item_result->fetch_assoc()) {
            echo "<div class='task'>";
            echo "<strong>{$item['item_name']}</strong><br>";
            echo "<span>Assigned to: " . ($item['user_name'] ?? 'Unassigned') . "</span> ";
            echo "<span class='badge {$item['status']}'>{$item['status']}</span>";
            if (!empty($item['note'])) {
                echo "<div class='note'>📝 {$item['note']}</div>";
            }
            echo "</div>";
        }

        echo "</div>";
    }
    ?>
</div>
</body>
</html>
