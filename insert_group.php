<?php
session_start();
include 'includes/scripts/connection.php';

// Check if user is logged in
if (isset($_SESSION['pacpal_logedin_user_id']) && trim($_SESSION['pacpal_logedin_user_id']) !== '') {
    $user_id = $_SESSION['pacpal_logedin_user_id'];
    $query = "SELECT * FROM user_master WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $userdata = mysqli_fetch_assoc($result);
    $user_role = $userdata["user_role"];

    // Only admin (role 3) allowed to create group
    if ($user_role != 3) {
        header("Location: 404.php");
        exit;
    }
} else {
    header("Location: sign-in.php");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $group_name = trim($_POST['group_name']);
    $group_description = trim($_POST['group_description']);
    $owner_user_id = $user_id;

    if (empty($group_name) || empty($group_description)) {
        die("<script>alert('Group name and description are required.'); window.history.back();</script>");
    }

    $members = $_POST['members'] ?? [];

    $valid_members = [];
    $admin_count = 0;

    foreach ($members as $index => $member) {
        $member_user_id = $member['user_id'] ?? null;
        $role = $member['role'] ?? null;

        if (empty($member_user_id) && empty($role)) continue;

        if (empty($member_user_id) || empty($role)) {
            die("<script>alert('Each member must have both a user and a role.'); window.history.back();</script>");
        }

        if ($member_user_id == $owner_user_id) {
            die("<script>alert('You cannot select yourself as a group member.'); window.history.back();</script>");
        }

        // Ensure user is admin in user_master
        $checkQuery = "SELECT user_role FROM user_master WHERE user_id = ?";
        $stmtCheck = $conn->prepare($checkQuery);
        $stmtCheck->bind_param("i", $member_user_id);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $user = $result->fetch_assoc();
        $stmtCheck->close();

        if (!$user || $user['user_role'] != 3) {
            die("<script>alert('Only admin users can be added to the group.'); window.history.back();</script>");
        }

        if ($role === "admin") {
            $admin_count++;
        }

        $valid_members[] = [
            'user_id' => $member_user_id,
            'role' => $role
        ];
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

    // Insert the creator as 'Owner'
    $stmt_owner = $conn->prepare("INSERT INTO user_group_roles (group_id, user_id, role) VALUES (?, ?, ?)");
    $owner_role = "Owner";
    $stmt_owner->bind_param("iis", $group_id, $owner_user_id, $owner_role);
    $stmt_owner->execute();
    $stmt_owner->close();

    // Insert valid group members
    $stmt_member = $conn->prepare("INSERT INTO user_group_roles (group_id, user_id, role) VALUES (?, ?, ?)");
    foreach ($valid_members as $member) {
        $stmt_member->bind_param("iis", $group_id, $member['user_id'], $member['role']);
        $stmt_member->execute();
    }
    $stmt_member->close();

    $conn->close();

    echo "<script>alert('Group created successfully!'); window.location.href='cardlist.php';</script>";
    exit;
} else {
    echo "Invalid Request.";
    exit;
}
?>
