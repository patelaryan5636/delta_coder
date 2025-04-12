<?php
session_start();
require_once 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    header("Location: sign-in.php");
    exit;
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = $_GET['group_id'] ?? null;

// Check if the user is admin in the group
$checkAdmin = mysqli_query($conn, "SELECT * FROM user_group_roles WHERE user_id = $user_id AND group_id = $group_id AND role = 'admin'");
if (mysqli_num_rows($checkAdmin) == 0) {
    die("<script>alert('Only Admins can assign tasks.'); window.location.href='dashboard.php';</script>");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Assign Checklist Task</title>
</head>
<body>
  <h2>Assign Task in Group</h2>
  <form action="process_checklist.php" method="POST">
    <input type="hidden" name="group_id" value="<?= $group_id ?>">

    <label>Category:</label>
    <select name="category_id" required>
    <option value="">Select Category</option>
    <?php
    $catRes = mysqli_query($conn, "SELECT * FROM checklist_categories WHERE group_id = $group_id");
    while ($cat = mysqli_fetch_assoc($catRes)) {
        echo "<option value='{$cat['cc_id']}'>{$cat['category_name']}</option>";
    }
    ?>
</select><br><br>

    <label>Task Name:</label>
    <input type="text" name="item_name" required><br><br>

    <label>Note:</label>
    <textarea name="note" rows="3"></textarea><br><br>

    <label>Assign To:</label>
    <select name="assigned_to" required>
      <option value="">Select</option>
      <?php
        $members = mysqli_query($conn, "SELECT u.user_id, u.user_name FROM user_master u JOIN user_group_roles ugr ON u.user_id = ugr.user_id WHERE ugr.group_id = $group_id");
        while ($user = mysqli_fetch_assoc($members)) {
            echo "<option value='{$user['user_id']}'>{$user['user_name']}</option>";
        }
      ?>
    </select><br><br>

    <input type="submit" value="Assign Task">
  </form>
</body>
</html>
