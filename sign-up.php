<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login.css">
    <title>Sign up</title>
</head>

<body>
    <div class="container">
        <form action="process_register" method="post" class="login" id="signupform">
            <div class="main">
                <div class="heading">
                    <h1>Sign up</h1>
                </div>
                <h4>
                    <?php
                    
                        if (isset($_SESSION['error_messages'])){
                            echo "<a>" . $_SESSION['error_messages'] . "</a>";
                            unset($_SESSION['error_messages']);
                        }
                    ?>
                </h4>
                <div class="inputs">
                    <input type="text" name="username" class="input" placeholder="User Name"
                        title="Please enter a valid name without numbers" autofocus required>
                    <input type="text" name="fullname" class="input" placeholder="Full Name"
                        title="Please enter a valid name without numbers" required>
                    <input type="number" name="mobile" class="input" placeholder="Phone Number"
                        pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" required>
                    <select name="gender" class="input" style="width: 75%;" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                    <input type="email" name="email" class="input" placeholder="Email" id="emailID"
                        required>
                    <span id="emailError" class="error"></span>
                    <input type="password" name="password" class="input" placeholder="Password"
                        pattern=".{8,}" title="Password must be at least 8 characters long" id="password" required>
                    <input type="password" name="confirm_password" class="input"
                        placeholder="Confirm password" pattern=".{8,}"
                        title="Password must be at least 8 characters long" oninput="checkPasswordMatch()"
                        id="confirm-password" required>
                </div>
                <div class="button">
                    <input type="submit" class="btn" value="Sign Up">
                </div>
                <div class="signup">
                    Have an account? <a href="sign-in.php">&nbsp;Sign In</a>
                </div>
            </div>
        </form>
        <div class="leftimg">
            <dotlottie-player src="https://lottie.host/40091d37-a210-43c2-929d-0a695214afd7/O2En3IUlC3.lottie"
                background="transparent" speed="1" style="width: 300px; height: 300px" loop autoplay></dotlottie-player>
        </div>
    </div>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script>
        function checkPasswordMatch() {
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirm-password");

            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity("Passwords do not match");
            } else {
                confirmPassword.setCustomValidity("");
            }
        }
    </script>
    <script>
        document.getElementById('signupform').addEventListener('input', function (event) {
            const phoneInput = document.querySelector('input[name="phone_number"]');
            const phoneError = document.getElementById('phoneError');

            if (!phoneError) {
                const errorSpan = document.createElement('span');
                errorSpan.id = 'phoneError';
                errorSpan.className = 'error';
                phoneInput.insertAdjacentElement('afterend', errorSpan);
            }

            if (phoneInput.value.length > 10) {
                phoneInput.value = phoneInput.value.slice(0, 10);
            }

            if (phoneInput.value.length < 10 && phoneInput.value.length > 0) {
                // document.getElementById('phoneError').textContent = 'Phone number must be 10 digits.';
            } else {
                document.getElementById('phoneError').textContent = '';
            }
        });
    </script>
    <script>
        // document.getElementById('signupform').addEventListener('submit', function (event) {
        //     // Prevent the form from submitting
        //     event.preventDefault();

        //     // Get the email input value
        //     var emailInput = document.getElementById('emailID');
        //     var email = emailInput.value.trim();

        //     // Regular expression for basic email validation
        //     var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        //     // Check if the email is valid
        //     if (emailRegex.test(email)) {
        //         // Email is valid, you can submit the form or perform additional actions
        //         document.getElementById('emailError').textContent = '';
        //         // Here you can submit the form or perform other actions
        //         alert('Email is valid.');
        //     } else {
        //         // Email is not valid, display an error message
        //         document.getElementById('emailError').textContent = 'Invalid email format';
        //     }
        // });
    </script>

</body>

</html>