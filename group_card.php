<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Group Cards</title>
</head>
<body>

<?php
$conn = new mysqli("localhost", "root", "", "pacpal");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch all groups
$result = $conn->query("SELECT * FROM group_master");

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $groupName = $row['group_name'];
    $ownerId = $row['created_by'];
    $createdOn = $row['created_at'];
    // $status = $row['status']; // Assuming there's a 'status' column

    // Fetch owner's name
    $ownerName = "Unknown";
    $result2 = $conn->query("SELECT user_name FROM user_master WHERE user_id = '$ownerId'");
    if ($result2 && $result2->num_rows > 0) {
      $row2 = $result2->fetch_assoc();
      $ownerName = $row2['user_name'];
    }

    echo "<div style='margin-bottom: 20px; border: 1px solid #ccc; padding: 15px;'>";

    echo "<h3>$groupName <span></span></h3>";
    echo "<p>👤 Created by: $ownerName</p>";
    echo "<p>📅 Created on: $createdOn</p>";
    echo "<p>👥 My Role: Admin</p>";

    echo "<button onclick='viewDetails()'>View Details</button> ";
    echo "<button onclick='editCard()'>Edit</button> ";
    echo "<button onclick='deleteCard()'>Delete</button>";

    echo "</div>";
  }
} else {
  echo "<p>No groups found.</p>";
}

$conn->close();
?>

<script>
  function viewDetails() {
    alert("Viewing details...");
  }

  function editCard() {
    alert("Editing card...");
  }

  function deleteCard() {
    if (confirm("Are you sure you want to delete this?")) {
      alert("Card deleted.");
    }
  }
</script>

</body>
</html>
