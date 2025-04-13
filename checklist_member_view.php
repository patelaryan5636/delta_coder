<?php
session_start();
include 'includes/scripts/connection.php';

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;

if (!$group_id) {
    die("Group ID is missing.");
}

// Get role directly (no prepared statements)
$role_sql = "SELECT role FROM user_group_roles WHERE group_id = $group_id AND user_id = $user_id";
$role_result = mysqli_query($conn, $role_sql);

if (!$role_result || mysqli_num_rows($role_result) == 0) {
    die("Access denied: You are not part of this group.");
}

$role = mysqli_fetch_assoc($role_result);

// Optional: If you want only members (not admin/owner) to view, uncomment this:
// if (in_array(strtolower($role['role']), ['owner', 'admin'])) {
//     die("Access denied: Only members can view this page.");
// }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary:rgb(181, 198, 199);
            --primary2: #96b4b4;
            --primary-light:rgb(0, 2, 2);
            --secondary:#c4dfdf;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
            --gray: #6c757d;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            background: linear-gradient(135deg, var(--primary2), var(--secondary));
            color: white;
            padding: 25px 30px;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }

        .header h2 {
            font-weight: 600;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .task-count {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .task-list {
            padding: 20px 30px;
        }

        .no-tasks {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray);
        }

        .no-tasks i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 15px;
            display: block;
        }

        .task-card {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .task-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .task-title {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark);
            flex: 1;
        }

        .task-category {
            display: inline-block;
            background: var(--primary);
            color: var(--primary-light);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .task-form {
            margin-top: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }

        .status-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Poppins', sans-serif;
            background-color: #fff;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }

        .status-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 101, 122, 0.1);
        }

        .note-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-family: 'Poppins', sans-serif;
            min-height: 100px;
            resize: vertical;
            transition: var(--transition);
        }

        .note-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(10, 101, 122, 0.1);
        }

        .btn-save {
            background: var(--primary2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-save:hover {
            background: #084c5d;
            transform: translateY(-2px);
        }

        .btn-save i {
            margin-right: 8px;
        }

        /* Status indicators */
        .status-Not-Started { color: var(--gray); }
        .status-Packed { color: var(--warning); }
        .status-Delivered { color: var(--success); }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .header {
                padding: 20px;
            }
            
            .task-list {
                padding: 15px;
            }
            
            .task-header {
                flex-direction: column;
            }
            
            .task-title {
                margin-bottom: 10px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }
            
            .header h2 {
                font-size: 1.5rem;
            }
            
            .task-card {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>My Assigned Tasks</h2>
        <div class="task-count">
            <?php 
            $count_sql = "SELECT COUNT(*) as total FROM checklist_items ci 
                         JOIN checklist_categories cc ON ci.category_id = cc.cc_id 
                         WHERE ci.assigned_to = $user_id AND cc.group_id = $group_id";
            $count_result = mysqli_query($conn, $count_sql);
            $total_tasks = mysqli_fetch_assoc($count_result)['total'];
            echo $total_tasks . ' ' . ($total_tasks == 1 ? 'Task' : 'Tasks');
            ?>
<<<<<<< Updated upstream
        </div>
    </div>
=======
            <form method="post" action="update_task_status.php?id=<?= $group_id ?>">
                <div class="task-card">
                    <strong>Task:</strong> <?= htmlspecialchars($task['item_name']) ?><br>
                    <strong>Category:</strong> <?= htmlspecialchars($task['category_name']) ?><br>
>>>>>>> Stashed changes

    <div class="task-list">
        <?php
        $task_sql = "
            SELECT ci.id, ci.item_name, ci.status, ci.note, cc.category_name
            FROM checklist_items ci
            JOIN checklist_categories cc ON ci.category_id = cc.cc_id
            WHERE ci.assigned_to = $user_id AND cc.group_id = $group_id
            ORDER BY 
                CASE ci.status 
                    WHEN 'Not Started' THEN 1
                    WHEN 'Packed' THEN 2
                    WHEN 'Delivered' THEN 3
                    ELSE 4
                END,
                ci.item_name
        ";
        $task_result = mysqli_query($conn, $task_sql);

        if ($task_result && mysqli_num_rows($task_result) > 0) {
            while ($task = mysqli_fetch_assoc($task_result)) {
                $status_class = 'status-' . str_replace(' ', '-', $task['status']);
                ?>
                <form method="post" action="update_task_status.php" class="task-card">
                    <div class="task-header">
                        <div>
                            <div class="task-title"><?= htmlspecialchars($task['item_name']) ?></div>
                            <div class="task-category"><?= htmlspecialchars($task['category_name']) ?></div>
                        </div>
                        <div class="<?= $status_class ?>">
                            <i class="fas fa-circle"></i> <?= $task['status'] ?>
                        </div>
                    </div>

                    <div class="task-form">
                        <div class="form-group">
                            <label for="status-<?= $task['id'] ?>">Update Status</label>
                            <select id="status-<?= $task['id'] ?>" class="status-select" name="status">
                                <option value="Not Started" <?= $task['status'] === 'Not Started' ? 'selected' : '' ?>>Not Started</option>
                                <option value="Packed" <?= $task['status'] === 'Packed' ? 'selected' : '' ?>>Packed</option>
                                <option value="Delivered" <?= $task['status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="note-<?= $task['id'] ?>">Notes</label>
                            <textarea id="note-<?= $task['id'] ?>" class="note-input" name="note" rows="3"><?= htmlspecialchars($task['note']) ?></textarea>
                        </div>

                        <input type="hidden" name="item_id" value="<?= $task['id'] ?>">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
                <?php
            }
        } else {
            echo '
            <div class="no-tasks">
                <i class="far fa-check-circle"></i>
                <h3>No Tasks Assigned</h3>
                <p>You currently have no tasks assigned to you.</p>
            </div>';
        }
        ?>
    </div>
</div>

<script>
    // Add animation to status change
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const statusDisplay = this.closest('.task-card').querySelector('[class^="status-"]');
            const newStatus = this.value.replace(' ', '-');
            
            // Remove all status classes
            statusDisplay.className = statusDisplay.className.replace(/\bstatus-\S+/g, '');
            
            // Add new status class
            statusDisplay.classList.add('status-' + newStatus);
            
            // Update text
            statusDisplay.innerHTML = '<i class="fas fa-circle"></i> ' + this.value;
        });
    });

    // Add focus styles for better accessibility
    document.querySelectorAll('select, textarea, button').forEach(el => {
        el.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 3px rgba(10, 101, 122, 0.3)';
        });
        
        el.addEventListener('blur', function() {
            this.style.boxShadow = '';
        });
    });
</script>
</body>
</html>