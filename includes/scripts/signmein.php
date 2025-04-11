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
                $sql = "Select * from credential from pacpal where key = 'mail' ";
                $result = mysqli_query($conn,$sql);
                $data = mysqli_fetch_assoc($result);
                $password = $data['value'];
                // First-time login → Send welcome email and update is_verified to 1
                if (sendWelcomeEmail($login_email, $username,$password)) {
                    $updateQuery = "UPDATE user_master SET is_verified = 1 WHERE user_id = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("i", $userId);
                    $updateStmt->execute();

                    $_SESSION['Yatra_success_message'] = "🎉 Welcome, $username! Your account is now verified.";
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

    $mail->Subject = '&#127881; Welcome to Pacpal!';
    $mail->Body = "
        <h2 style='color: #2c7865;'> Welcome to Pacpal, $username!</h2>
        <p>We're excited to have you on board. Now you can explore amazing travel experiences with Yatra.</p>
        <p><strong>Your account has been successfully verified.</strong></p>
        <p>Start your journey today!</p>
        <a href='https://yourwebsite.com/login' style='background: #2c7865; color: #fff; padding: 10px 15px; border-radius: 5px; text-decoration: none;'>Login to Your Account</a>
        <br><br>
        <p>If you have any questions, feel free to reach out to our support team.</p>
        <p>Happy Travelling! 🌍✨</p>
    ";

    return $mail->send();
}
?>
