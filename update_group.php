<?php
require 'includes/scripts/connection.php';  

$group_id = isset($_POST['group_id']) ? intval($_POST['group_id']) : 0;
if ($group_id <= 0) {
    die("Invalid group ID.");
}   
$group_name = $_POST['group_name'];
$group_desc = $_POST['group_description'];

// Update group details
$conn->query("UPDATE group_master SET group_name='$group_name', group_description='$group_desc' WHERE group_id=$group_id");

// Delete old members
$conn->query("DELETE FROM user_group_roles WHERE group_id=$group_id");

// Re-insert members
$members = $_POST['members']; // array of user_ids
$roles = $_POST['roles'];     // array of roles

for ($i = 0; $i < count($members); $i++) {
    $user_id = intval($members[$i]);
    $role = $roles[$i];
    $conn->query("INSERT INTO user_group_roles (group_id, user_id, role) VALUES ($group_id, $user_id, '$role')");
}

header("Location: cardlist.php"); // redirect back
exit;
