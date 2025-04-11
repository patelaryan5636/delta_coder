<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>OTP Verification</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-image {
            background: url("otpbg.jpg") no-repeat center center/cover;
            height: 100vh;
        }

        .otp-input {
            width: 50px;
            height: 50px;
            text-align: center;
            font-size: 1.5rem;
            border: 2px solid #c4dfdf;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s;
        }

        .otp-input:focus {
            border-color: #2c7865;
            box-shadow: 0px 0px 8px rgba(44, 120, 101, 0.5);
        }
    </style>
</head>

<body class="bg-image flex items-center justify-evenly p-9">
    <div class="flex items-center justify-evenly h-full w-full bg-[#f7f7f78e] rounded-lg">
        <div>
            <dotlottie-player src="https://lottie.host/cc015a4e-6bc1-4412-97a7-105e28a805bc/pOIAsU6wOG.lottie"
                background="transparent" speed="1" style="width: 350px; height: 350px" loop autoplay></dotlottie-player>
        </div>
        <div class="bg-[#F8F6F4] shadow-lg rounded-lg p-8 w-96 text-center">
            <img src="./assets/img/Black Yellow White Circle Badge illustration Simple Tour & Travel Logo.png"
                alt="Logo" class="h-20 mx-auto mb-4" />

            <h2 class="text-2xl font-bold text-gray-800">Enter OTP</h2>
            <p class="text-gray-600 text-sm">
                We have sent a verification code to your email
            </p>

            <!-- Display Error Message if OTP doesn't match -->
            <br>
            <?php if(isset($_SESSION['otp_error'])): ?>
            <div id="otpError"
                class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded-lg shadow-md mb-4 transition-opacity duration-500">
                <strong class="block font-semibold">⚠️ Error!</strong>
                <p class="mt-2 text-sm">
                    <?php echo $_SESSION['otp_error']; ?>
                </p>
            </div>
            <?php unset($_SESSION['otp_error']); // Clear the error message after displaying ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['otp_success'])): ?>
            <div id="otpSuccess"
                class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded-lg shadow-md mb-4 transition-opacity duration-500">
                <strong class="block font-semibold">✅ OTP Sent!</strong>
                <p class="mt-2 text-sm">
                    <?php echo $_SESSION['otp_success']; ?>
                </p>
            </div>
            <?php unset($_SESSION['otp_success']); // Clear message after displaying ?>
            <?php endif; ?>

            <p id="otpMessage" class="text-sm text-green-500 mt-2"></p>

            <!-- OTP FORM -->
            <form action="verify_otp.php" method="POST">
                <div class="flex justify-center gap-3 mt-1">
                    <input type="text" maxlength="1" class="otp-input" id="otp1" required oninput="moveNext(1)"
                        onkeydown="handleBackspace(event, 1)">
                    <input type="text" maxlength="1" class="otp-input" id="otp2" required oninput="moveNext(2)"
                        onkeydown="handleBackspace(event, 2)">
                    <input type="text" maxlength="1" class="otp-input" id="otp3" required oninput="moveNext(3)"
                        onkeydown="handleBackspace(event, 3)">
                    <input type="text" maxlength="1" class="otp-input" id="otp4" required oninput="moveNext(4)"
                        onkeydown="handleBackspace(event, 4)">
                </div>
                <input type="hidden" name="otp" id="otp-hidden">

                <button type="submit"
                    class="w-full mt-5 bg-[#C4DFDF] text-gray-600 py-2 rounded-lg text-lg hover:bg-[#D2E9E9] transition">
                    Verify OTP →
                </button>
            </form>

            <p class="text-sm text-gray-600 mt-4">
                Didn’t receive a code? <a href="#" id="resendOtp" class="text-blue-500">Resend OTP</a>
            </p>
        </div>
    </div>

    <script>
        // Function to move focus to the next OTP input
        function moveNext(index) {
            let input = document.getElementById("otp" + index);
            if (input.value.length === 1 && index < 4) {
                document.getElementById("otp" + (index + 1)).focus();
            }
        }

        // Function to handle backspace and move focus backward
        function handleBackspace(event, index) {
            let input = document.getElementById("otp" + index);
            if (event.key === "Backspace" && input.value === "" && index > 1) {
                document.getElementById("otp" + (index - 1)).focus();
                document.getElementById("otp" + (index - 1)).value = "";
            }
        }

        // Combine OTP inputs into one hidden field before submitting
        document.querySelector("form").addEventListener("submit", function (e) {
            let otp = "";
            document.querySelectorAll(".otp-input").forEach(input => {
                otp += input.value;
            });
            document.getElementById("otp-hidden").value = otp;
        });

        let resendBtn = document.getElementById("resendOtp");
        let otpMessage = document.getElementById("otpMessage");
        let timer = 30; // 30 seconds countdown

        function startResendTimer() {
            resendBtn.disabled = true;
            resendBtn.innerText = `Resend OTP (${timer}s)`;

            let countdown = setInterval(() => {
                timer--;
                resendBtn.innerText = `Resend OTP (${timer}s)`;

                if (timer === 0) {
                    clearInterval(countdown);
                    resendBtn.disabled = false;
                    resendBtn.innerText = "Resend OTP";
                }
            }, 1000);
        }

        // Start the timer when the page loads
        startResendTimer();

        // Event listener for the resend OTP button
        resendBtn.addEventListener("click", function (e) {
            e.preventDefault(); // Prevent default button action

            fetch("resend_otp", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
            })
                .then((response) => response.json())
                .then((data) => {
                    otpMessage.innerText = data.message;
                    otpMessage.style.color = data.status === "success" ? "green" : "red";

                    if (data.status === "success") {
                        timer = 30; // Reset timer
                        startResendTimer(); // Restart countdown
                    }
                })
                .catch((error) => console.error("Error:", error));
        });

        // Redirect to userregister.php after 2 minutes
        setTimeout(function () {
            window.location.href = "sign-up";
        }, 60000); // 120,000 milliseconds = 2 minutes

        // Function to fade out messages after 5 seconds
        setTimeout(() => {
            let errorMsg = document.getElementById('otpError');
            let successMsg = document.getElementById('otpSuccess');

            if (errorMsg) {
                errorMsg.style.opacity = '0';
                setTimeout(() => { errorMsg.style.display = 'none'; }, 500);
            }

            if (successMsg) {
                successMsg.style.opacity = '0';
                setTimeout(() => { successMsg.style.display = 'none'; }, 500);
            }
        }, 5000);

    </script>



</body>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

</html>