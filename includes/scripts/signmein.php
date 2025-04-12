<?php
session_start();
require_once 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php'; // Include PHPMailer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_email = $_POST["pacpal_login_email"];
    $loginPassword = $_POST["pacpal_login_password"];

    // Validate input
    if (empty($login_email) || empty($loginPassword)) {
        $_SESSION['Yatra_error_message'] = "Email and password are required.";
        header("Location: userlogin");
        exit();
    }

    // Check user in database
    $selectQuery = "SELECT * FROM user_master WHERE email = ?";
    $stmt = $conn->prepare($selectQuery);
    $stmt->bind_param("s", $login_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPasswordFromDB = $row["password"];
        $isVerified = $row["is_verified"];
        $userId = $row["user_id"];
        $username = $row["user_name"]; // Get username for email
        $userRole = $row["user_role"]; // Fetch user role

        // Check user role
        if ($userRole != 3) {
            $_SESSION['pacpal_error_message'] = "You are not eligible for this access. Please contact support.";
            header("Location: ../../sign-in"); // Redirect to userregister.php
            exit();
        }

        // Verify the provided password
        if (password_verify($loginPassword, $hashedPasswordFromDB)) {
            if ($isVerified == 0) {
                $sql = "SELECT value FROM credential WHERE `key` = 'mail'";
                $result = mysqli_query($conn, $sql);
                $data = mysqli_fetch_assoc($result);
                $mail_password = $data['value'];

                // First-time login → Send welcome email and update is_verified to 1
                if (sendWelcomeEmail($login_email, $username,$mail_password)) {
                    $updateQuery = "UPDATE user_master SET is_verified = 1 WHERE user_id = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("i", $userId);
                    $updateStmt->execute();

                    $_SESSION['Yatra_success_message'] = " Welcome, $username! Your account is now verified.";
                }
            }

            // Set session and redirect to dashboard
            $_SESSION['pacpal_logedin_user_id'] = $userId;
            header("Location: ../../index");
            exit();
        } else {
            $_SESSION['pacpal_error_message'] = "Incorrect password.";
            header("Location: ../../sign-in");
            exit();
        }
    } else {
        $_SESSION['pacpal_error_message'] = "Email not found.";
        header("Location: ../../sign-in");
        exit();
    }
    $stmt->close();
}
$conn->close();



// Function to send welcome email
function sendWelcomeEmail($email, $username,$pass) {
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Change this to your SMTP provider
    $mail->SMTPAuth = true;
    $mail->Username = 'patelaryan5636@gmail.com'; // Your email
    $mail->Password = $pass ; // Your email password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('patelaryan5636@gmail.com', 'Pacpal Support');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8'; // Important for emoji support
    $mail->Subject = "🎉 Welcome to PackPal, {$username}!";

    $mail->Body = "
        <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f9f9f9; border-radius: 10px; color: #333;'>
            <h2 style='color: #2c7865;'>Welcome to PackPal, {$username}! 🎒</h2>

            <p style='font-size: 16px; line-height: 1.6;'>
                We’re thrilled to have you join <strong>PackPal</strong> — your smart assistant for organizing group packing and travel plans with ease.
            </p>

            <p style='font-size: 16px; line-height: 1.6;'>
                <strong>Your account has been successfully verified!</strong><br>
                You’re now ready to create or join travel groups, assign checklist items, and collaborate seamlessly with your crew.
            </p>

            <div style='text-align: center; margin: 30px 0;'>
                <a href='https://yourwebsite.com/login' 
                   style='background-color: #2c7865; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>
                   Login to Your Account
                </a>
            </div>

            <p style='font-size: 14px; color: #555;'>
                Have any questions? Our support team is just a message away.
            </p>

            <p style='font-size: 14px; color: #555;'>
                Here's to stress-free travel planning. ✈️🌍<br>
                Let PackPal handle the logistics while you enjoy the journey!
            </p>

            <hr style='margin-top: 30px; border: none; border-top: 1px solid #ccc;'>

            <p style='font-size: 12px; color: #999;'>
                &copy; " . date('Y') . " PackPal. All rights reserved.
            </p>
        </div>
    ";

    return $mail->send();
    
}
?>
