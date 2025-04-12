<?php
// get_users.php

require_once 'includes/scripts/connection.php'; // Database connection

$sql = "SELECT user_id, user_name FROM user_master";
$result = $conn->query($sql);

$users = [];
while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}
header('Content-Type: application/json');
echo json_encode($users);
?>
