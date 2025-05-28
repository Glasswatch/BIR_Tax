<?php
session_start();
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, password, is_admin, is_approved, access_level FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $hashedPassword, $is_admin, $is_approved, $access_level);
        $stmt->fetch();

        if (password_verify($password, $hashedPassword)) {
            if (!$is_approved && !$is_admin) {
                header("Location: login.php?error=Your+account+is+pending+approval");
                exit();
            }

            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['is_admin'] = (bool)$is_admin;
            $_SESSION['is_approved'] = (bool)$is_approved;
            $_SESSION['access_level'] = $access_level;


            if ($is_admin) {
                header("Location: ../TaskD/admin_users.php");
            } else if ($access_level === 'Employee') {
                header("Location: ../taskB/official_dashboard.php");
            } else if ($access_level === 'Bank') {
                header("Location: ../taskC/payment_approval.php");
            }else {
                header("Location: calc.php");
            }
            exit();

        } else {
            header("Location: login.php?error=Invalid+Username+or+Password");
            exit();
        }
    } else {
        header("Location: login.php?error=Invalid+Username+or+Password");
        exit();
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="register_design.css" />
    <title>Login</title>
</head>

<body>
    <div class="container">
        
        <div class="bir-header">
           <img src="../taskb/picture.png" alt="BIR Logo" class="bir-img">
           <h2>Login</h2>
        </div> 

        
        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required /><br />
            <input type="password" name="password" placeholder="Password" required /><br />
            <button type="submit">Login</button>
            <p style="text-align: center; margin-top: 20px; color: #777; font-size: 14px;">
                Don't have an account?
                <a href="Register.php" style="color: #3498db; text-decoration: none; font-weight: 500;">Sign Up</a>
            </p>
            <p style="text-align: center; margin-top: 20px; color: #777; font-size: 14px;">
                <a href="forgot_password.php" style="color: #3498db; text-decoration: none; font-weight: 500;">Forgot Password</a>
            </p>
        </form>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <script src="PasswordLogic.js"></script>
</body>

</html>
