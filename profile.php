<?php
session_start();
include("includes/scripts/connection.php");

if (!isset($_SESSION['pacpal_logedin_user_id'])) {
    die("Unauthorized access");
}

$user_id = $_SESSION['pacpal_logedin_user_id'];
$success = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $update_sql = "UPDATE user_master SET user_name='$username', full_name='$fullname', phone='$phone' WHERE user_id = $user_id";
    if (mysqli_query($conn, $update_sql)) {
        $success = true;
    }
}

// Fetch current user data
$sql = "SELECT user_name, email, full_name, phone FROM user_master WHERE user_id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Packpal</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#a5c6bc', secondary: '#f0f4f3' }, borderRadius: { 'none': '0px', 'sm': '4px', DEFAULT: '8px', 'md': '12px', 'lg': '16px', 'xl': '20px', '2xl': '24px', '3xl': '32px', 'full': '9999px', 'button': '8px' } } } }</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css">
    <style>
        @import url('https://fonts.cdnfonts.com/css/agbalumo');
        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        body {
            background-color: #f9fafb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        input:disabled {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }

        input:focus {
            outline: none;
            border-color: #a5c6bc;
            box-shadow: 0 0 0 3px rgba(165, 198, 188, 0.2);
        }

        :where([class^="ri-"])::before {
            content: "\f3c2";
        }

        .nav-container {
            /* position: fixed; */
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
        }

        .logo {
            font-family: 'Pacifico', serif;
            color: #88ABA5;
            font-size: 1.8rem;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .nav-button {
            transition: all 0.2s ease;
        }

        .nav-button:hover {
            filter: brightness(0.95);
        }

        .profile-container {
            position: relative;
        }


        .profile-container:hover .logout-button {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        @media (max-width: 640px) {
            .nav-content {
                padding: 0.75rem 1rem;
            }

            .logo {
                font-size: 1.5rem;
            }
        }
    </style>
</head>


<body>
<?php include("navbar.php"); ?>
<main class="min-h-[calc(100vh-8rem)] w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-sm p-6 md:p-8 w-full">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">My Profile</h1>
            <a href="logout" class="text-2xl flex items-center text-primary hover:text-primary/80 transition-colors font-bold">
                Logout
            </a>
        </div>

        <?php if ($success): ?>
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">Profile updated successfully!</div>
        <?php endif; ?>

        <div class="flex flex-col items-center mb-8">
            <div class="w-24 h-24 flex items-center justify-center rounded-full bg-primary/10 mb-4">
                <span class="text-4xl font-medium text-primary"><?= strtoupper($user['full_name'][0]) ?></span>
            </div>
            <h2 id="displayUsername" class="text-5xl font-medium text-gray-800" style="font-family: 'Agbalumo', sans-serif;">
                <?= htmlspecialchars($user['user_name']) ?>
            </h2>
        </div>

        <form class="space-y-6" action="" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['user_name']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded text-gray-800 focus:border-primary">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled
                        class="w-full px-4 py-3 border border-gray-300 rounded text-gray-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="fullname" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user['full_name']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded text-gray-800 focus:border-primary">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"
                        class="w-full px-4 py-3 border border-gray-300 rounded text-gray-800 focus:border-primary">
                </div>
            </div>
            <div class="pt-4">
                <input type="submit"
                    class="w-full bg-primary text-white py-3 px-4 rounded-button hover:bg-[#849E96] transition-colors font-medium" style="cursor: pointer;" value="Save Changes">
            </div>
        </form>
    </div>
</main>
<footer class="bg-gray-50 pt-6 pb-8">
    <div class="container mx-auto px-6">
        <div class="border-t border-gray-200 pt-8 flex flex-col md:flex-row justify-center justify-center items-center">
            <p class="text-gray-500 text-sm mb-4 md:mb-0">© 2025 PackPal. All rights reserved.</p>
        </div>
    </div>
</footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const usernameInput = document.getElementById('username');
            const fullnameInput = document.getElementById('fullname');
            const displayUsername = document.getElementById('displayUsername');
            const avatarInitials = document.querySelectorAll('.text-4xl.font-medium.text-primary, .text-lg.font-medium.text-primary');
            function updateAvatarInitial(name) {
                const initial = name.trim().charAt(0).toUpperCase();
                avatarInitials.forEach(avatar => {
                    avatar.textContent = initial;
                });
            }
            usernameInput.addEventListener('input', function (e) {
                displayUsername.textContent = e.target.value;
                updateAvatarInitial(e.target.value);
            });
            fullnameInput.addEventListener('input', function (e) {
                updateAvatarInitial(e.target.value);
            });
            // Initialize avatar with current username
            updateAvatarInitial(usernameInput.value);
        });
    </script>
</body>

</html>