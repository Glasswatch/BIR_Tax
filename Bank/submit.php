<?php
session_start();
require_once '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['description'])) {
    $description = trim($_POST['description']);

    if (empty($description)) {
        $error = "Report description cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reports (user_id, description, status, created_at, updated_at) VALUES (?, ?, 'Pending', NOW(), NOW())");
        $stmt->bind_param("is", $user_id, $description);

        if ($stmt->execute()) {
            $success = "Report submitted successfully!";
        } else {
            $error = "Something went wrong while submitting the report.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <link rel="stylesheet" href="bank.css">
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo-container">
            <img src="../BIR_Employee/picture.png" alt="BIR Logo" class="bir-logo">
            <h1>Bureau of Internal Revenue</h1>
            
        </div>
        <a href="../BIR_Taxpayer/login.php" class="logout-btn">Logout</a>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <ul>
            <li><a href="submit.php">📝Create Reports</a></li>
            <li><a href="payment_approval.php">💵💰💳Payment Verification</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <div class="report-form">
            <h2>Submit a Report</h2>

            <?php if (isset($success) && $success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
            <?php elseif (isset($error) && $error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <label for="description">Report Description:</label><br>
                <textarea name="description" id="description" required></textarea><br>
                <button type="submit" class="submit-btn">Submit Report</button>
            </form>
        </div>
    </div>
</body>
</html>