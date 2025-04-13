<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$group_id = $_GET['id'];

$user_id = $_SESSION['pacpal_logedin_user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = intval($_POST['item_id']);
    $status = $_POST['status'];
    $note = trim($_POST['note']);

    // Validate status
    $valid_status = ['Not Started', 'Packed', 'Delivered'];
    if (!in_array($status, $valid_status)) {
        die("Invalid status");
    }

    // Update only if the task belongs to the user
    $check = $conn->prepare("SELECT * FROM checklist_items WHERE id = ? AND assigned_to = ?");
    $check->bind_param("ii", $item_id, $user_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        die("Unauthorized action");
    }

    $update = $conn->prepare("UPDATE checklist_items SET status = ?, note = ? WHERE id = ?");
    $update->bind_param("ssi", $status, $note, $item_id);
    $update->execute();

    header("Location: checklist_member_view.php?group_id=" . $group_id);
    // header("Location: checklist_member_view.php?group_id=" . $_GET['group_id']);
    exit;
} else {
    die("Invalid request");
}
