<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = trim($_POST['last_name'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email.");
        exit();
    }

    if (strlen($password) < 8) {
        die("Password must be at least 8 characters.");
        exit();
    }
    
    // Check if password is in common list
    $common_password_file = 'Common_Password.txt';
    if (file_exists($common_password_file)) {
        $common_passwords = file($common_password_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array(strtolower($password), $common_passwords, true)) {
            die("Your password is too common. Please choose a stronger password.");
            exit();
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (last_name, first_name, email ,password,phone_number) VALUES (?, ?, ?, ?,?)");
    if (!$stmt) {
        die("Database error: " . $conn->error);
        exit();
    }

    $stmt->bind_param("sssss", $last_name, $first_name, $email, $hashedPassword, $phone_number);

    if ($stmt->execute()) {
       header("Location: login.php?error=Your+account+is+pending+approval");
    } else {
        if ($conn->errno === 1062) {
            die("Email already exists.");
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="register_design.css">
    <script src="PasswordLogic.js"></script>
</head>

<body>
    
    <div class="container">
        <div class="bir-header">
           <img src="../taskb/picture.png" alt="BIR Logo" class="bir-img">
            <h2>Register</h2>
        </div> 

   

    <form method="POST" action="register.php" onsubmit="return validatePasswords();">
        <input type="text" name="first_name" placeholder="First Name" required><br><br>
        <input type="text" name="last_name" placeholder="Last Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="text" name="phone_number" placeholder="Phone Number" required><br><br>
        <input type="password" id="password" name="password" placeholder="Password" required minlength="8"
            autocomplete="new-password" oninput="updatePasswordFeedback();"><br>


        
        <div class="requirements">
            <em>Note: Password cannot be the reverse of your username or any disallowed string.</em><br>
            <strong>Password must contain:</strong>
            <span id="length"> At least 8 characters</span>
            <span id="lowercase"> A lowercase letter</span>
            <span id="uppercase"> An uppercase letter</span>
            <span id="number"> A number</span>
            <span id="Unique"> A unique Character</span>
            <br>
        </div><br>

        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required
            minlength="8" autocomplete="new-password"><br><br>

        <button type="submit">Register</button>
           <p style="
                text-align: center;
                margin-top: 20px;
                color: #777;
                font-size: 14px;
            ">
                Already have an account? 
                <a href="login.php" style="
                    color: #3498db;
                    text-decoration: none;
                    font-weight: 500;
                ">Log in here</a>
            </p>
    </form>

    </div>
    

</body>

</html>

