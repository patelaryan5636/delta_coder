<?php 
    session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <title>Sign In</title>
</head>

<body>
    <div class="container">
        <div class="leftimg">
            <dotlottie-player src="https://lottie.host/1cd039a3-b625-490a-82da-0ab7a261f20b/IrcoiR4rNj.lottie"
                background="transparent" speed="1" style="width: 350px; height: 350px" loop autoplay></dotlottie-player>
        </div>
        <form action="includes/scripts/signmein.php" method="post" class="login">
            <div class="main">
                <div class="heading">
                    <h1>Sign In</h1>
                </div>
                <h4>
                    <?php
            
            if (isset($_SESSION['pacpal_error_message'])){
                echo "<a>" . $_SESSION['pacpal_error_message'] . "</a>";
                unset($_SESSION['pacpal_error_message']);
            }
            if (isset($_SESSION['success_message'])){
              echo "<a>" . $_SESSION['success_message'] . "</a>";
              unset($_SESSION['success_message']);
            }
          ?>
                </h4>
                <div class="inputs">
                    <input type="email" name="pacpal_login_email" class="input" placeholder="Email" autofocus required>
                    <input type="password" name="pacpal_login_password" class="input" placeholder="Password"
                        pattern=".{8,}" title="Password must be at least 8 characters long" required>
                </div>
                <div class="link">
                    <a href="forgot-password.php">Forgot password?</a>
                </div>
                <div class="button">
                    <input type="submit" class="btn" value="Sign In">
                </div>
                <div class="signup">
                    Don't have an account? <a href="sign-up.php">&nbsp;Sign Up</a>
                </div>
            </div>
        </form>
    </div>
</body>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

</html>