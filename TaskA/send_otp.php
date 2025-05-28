<?php
require_once '../db.php';
require '../vendor/autoload.php'; // Ensure PHPMailer is autoloaded

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Generate 6-digit OTP
        $otp = random_int(100000, 999999);
        $otp_hashed = password_hash($otp, PASSWORD_DEFAULT);
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Save OTP hash and expiry in DB for that user
        $update = $conn->prepare("UPDATE users SET reset_otp = ?, otp_expires = ? WHERE id = ?");
        $update->bind_param("ssi", $otp_hashed, $expires, $user_id);
        $update->execute();

        // Send OTP via email using PHPMailer
        $mail = new PHPMailer;

        $mail->isSMTP();    
        $mail->Host = 'smtp.gmail.com';  // your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'coastalguardian03@gmail.com'; 
        $mail->Password = 'nyww qhzz hlvo nilp'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('coastalguardian03@gmail.com', 'Your App Name');
        $mail->addAddress($email);

        $mail->Subject = 'Your OTP Code for Password Reset';
        $mail->Body = "Your OTP code is: $otp \nThis code will expire in 5 minutes.";

        if ($mail->send()) {
            header("Location: verify_otp.php?email=" . urlencode($email));
            exit();
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        echo "Email not found.";
    }
}
?>
