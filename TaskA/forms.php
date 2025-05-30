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
            <img src="../TaskB/picture.png" alt="BIR Logo" class="bir-logo">
            <h1>Bureau of Internal Revenue</h1>
            
        </div>
        <a href="../TaskA/login.php" class="logout-btn">Logout</a>
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