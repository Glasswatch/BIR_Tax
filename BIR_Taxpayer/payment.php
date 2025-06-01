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

// Initialize variables for receipt
$afrNumber = '';
$convenienceFee = 0;
$totalAmount = 0;
$paymentSuccess = false;

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

    // Calculate convenience fee (example: 0.5% of amount)
    $convenienceFee = $calculation['withholding_tax'] * 0.005;
    $totalAmount = $calculation['withholding_tax'] + $convenienceFee;
    
    // Generate AFR number (format: 000000000-00000-00-000-00-Q0-000000-O)
    $afrNumber = sprintf("%09d", $userId) . '-' . 
                 sprintf("%05d", rand(0, 99999)) . '-' . 
                 date('m') . '-' . 
                 sprintf("%03d", rand(0, 999)) . '-' . 
                 date('y') . '-Q' . 
                 ceil(date('n')/3) . '-' . 
                 sprintf("%06d", rand(0, 999999)) . '-O';

    // Save payment to DB
    $paymentQuery = "INSERT INTO tax_payments 
        (user_id, calculation_id, payment_method, amount, convenience_fee, 
         reference_number, afr_number, payment_status, payment_date, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
    $paymentStmt = $conn->prepare($paymentQuery);
    $paymentStmt->bind_param(
        "iisddss", 
        $userId, 
        $calculation['id'], 
        $paymentMethod, 
        $calculation['withholding_tax'],
        $convenienceFee,
        $referenceNumber,
        $afrNumber
    );

    if ($paymentStmt->execute()) {
        $paymentId = $conn->insert_id;
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
    <link rel="stylesheet" href="css/payment.css">
    <style>
        /* Receipt Styles */
        .receipt-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .bir-header h1 {
            color: #003366;  
            text-align: center;
            margin-bottom: 5px;
        }

        .bir-header h2 {
            color: #333;
            text-align: center;
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: normal;
        }

        .receipt-success {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f0fff0;
            border: 1px solid #a0d8a0;
        }

        .receipt-success h3 {
            color: #008000;
            margin: 0;
        }

        .receipt-details {
            margin: 20px 0;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .detail-item.total {
            font-weight: bold;
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
        }

        .detail-label {
            font-weight: bold;
        }

        .receipt-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .receipt-container, .receipt-container * {
                visibility: visible;
            }
            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                box-shadow: none;
                border: none;
            }
            .receipt-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <?php if (!$paymentSuccess): ?>
    <div class="payment-container">
        <div class="bir-header">
            <img src="../BIR_Employee/picture.png" alt="BIR Logo" class="bir-logo">
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
                    <label for="referenceNumber">Reference Number (Required):</label>
                    <input type="number" pattern="\d{11}" maxlength="11" id="referenceNumber" name="referenceNumber" placeholder="Enter reference number" required>
                </div>

                <button type="submit" class="btn">Proceed to Payment</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($paymentSuccess) && $paymentSuccess): ?>
    <div class="receipt-container">
        <div class="bir-header">
            <img src="../BIR_Employee/picture.png" alt="BIR Logo" class="bir-logo">
            <h1 style="text-align: center;">BIRTaxPay</h1>
          
        </div>
          <h2 style="text-align: center;">AUTHORIZED TAX PAYMENT GATEWAY</h2>
        <div class="receipt-success">
            <h3>Payment Successful</h3>
            <p>Thank you for your payment.</p>
        </div>
        
        <div class="receipt-details">
            <div class="detail-item">
                <span class="detail-label">AFR#:</span>
                <span><?php echo $afrNumber; ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span>Pending   </span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Date and Time:</span>
                <span><?php echo date('F j, Y g:i A'); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Merchant:</span>
                <span>Bureau of Internal Revenue</span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Reference No.:</span>
                <span><?php echo $referenceNumber; ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Payment Method:</span>
                <span><?php 
                    echo ucfirst(str_replace('_', ' ', $paymentMethod)); 
                ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Customer Account No.:</span>
                <span><?php echo $userId; ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Amount:</span>
                <span>PHP <?php echo number_format($calculation['withholding_tax'], 2); ?></span>
            </div>
            
            <div class="detail-item">
                <span class="detail-label">Convenience Fee:</span>
                <span>PHP <?php echo number_format($convenienceFee, 2); ?></span>
            </div>
            
            <div class="detail-item total">
                <span class="detail-label">TOTAL AMOUNT:</span>
                <span>PHP <?php echo number_format($totalAmount, 2); ?></span>
            </div>
        </div>
        
        <div class="receipt-actions">
            <button onclick="window.print()" class="btn">Print Receipt</button>
            <a href="../BIR_Taxpayer/dashboard.php" class="btn">Return to Dashboard</a>
        </div>
    </div>
    
    <script>
        // Auto-scroll to receipt
        document.addEventListener('DOMContentLoaded', function() {
            window.scrollTo(0, document.body.scrollHeight);
        });
    </script>
    <?php endif; ?>
</body>
</html>