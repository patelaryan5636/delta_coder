<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    header("Location: sign-in.php");
    exit;
}

$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
$user_id = $_SESSION['pacpal_logedin_user_id'];

// Only owner can access
$checkOwner = mysqli_query($conn, "SELECT * FROM user_group_roles WHERE group_id = $group_id AND user_id = $user_id AND role = 'Owner'");
if (mysqli_num_rows($checkOwner) == 0) {
    die("Only the group owner can add categories.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO checklist_categories (group_id, category_name) VALUES (?, ?)");
        $stmt->bind_param("is", $group_id, $category_name);
        $stmt->execute();
        $stmt->close();
        echo "<script>alert('Category added!'); window.location.href='assign_checklist.php?group_id=$group_id';</script>";
    } else {
        echo "<script>alert('Category name is required.');</script>";
    }
}
?>

<form method="post">
    <label>Category Name:</label>
    <input type="text" name="category_name" required>
    <button type="submit">Add Category</button>
</form>
