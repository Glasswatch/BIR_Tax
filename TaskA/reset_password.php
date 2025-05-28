<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($newPassword !== $confirm) {
        header("Location: reset_password.php?error=You+need+Stronger+Password");
        exit();
    }

    if (strlen($newPassword) < 8) {
        echo "Password must be at least 8 characters.";
        exit();
    }

    $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param("ss", $hashed, $_SESSION['reset_email']);
    $stmt->execute();

    session_unset();
    session_destroy();

    echo "Password reset successful. <a href='login.php'>Login here</a>";
}
?>

<!-- reset_password.php UI -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="register_design.css">
</head>

<body>
    
    <div class="container">
        <div class="bir-header">
           <img src="../taskb/picture.png" alt="BIR Logo" class="bir-img">
            <h2>Reset Password</h2>
        </div> 

   

    <form method="POST" action="reset_password.php" onsubmit="return validatePasswords();">
        <input type="password" id="password1" name="password" placeholder="Password" required minlength="8"
            autocomplete="new-password" oninput="updatePasswordFeedback();"><br>

        <input type="password" id="confirm_password1" name="confirm_password" placeholder="Confirm Password" required
            minlength="8" autocomplete="new-password"><br><br>
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
        <button type="submit">Reset</button>
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
     <div id="PasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage1"></p>
        </div>
    </div>

    

<script>
    // Inject PHP session values into JavaScript
    const firstName = <?php echo json_encode($_SESSION['first_name'] ?? ''); ?>;
    const lastName = <?php echo json_encode($_SESSION['last_name'] ?? ''); ?>;

    // Custom modal alert
    function showAlert(title, message) {
        document.getElementById("alertTitle").innerText = title;
        document.getElementById("alertMessage").innerText = message;
        document.getElementById("customAlert").style.display = "block";
    }

    function closeAlert() {
        document.getElementById("customAlert").style.display = "none";
    }

    // Password validation helpers
    function hasLowerCase(str) {
        return /[a-z]/.test(str);
    }

    function hasUpperCase(str) {
        return /[A-Z]/.test(str);
    }

    function hasNumber(str) {
        return /\d/.test(str);
    }

    function hasUniqueChar(str) {
        return /[\W_]/.test(str); // non-alphanumeric (special) character
    }

    function containsNameOrReverse(password, firstName, lastName) {
        const pwd = password.toLowerCase();
        const first = firstName.toLowerCase();
        const last = lastName.toLowerCase();
        const revFirst = first.split('').reverse().join('');
        const revLast = last.split('').reverse().join('');

        return (
            pwd.includes(first) ||
            pwd.includes(last) ||
            pwd.includes(revFirst) ||
            pwd.includes(revLast)
        );
    }

    // Live password feedback
    function updatePasswordFeedback() {
        const password = document.getElementById('password1').value;

        updateRequirementStatus('length', password.length >= 8);
        updateRequirementStatus('lowercase', hasLowerCase(password));
        updateRequirementStatus('uppercase', hasUpperCase(password));
        updateRequirementStatus('number', hasNumber(password));
        updateRequirementStatus('Unique', hasUniqueChar(password));
    }

    function updateRequirementStatus(id, isValid) {
        const element = document.getElementById(id);
        if (element) {
            element.classList.toggle('valid', isValid);
            element.textContent = (isValid ? '✔️' : '❌') + element.textContent.slice(1);
        }
    }

    // Form submit validation
    function validatePasswords() {
        const password = document.getElementById('password1').value;
        const confirmPassword = document.getElementById('confirm_password1').value;

        if (password !== confirmPassword) {
            showAlert("Password Error", "Passwords do not match!");
            return false;
        }

        if (password.length < 8) {
            showAlert("Password Error", "Password must be at least 8 characters.");
            return false;
        }

        if (!hasLowerCase(password)) {
            showAlert("Password Error", "Password must contain at least one lowercase letter.");
            return false;
        }

        if (!hasUpperCase(password)) {
            showAlert("Password Error", "Password must contain at least one uppercase letter.");
            return false;
        }

        if (!hasNumber(password)) {
            showAlert("Password Error", "Password must contain at least one number.");
            return false;
        }

        if (!hasUniqueChar(password)) {
            showAlert("Password Error", "Password must contain at least one special character.");
            return false;
        }

        if (containsNameOrReverse(password, firstName, lastName)) {
            showAlert("Password Error", "Password must NOT contain your first name, last name, or their reversed versions.");
            return false;
        }

        return true;
    }
</script>

<div id="customAlert" style="
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.5);
">
    <div style="
        background-color: white;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 300px;
        border-radius: 10px;
        text-align: center;
    ">
        <h2 id="alertTitle" style="margin-top: 0;"></h2>
        <p id="alertMessage"></p>
        <button onclick="closeAlert()" style="
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            cursor: pointer;
            border-radius: 5px;
        ">OK</button>
    </div>
</div>



   
</body>

</html>


