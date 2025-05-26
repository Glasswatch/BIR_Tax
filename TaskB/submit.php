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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .top-nav {
            background-color: #004080;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .bir-logo {
            height: 50px;
            width: auto;
        }
        .main-nav {
            background-color: #003366;
            padding: 10px 20px;
        }
        .main-nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }
        .main-nav li {
            margin-right: 20px;
        }
        .main-nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        .main-nav a:hover {
            background-color: #004080;
        }
        .content {
            padding: 20px;
            background-color: white;
            margin: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            text-align: center; 
        }
        th { 
            background-color: #004080; 
            color: white; 
        }
        button { 
            padding: 5px 10px; 
            margin: 2px; 
            cursor: pointer; 
            border: none;
            border-radius: 3px;
        }
        .status-Pending { 
            color: orange; 
            font-weight: bold; 
        }
        .status-Approved { 
            color: green; 
            font-weight: bold; 
        }
        .status-Rejected { 
            color: red; 
            font-weight: bold; 
        }
        .logout-btn {
            background-color: #cc0000;
            color: white;
            padding: 5px 15px;
            border-radius: 3px;
            text-decoration: none;
        }
        .logout-btn:hover {
            background-color: #990000;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .report-form {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #004080;
        }
        textarea {
            width: 90%;
            height: 150px;
            padding: 10px;
            resize: vertical;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
            font-size: 14px;
            resize: none;
        }
        .submit-btn {
            background-color: #004080;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #002b59;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo-container">
            <img src="picture.png" alt="BIR Logo" class="bir-logo">
            <h1>Bureau of Internal</h1>
            
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="tax-calculator.php">Tax Calculator</a></li>
            <li><a href="profile.php">Profile</a></li>
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