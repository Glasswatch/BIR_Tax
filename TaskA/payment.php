<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the latest calculation for this user
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
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="bir-header">
            <img src="../taskb/picture.png" alt="BIR Logo" class="bir-logo">
           <h1>Complete Your Payment</h1>
        </div> 
        
        
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

            <form action="process_payment.php" method="POST">
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
                    <input type="text" id="referenceNumber" name="referenceNumber" placeholder="Enter reference number">
                </div>

                <button type="submit" class="btn">Proceed to Payment</button>
            </form>
        </div>
    </div>
</body>
</html>
