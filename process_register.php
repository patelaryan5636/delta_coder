<?php
session_start();
require_once 'includes/scripts/connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $username = $_POST['username'];
    $gender = $_POST['gender'];
    $fullname = $_POST["fullname"];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($email) || empty($mobile) || empty($fullname) || empty($username) || empty($gender) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_messages'] = "Please fill all fields.";
        header("location: sign-up");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_messages'] = "Password and Confirm Password do not match.";
        header("location: sign-up");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_messages'] = "Please enter a valid email address.";
        header("location: sign-up");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user_master WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_messages'] = "This email address is already registered.";
        header("location: sign-up");
        exit();
    }

    if (strlen($mobile) != 10 || !is_numeric($mobile)) {
        $_SESSION['error_messages'] = "Mobile number must be 10 digits.";
        header("location: sign-up");
        exit();
    }

    if (strlen($password) < 8 || strlen($password) > 14) {
        $_SESSION['error_messages'] = "Password must be between 8 and 14 characters.";
        header("location: sign-up");
        exit();
    }

    if (strlen($username) > 16) {
        $_SESSION['error_messages'] = "Username must be a maximum of 16 characters.";
        header("location: sign-up");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM user_master WHERE phone = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error_messages'] = "This mobile number is already registered.";
        header("location: sign-up");
        exit();
    }

    $otp = strtoupper(substr(str_shuffle('123456789'), 0, 4));
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $_SESSION['temp_data'] = [
        'email' => $email,
        'mobile' => $mobile,
        'username' => $username,
        'gender' => $gender,
        'fullname' => $fullname,
        'password' => $hashed_password
    ];
    $_SESSION['registered'] = true;

    $deleteStmt = $conn->prepare("DELETE FROM otp_verifications WHERE email = ?");
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();

    $stmt = $conn->prepare("INSERT INTO otp_verifications (email, otp) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $otp);

    $sql = "SELECT value FROM credential WHERE `key` = 'mail'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    $mail_password = $data['value'];

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'patelaryan5636@gmail.com';
            $mail->Password = $mail_password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('patelaryan5636@gmail.com', 'Packpal support');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "🔐 Your Packpal OTP Code - Secure Your Account Now!";
        
            $mail->Body = "
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>PackPal OTP Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .email-container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #4CAF50;
            text-align: center;
        }
        .otp {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            color: #333;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            background-color: #f0f8ff;
        }
        .cta-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            font-size: 14px;
            text-align: center;
            margin-top: 30px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class='email-container'>
        <h1>PackPal: OTP Verification</h1>
        <p>Hello,</p>
        <p>Thank you for registering with PackPal! To complete your registration, please enter the One-Time Password (OTP) below:</p>
        <div class='otp'>".$otp."</div>
        <p>This OTP is valid for 10 minutes only. If you did not request this, please ignore this email.</p>
        <a href='#' class='cta-button'>Verify OTP</a>
        <div class='footer'>
            <p>PackPal Team</p>
            <p>If you have any questions, feel free to contact us at support@packpal.com</p>
        </div>
    </div>
</body>
</html>
";

            $mail->send();
            $_SESSION['otp_success'] = 'Your OTP has been sent successfully. Please check your email.';
            header("Location: otppage");
            exit();
        } catch (Exception $e) {
            die("OTP Email could not be sent. Error: " . $mail->ErrorInfo);
        }
    } else {
        die("Failed to store OTP in database.");
    }
} else {
    header("location: sign-up");
}
?>
