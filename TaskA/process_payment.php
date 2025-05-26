<?php
session_start();
require_once '../db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if payment method is selected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['paymentMethod'] ?? '';
    $referenceNumber = trim($_POST['referenceNumber'] ?? '');
    $userId = $_SESSION['user_id'];
    
    // Validate payment method
    $validMethods = ['creditCard', 'bankTransfer', 'gcash'];
    if (!in_array($paymentMethod, $validMethods)) {
        $_SESSION['error'] = "Please select a valid payment method.";
        header("Location: payment.php");
        exit();
    }

    // Validate reference number (optional but recommended)
    if (empty($referenceNumber)) {
        $_SESSION['error'] = "Reference number is required for this payment.";
        header("Location: payment.php");
        exit();
    }
    
    // Get the latest calculation
    $query = "SELECT * FROM withholding_tax_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $calculation = $result->fetch_assoc();
    $stmt->close();
    
    if (!$calculation) {
        $_SESSION['error'] = "No calculation found. Please complete the tax calculation first.";
        header("Location: calc.php");
        exit();
    }

    // Save payment information including reference number
    $paymentQuery = "INSERT INTO tax_payments 
        (user_id, calculation_id, payment_method, amount, reference_number, payment_status, created_at) 
        VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
        
    $paymentStmt = $conn->prepare($paymentQuery);
    $paymentStmt->bind_param(
        "iisss", 
        $userId, 
        $calculation['id'], 
        $paymentMethod, 
        $calculation['withholding_tax'], 
        $referenceNumber
    );

    if ($paymentStmt->execute()) {
        $_SESSION['success'] = "Payment initiated successfully. Please complete the payment process.";
        header("Location: payment_success.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to process payment: " . $paymentStmt->error;
        header("Location: payment.php");
        exit();
    }

    $paymentStmt->close();
    $conn->close();
} else {
    header("Location: payment.php");
    exit();
}
?>
