<?php
session_start();
include '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);
    $owner_user_id = $_SESSION['user_id']; // Assuming user is logged in and ID is stored in session

    if (empty($group_name) || empty($group_description)) {
        die("<script>alert('Group name and description are required.'); window.history.back();</script>");
    }

    // Collect members and roles
    $members = $_POST['member'] ?? [];
    $roles = $_POST['role'] ?? [];

    $valid_members = [];
    $admin_count = 0;

    for ($i = 0; $i < count($members); $i++) {
        $user_id = $members[$i];
        $role = $roles[$i];

        // Skip empty rows
        if (empty($user_id) && empty($role)) {
            continue;
        }

        // Both fields must be filled
        if (empty($user_id) || empty($role)) {
            die("<script>alert('Each member must have both a user and a role.'); window.history.back();</script>");
        }

        // Prevent self from being added
        if ($user_id == $owner_user_id) {
            die("<script>alert('You cannot select yourself as a group member.'); window.history.back();</script>");
        }

        if ($role == "admin") {
            $admin_count++;
        }

        $valid_members[] = [
            'user_id' => $user_id,
            'role' => $role
        ];
    }

    if (count($valid_members) === 0) {
        die("<script>alert('At least one member must be selected.'); window.history.back();</script>");
    }

    if ($admin_count > 1) {
        die("<script>alert('Only one admin is allowed (besides you).'); window.history.back();</script>");
    }

    // Insert into group_master
    $stmt = $conn->prepare("INSERT INTO group_master (group_name, group_description, created_by) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $group_name, $group_description, $owner_user_id);
    $stmt->execute();
    $group_id = $stmt->insert_id;
    $stmt->close();

    // Insert owner as admin in group_user_roles
    $stmt2 = $conn->prepare("INSERT INTO group_user_roles (group_id, user_id, role) VALUES (?, ?, ?)");
    $owner_role = "admin";
    $stmt2->bind_param("iis", $group_id, $owner_user_id, $owner_role);
    $stmt2->execute();

    // Insert each member
    foreach ($valid_members as $member) {
        $stmt2->bind_param("iis", $group_id, $member['user_id'], $member['role']);
        $stmt2->execute();
    }

    $stmt2->close();
    $conn->close();

    echo "<script>alert('Group created successfully!'); window.location.href='group_list.php';</script>";
} else {
    echo "Invalid Request.";
}
?>

