<?php
session_start();
require_once '../db.php';
require '../vendor/autoload.php'; // Ensure PHPMailer is autoloaded

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['otp_expires'] = time() + 300; // 5 min expiry

        // Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
            $mail->SMTPAuth = true;
            $mail->Username = 'coastalguardian03@gmail.com';
            $mail->Password = 'nyww qhzz hlvo nilp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('coastalguardian03@gmail.com', 'BIR Support');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset OTP';
            $mail->Body = "Your OTP code is: <strong>$otp</strong><br>This code will expire in 5 minutes.";

            $mail->send();
            header("Location: verify_otp.php");
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
}
?>

<!-- forgot_password.php UI -->
<form method="POST">
    <h2>Forgot Password</h2>
    <input type="email" name="email" placeholder="Enter your registered email" required><br><br>
    <button type="submit">Send OTP</button>
</form>
