<?php 
session_start();
require_once '../db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] !== 'user') {
    header("Location: Login.php");
    exit();
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <link rel="stylesheet" href="css/forms.css">
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
            <li><a href="forms.php">📝Forms</a></li>
            <li><a href="calc.php">💵💰💳Withholding tax calculator</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div class="content">
        <div class="report-form">
            <table>
                <thead>
                    <tr>
                        <th>Form No.</th>
                        <th>Form Title</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="forms1.php">0605</a></td>
                        <td>Payment Form</td>
                    </tr>
                    <tr>
                        <td><a href="forms1.php#2" id="1">0611-A</a></td>
                        <td>Payment Form Covered by a Letter Notice</td>
                    </tr>
                    <tr>
                        <td><a href="forms1.php#3">0613</a></td>
                        <td>Payment Form Under Tax Compliance Verification Drive/Tax Mapping</td>
                    </tr>
                    <tr>
                        <td><a href="forms1.php#4">0619-E</a></td>
                        <td>Monthly Remittance Form of Creditable Income Taxes Withheld (Expanded)</td>
                    </tr>
                    
                </tbody>

            </table>
        </div>
    </div>
</body>
</html>