<?php
session_start();
require_once '../db.php';

// If not logged in, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch latest calculation for both GET and POST requests
$userId = $_SESSION['user_id'];
$query = "SELECT * FROM withholding_tax_results WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$calculation = $result->fetch_assoc();
$stmt->close();

if (!$calculation) {
    $_SESSION['error'] = "No calculation found. Please complete the tax calculation first.";
    header("Location: withholding_tax_calculator.php");
    exit();
}

// Only handle POST when user is submitting the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['paymentMethod'] ?? '';
    $referenceNumber = trim($_POST['referenceNumber'] ?? '');

    $validMethods = ['creditCard', 'bankTransfer', 'gcash'];
    if (!in_array($paymentMethod, $validMethods)) {
        $_SESSION['error'] = "Invalid payment method.";
        header("Location: payment.php");
        exit();
    }

    if (empty($referenceNumber)) {
        $_SESSION['error'] = "Reference number is required.";
        header("Location: payment.php");
        exit();
    }

    // Save payment to DB
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
        $paymentSuccess = true;
    } else {
        $_SESSION['error'] = "Payment failed: " . $paymentStmt->error;
    }

    $paymentStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        .calculation-summary {
            background-color: #eaf7fd;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .summary-label {
            font-weight: bold;
        }
        .payment-options {
            margin-top: 20px;
        }
        .payment-method {
            margin-bottom: 15px;
        }
        input[type="text"] {
            padding: 8px;
            width: 100%;
            box-sizing: border-box;
            margin-top: 5px;
        }
        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            display: inline-block;
        }
        .bir-logo {
            height: 50px;
            width: auto;
            margin-right: 1rem;
        }
        .bir-header{
            display:flex;
            align-items:center;
            justify-content: center;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 20% auto;
            padding: 30px;
            border-radius: 8px;
            width: 80%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: modalFadeIn 0.3s;
        }
        @keyframes modalFadeIn {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        .success-icon {
            font-size: 60px;
            color: #2ecc71;
            margin-bottom: 20px;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="bir-header">
            <img src="../taskb/picture.png" alt="BIR Logo" class="bir-logo">
           <h1>Complete Your Payment</h1>
        </div> 
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="calculation-summary">
            <h3>Your Tax Calculation Summary</h3>
            <div class="summary-item">
                <span class="summary-label">Gross Compensation Income:</span>
                <span>₱<?php echo number_format($calculation['gross_compensation_income'], 2); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Total Non-Taxable Income:</span>
                <span>₱<?php echo number_format($calculation['total_non_taxable_exempt_income'], 2); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Net Taxable Income:</span>
                <span>₱<?php echo number_format($calculation['net_taxable_compensation_income'], 2); ?></span>
            </div>
            <div class="summary-item">
                <span class="summary-label">Withholding Tax Due:</span>
                <span>₱<?php echo number_format($calculation['withholding_tax'], 2); ?></span>
            </div>
        </div>
        
        <div class="payment-options">
            <h3>Select Payment Method</h3>

            <form action="payment.php" method="POST">
                <div class="payment-method">
                    <input type="radio" id="creditCard" name="paymentMethod" value="creditCard" checked>
                    <label for="creditCard">Credit/Debit Card</label>
                </div>

                <div class="payment-method">
                    <input type="radio" id="bankTransfer" name="paymentMethod" value="bankTransfer">
                    <label for="bankTransfer">Bank Transfer</label>
                </div>

                <div class="payment-method">
                    <input type="radio" id="gcash" name="paymentMethod" value="gcash">
                    <label for="gcash">GCash</label>
                </div>

                <div class="payment-method">
                    <label for="referenceNumber">Reference Number (if available):</label>
                    <input type="text" id="referenceNumber" name="referenceNumber" placeholder="Enter reference number" required>
                </div>

                <button type="submit" class="btn">Proceed to Payment</button>
            </form>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <div class="success-icon">✓</div>
            <h2>Payment Successful!</h2>
            <p>You will be redirected to the login page shortly.</p>
        </div>
    </div>

    <script>
        <?php if (isset($paymentSuccess) && $paymentSuccess): ?>
            // Show modal
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('successModal');
                modal.style.display = 'block';
                
                // Redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 1000);
            });
        <?php endif; ?>
    </script>
</body>
</html>