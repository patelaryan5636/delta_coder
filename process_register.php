<?php
session_start();
require_once 'includes/scripts/connection.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $username = $_POST['username'];
    $gender = $_POST['gender'];
    $fullname= $_POST["fullname"];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // ✅ Check if any field is empty
    if (empty($email) || empty($mobile) || empty($fullname) ||empty($username) || empty($gender) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_messages'] = "Please fill all fields.";
        header("location: sign-up");
        exit();
    }

    // ✅ Check if password and confirm password match
    if ($password !== $confirm_password) {
        $_SESSION['error_messages'] = "Password and Confirm Password do not match.";
        header("location: sign-up");
        exit();
    }
    
    // ✅ Check if the email format is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_messages'] = "Please enter a valid email address.";
        header("location: sign-up");
        exit();
    }

    // ✅ Check if the email already exists in the database
    $sql = "SELECT * FROM user_master WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_messages'] = "This email address is already registered.";
        header("location: sign-up");
        exit();
    }

    // ✅ Check if the mobile number is exactly 10 digits long
    if (strlen($mobile) != 10 || !is_numeric($mobile)) {
        $_SESSION['error_messages'] = "Mobile number must be 10 digits.";
        header("location: sign-up");
        exit();
    }

    // ✅ Check if the password length is between 8 and 14 characters
    if (strlen($password) < 8 || strlen($password) > 14) {
    $_SESSION['error_messages'] = "Password must be between 8 and 14 characters.";
    header("location: sign-up");
    exit();
    }

    // ✅ Check if the username length is more than 16 characters
    if (strlen($username) > 16) {
        $_SESSION['error_messages'] = "Username must be a maximum of 16 characters.";
        header("location: sign-up");
        exit();
    }
    
    // ✅ Check if the mobile number already exists in the database
    $sql = "SELECT * FROM user_master WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_messages'] = "This mobile number is already registered.";
        header("location: sign-up");
        exit();
    }
    // Generate a 4-character OTP
    $otp = strtoupper(substr(str_shuffle('123456789'), 0, 4));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Store temporary data in session
    $_SESSION['temp_data'] = [
        'email' => $email,
        'mobile' => $mobile,
        'username' => $username,
        'gender' => $gender,
        'fullname' => $fullname,
        'password' => $password
    ];
    
    // Mark user as registered to access OTP page
    $_SESSION['registered'] = true;

    // Delete any existing OTP for the email before inserting a new one
    $deleteStmt = $conn->prepare("DELETE FROM otp_verifications WHERE email = ?");
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();

    // Store OTP in the otp_verifications table
    $stmt = $conn->prepare("INSERT INTO otp_verifications (email, otp) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $otp);
    
    $sql = "Select * from credential from pacpal where key = 'mail' ";
    $result = mysqli_query($conn,$sql);
    $data = mysqli_fetch_assoc($result);
    $password = $data['value'];

    if ($stmt->execute()) {
        // Send OTP via Email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'patelaryan5636@gmail.com'; // Your email
            $mail->Password = $password; // Your email app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('patelaryan5636@gmail.com', 'Yatra support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = '🔐 Your Yatra OTP Code - Secure Your Account Now!';
            $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification - Yatra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 0;
            margin: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .header {
            background: #0073e6;
            color: white;
            padding: 15px;
            font-size: 22px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #0073e6;
            margin: 20px 0;
        }
        .content {
            font-size: 16px;
            color: #333;
            padding: 20px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
        .yatra-logo {
            width: 80px;
            margin-top: 10px;
        }
        .verify-button {
            display: inline-block;
            padding: 12px 20px;
            background-color: #0073e6;
            color: white;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            margin-top: 20px;
        }
        .verify-button:hover {
            background-color: #005bb5;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">Yatra - OTP Verification</div>
        <img class="yatra-logo" src="yatra.png" alt="Yatra Logo">
        <div class="content">
            <p>Dear Traveler,</p>
            <p>Your One-Time Password (OTP) for verification is:</p>
            <div class="otp-code">'.$otp.'</div>
            <p>Please enter this OTP to complete your registration.</p>
            <p>If you did not request this, please ignore this email.</p>
            <a href="localhost/yatra/otppage" class="verify-button">Verify Now</a>
        </div>
        <div class="footer">
            &copy; '.date("Y").' Yatra. All rights reserved.
        </div>
    </div>
</body>
</html>';

            $mail->send();

            // Redirect user to OTP verification page
            $_SESSION['otp_success'] = 'Your OTP has been sent successfully. Please check your email.';
            header("Location: otppage");
            exit();
        } catch (Exception $e) {
            die("OTP Email could not be sent. Error: " . $mail->ErrorInfo);
        }
    } else {
        die("Failed to store OTP in database.");
    }
}else{
    header("location: sign-up");
}
?>
