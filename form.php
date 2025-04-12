<?php
session_start();
require_once 'includes/scripts/connection.php';
if(isset($_SESSION['pacpal_logedin_user_id']) && (trim ($_SESSION['pacpal_logedin_user_id']) !== '')){
  $user_id = $_SESSION['pacpal_logedin_user_id'];
  $query = "SELECT * FROM user_master WHERE user_id = $user_id";
  $result = mysqli_query($conn, $query);
  $userdata = mysqli_fetch_assoc($result);
  $user_role = $userdata["user_role"];
  if($user_role != 3){
      header("Location: 404.php");
  }
} else {
  header("Location: sign-in.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Create Group</title>
  <style>
    .member-row { margin-bottom: 10px; }
    select, input, textarea { margin: 5px; padding: 5px; }
  </style>
</head>
<body>

  <h2>Create New Group</h2>
  <form action="insert_group.php" method="POST" id="groupForm" style="max-width: 600px; margin: auto; font-family: Arial, sans-serif; border: 1px solid #ccc; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
    <h2 style="text-align: center; color: #333;">Create New Group</h2>
    
    <div style="margin-bottom: 15px;">
      <label for="group_name" style="display: block; font-weight: bold; margin-bottom: 5px;">Group Name:</label>
      <input type="text" id="group_name" name="group_name" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" required>
    </div>

    <div style="margin-bottom: 15px;">
      <label for="group_description" style="display: block; font-weight: bold; margin-bottom: 5px;">Group Description:</label>
      <textarea id="group_description" name="group_description" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" rows="4" required></textarea>
    </div>

    <h3 style="color: #333; margin-bottom: 10px;">Group Members:</h3>
    <div id="membersWrapper" style="margin-bottom: 15px;"></div>
    <button type="button" onclick="addMember()" style="background-color: #007BFF; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;">+ Add Member</button>
    
    <div style="text-align: center; margin-top: 20px;">
      <input type="submit" value="Create Group" style="background-color: #28a745; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
    </div>
  </form>

  <script>
    let count = 0;

    function addMember() {
      const wrapper = document.getElementById('membersWrapper');
      const memberDiv = document.createElement('div');
      memberDiv.classList.add('member-row');

      memberDiv.innerHTML = `
        <select name="members[${count}][user_id]" class="user-dropdown" required>
          <option value="">Loading users...</option>
        </select>

        <select name="members[${count}][role]" required>
          <option value="">Select Role</option>
          <option value="admin">Admin</option>
          <option value="member">Member</option>
          <option value="viewer">Viewer</option>
        </select>

        <button type="button" onclick="this.parentElement.remove()">Remove</button>
      `;

      wrapper.appendChild(memberDiv);

      fetch('get_users.php')
        .then(res => res.json())
        .then(data => {
          const dropdown = memberDiv.querySelector('.user-dropdown');
          dropdown.innerHTML = '<option value="">Select User</option>';
          data.forEach(user => {
            const option = document.createElement('option');
            option.value = user.user_id;
            option.textContent = user.user_name;
            dropdown.appendChild(option);
          });
        });

      count++;
    }
  </script>
</body>
</html>
