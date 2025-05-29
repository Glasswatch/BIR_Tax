<?php
session_start();
if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp']);

    if (time() > $_SESSION['otp_expires']) {
        echo "OTP expired. Try again.";
        session_unset();
        exit();
    }

    if ($enteredOtp == $_SESSION['reset_otp']) {
        header("Location: reset_password.php");
    } else {
        echo "Invalid OTP.";
    }
}
?>

<!-- verify_otp.php UI -->
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="register_design.css">
 </head>
 <body>

 <div class="container">
        <div class="bir-header">
           <img src="../taskb/picture.png" alt="BIR Logo" class="bir-img">
            <h2>BIR</h2>
        </div> 

    <div>
    <form method="POST">
        <h2>Verify OTP</h2>
        <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
        <button type="submit">Verify</button>
    </form>
</div>
 </body>
 </html>
 