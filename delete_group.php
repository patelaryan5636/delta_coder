<?php
session_start();
require 'includes/scripts/connection.php'; // adjust the path as needed

// Check if user is logged in
if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($group_id <= 0) {
    die("Invalid group ID.");
}

// Check if current user is the Owner of the group
$checkOwner = $conn->prepare("SELECT role FROM user_group_roles WHERE user_id = ? AND group_id = ? AND role = 'Owner'");
$checkOwner->bind_param("ii", $user_id, $group_id);
$checkOwner->execute();
$ownerResult = $checkOwner->get_result();

if ($ownerResult->num_rows === 0) {
    die("You are not authorized to delete this group.");
}

// Delete group members first due to foreign key constraints
$conn->query("DELETE FROM user_group_roles WHERE group_id = $group_id");

// Then delete the group itself
$conn->query("DELETE FROM group_master WHERE group_id = $group_id");

// Redirect after deletion
header("Location: cardlist.php?msg=Group+Deleted");
exit;
?>
