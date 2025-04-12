<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    die("Group ID missing.");
}

// Check if the user is a viewer in this group
$check_role = $conn->prepare("SELECT role FROM user_group_roles WHERE group_id = ? AND user_id = ?");
$check_role->bind_param("ii", $group_id, $user_id);
$check_role->execute();
$role_result = $check_role->get_result();
$user_role = $role_result->fetch_assoc()['role'] ?? null;

if ($user_role !== 'Viewer') {
    die("Access denied. Only viewers can access this page.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checklist Viewer</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 960px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0a657a;
        }
        .task-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        .task-table th, .task-table td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        .task-table th {
            background-color: #0a657a;
            color: white;
        }
        .status {
            font-weight: bold;
        }
        .note-box {
            background: #f9f9f9;
            border-left: 4px solid #0a657a;
            padding: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Checklist - Viewer Access</h2>

    <table class="task-table">
        <tr>
            <th>Task</th>
            <th>Category</th>
            <th>Assigned To</th>
            <th>Status</th>
            <th>Note</th>
        </tr>
        <?php
        $query = "
            SELECT ci.item_name, ci.status, ci.note, um.user_name, cc.category_name 
            FROM checklist_items ci 
            JOIN checklist_categories cc ON ci.category_id = cc.cc_id 
            LEFT JOIN user_master um ON ci.assigned_to = um.user_id 
            WHERE cc.group_id = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $results = $stmt->get_result();

        while ($row = $results->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['item_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
            echo "<td>" . ($row['user_name'] ?? 'Unassigned') . "</td>";
            echo "<td class='status'>" . $row['status'] . "</td>";
            echo "<td><div class='note-box'>" . nl2br(htmlspecialchars($row['note'])) . "</div></td>";
            echo "</tr>";
        }

        if ($results->num_rows === 0) {
            echo "<tr><td colspan='5'>No tasks available in this group.</td></tr>";
        }

        ?>
    </table>
</div>
</body>
</html>
