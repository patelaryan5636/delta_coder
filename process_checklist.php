<?php
include 'includes/scripts/connection.php';
session_start();

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$admin_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = $_POST['group_id'];
$category_id = $_POST['category_id'];
$item_name = trim($_POST['item_name']);
$assigned_to = $_POST['assigned_to'];
$status = $_POST['status'];
$note = trim($_POST['note'] ?? '');
$group_date = $_POST['group_date'];
$group_time = $_POST['group_time'];

// Combine date + time into MySQL DATETIME format
$due_datetime = date('Y-m-d H:i:s', strtotime("$group_date $group_time"));

// 1️⃣ Get group owner
$getOwner = $conn->prepare("SELECT user_id FROM user_group_roles WHERE group_id = ? AND role = 'Owner'");
$getOwner->bind_param("i", $group_id);
$getOwner->execute();
$owner_result = $getOwner->get_result();
$owner = $owner_result->fetch_assoc();
$owner_id = $owner['user_id'];
$getOwner->close();

// 2️⃣ Prevent assigning task to self (admin) or owner
if ($assigned_to == $admin_id) {
    die("<script>alert('Admins cannot assign tasks to themselves.'); window.history.back();</script>");
}
if ($assigned_to == $owner_id) {
    die("<script>alert('Admins cannot assign tasks to the group owner.'); window.history.back();</script>");
}

// 3️⃣ Check category exists
$checkCategory = $conn->prepare("SELECT * FROM checklist_categories WHERE cc_id = ?");
$checkCategory->bind_param("i", $category_id);
$checkCategory->execute();
$result = $checkCategory->get_result();
if ($result->num_rows === 0) {
    die("<script>alert('Invalid category selected.'); window.history.back();</script>");
}
$checkCategory->close();

// 4️⃣ Insert into checklist_items
$stmt = $conn->prepare("INSERT INTO checklist_items 
    (category_id, item_name, assigned_to, status, note, due_time)
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isisss", $category_id, $item_name, $assigned_to, $status, $note, $due_datetime);

if ($stmt->execute()) {
    echo "<script>alert('Checklist item assigned successfully.'); window.location.href='checklist_view.php?group_id=$group_id';</script>";
} else {
    echo "<script>alert('Something went wrong.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
