<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = $_GET['group_id'] ?? null;

if (!$group_id) {
    die("Group ID is missing.");
}

// Check user role in the group
$role_sql = "SELECT role FROM user_group_roles WHERE group_id = ? AND user_id = ?";
$role_stmt = $conn->prepare($role_sql);
$role_stmt->bind_param("ii", $group_id, $user_id);
$role_stmt->execute();
$role_result = $role_stmt->get_result();
$role = $role_result->fetch_assoc();

if (!$role || in_array(strtolower($role['role']), ['owner', 'admin'])) {
    die("Access denied: Only group members can view this page.");
}

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
    // Fetch tasks assigned to this user for this group
    $sql = "
        SELECT ci.id, ci.item_name, ci.status, ci.note, cc.category_name
        FROM checklist_items ci
        JOIN checklist_categories cc ON ci.category_id = cc.cc_id
        WHERE ci.assigned_to = ? AND cc.group_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($task = $result->fetch_assoc()) {
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
