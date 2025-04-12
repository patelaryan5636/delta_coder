<?php 

require 'includes/scripts/connection.php';  

$group_id = $_GET['id']; // Assuming passed in URL

// Fetch group info
$groupQuery = $conn->query("SELECT * FROM group_master WHERE group_id = '$group_id'");
$group = $groupQuery->fetch_assoc();

// Fetch group members
$membersQuery = $conn->query("SELECT gm.user_id, gm.role, um.user_name 
                              FROM user_group_roles gm 
                              JOIN user_master um ON gm.user_id = um.user_id 
                              WHERE gm.group_id = '$group_id'");

// Fetch all users for dropdown
$usersResult = $conn->query("SELECT user_id, user_name FROM user_master");
$allUsers = [];
while ($u = $usersResult->fetch_assoc()) {
    $allUsers[] = $u;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gropu</title>
</head>
<body>
<form action="update_group.php" method="POST">
    <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">

    <label>Group Name:</label>
    <input type="text" name="group_name" value="<?= $group['group_name'] ?>" class="form-input">

    <label>Group Description:</label>
    <textarea name="group_description" class="form-textarea"><?= $group['group_description'] ?></textarea>

    <h3>Group Members:</h3>
    <div id="members-container">
        <?php while ($member = $membersQuery->fetch_assoc()): ?>
        <div class="flex items-center mb-2">
            <select name="members[]" class="form-select">
                <?php foreach ($allUsers as $user): ?>
                    <option value="<?= $user['user_id'] ?>" <?= $user['user_id'] == $member['user_id'] ? 'selected' : '' ?>>
                        <?= $user['user_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="roles[]" class="form-select ml-2">
                <option value="Admin" <?= $member['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
                <option value="Editor" <?= $member['role'] == 'Editor' ? 'selected' : '' ?>>Editor</option>
                <option value="Viewer" <?= $member['role'] == 'Viewer' ? 'selected' : '' ?>>Viewer</option>
            </select>

            <button type="button" class="ml-2 text-red-600 remove-member">Remove</button>
        </div>
        <?php endwhile; ?>
    </div>

    <button type="button" id="add-member" class="btn mt-2">+ Add Member</button>
    <br><br>
    <button type="submit" class="btn bg-blue-600 text-white">Update Group</button>
</form>


<script>
document.getElementById('add-member').addEventListener('click', function() {
    const container = document.getElementById('members-container');
    const div = document.createElement('div');
    div.className = "flex items-center mb-2";
    div.innerHTML = `
        <select name="members[]" class="form-select">
            <?php foreach ($allUsers as $user): ?>
                <option value="<?= $user['user_id'] ?>"><?= $user['user_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <select name="roles[]" class="form-select ml-2">
            <option value="Admin">Admin</option>
            <option value="Editor">Editor</option>
            <option value="Viewer">Viewer</option>
        </select>
        <button type="button" class="ml-2 text-red-600 remove-member">Remove</button>
    `;
    container.appendChild(div);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-member')) {
        e.target.parentElement.remove();
    }
});
</script>

</body>
</html>