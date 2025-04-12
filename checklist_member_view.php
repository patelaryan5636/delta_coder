<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;

if (!$group_id) {
    die("Group ID is missing.");
}

// Get role directly (no prepared statements)
$role_sql = "SELECT role FROM user_group_roles WHERE group_id = $group_id AND user_id = $user_id";
$role_result = mysqli_query($conn, $role_sql);

if (!$role_result || mysqli_num_rows($role_result) == 0) {
    die("Access denied: You are not part of this group.");
}

$role = mysqli_fetch_assoc($role_result);

// Optional: If you want only members (not admin/owner) to view, uncomment this:
// if (in_array(strtolower($role['role']), ['owner', 'admin'])) {
//     die("Access denied: Only members can view this page.");
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .container {
            max-width: 850px;
            margin: auto;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.08);
        }
        h2 {
            text-align: center;
            color: #0a657a;
        }
        .task-card {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .task-card:last-child {
            border-bottom: none;
        }
        .status-select {
            padding: 6px 10px;
            margin-left: 10px;
            border-radius: 4px;
        }
        .note-input {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .btn-save {
            background: #0a657a;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>My Assigned Tasks</h2>

    <?php
    // Fetch tasks using plain query
    $task_sql = "
        SELECT ci.id, ci.item_name, ci.status, ci.note, cc.category_name
        FROM checklist_items ci
        JOIN checklist_categories cc ON ci.category_id = cc.cc_id
        WHERE ci.assigned_to = $user_id AND cc.group_id = $group_id
    ";
    $task_result = mysqli_query($conn, $task_sql);

    if ($task_result && mysqli_num_rows($task_result) > 0) {
        while ($task = mysqli_fetch_assoc($task_result)) {
            ?>
            <form method="post" action="update_task_status.php">
                <div class="task-card">
                    <strong>Task:</strong> <?= htmlspecialchars($task['item_name']) ?><br>
                    <strong>Category:</strong> <?= htmlspecialchars($task['category_name']) ?><br>

                    <label>Status:
                        <select class="status-select" name="status">
                            <option value="Not Started" <?= $task['status'] === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                            <option value="Packed" <?= $task['status'] === 'Packed' ? 'selected' : '' ?>>Packed</option>
                            <option value="Delivered" <?= $task['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        </select>
                    </label><br>

                    <label>Note:</label>
                    <textarea class="note-input" name="note" rows="3"><?= htmlspecialchars($task['note']) ?></textarea><br>

                    <input type="hidden" name="item_id" value="<?= $task['id'] ?>">
                    <button class="btn-save" type="submit">Save</button>
                </div>
            </form>
            <?php
        }
    } else {
        echo "<p>You have no assigned tasks.</p>";
    }
    ?>
</div>
</body>
</html>
