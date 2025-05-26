<?php
session_start();
require_once '../db.php'; // Your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_calculate'])) {
    if (!isset($_SESSION['user_id'])) {
      header("Location: Login.php");
      exit();
    }

    $userId = $_SESSION['user_id'];
    $grossCompIncome = isset($_POST['grossCompensationIncome']) ? floatval(str_replace(',', '', $_POST['grossCompensationIncome'])) : 0;
    $totalNonTaxable = isset($_POST['totalNonTaxableNt']) ? floatval(str_replace(',', '', $_POST['totalNonTaxableNt'])) : 0;
    $netTaxableIncome = isset($_POST['netIncome']) ? floatval(str_replace(',', '', $_POST['netIncome'])) : 0;
    $withholdingTax = isset($_POST['totalWithholdingTax']) ? floatval(str_replace(',', '', $_POST['totalWithholdingTax'])) : 0;

    $stmt = $conn->prepare("INSERT INTO withholding_tax_results (user_id, gross_compensation_income, total_non_taxable_exempt_income, net_taxable_compensation_income, withholding_tax) VALUES (?, ?, ?, ?, ?)");

    if (!$stmt) {
        die('Database prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("idddd", $userId, $grossCompIncome, $totalNonTaxable, $netTaxableIncome, $withholdingTax);

    if ($stmt->execute()) {

          header('Location: payment.php');  
          exit();
    } else {
        $errorMessage = "Failed to save results: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withholding Tax Calculator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .calculator-container {
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
        fieldset {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        legend {
            font-weight: bold;
            padding: 0 10px;
            color: #2c3e50;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        #calculateBtn {
            background-color: #3498db;
            color: white;
        }
        #clearBtn {
            background-color: #e74c3c;
            color: white;
        }
        .results {
            background-color: #eaf7fd;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .results h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .result-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .result-label {
            font-weight: bold;
        }

                .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        .alert-error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>
</head>
<body>
    <div class="calculator-container">
        <h1>Withholding Tax Calculator</h1>
        
        <form name="withholding_tax_calculator" id="taxForm" method="post"  onsubmit="return validateForm()">
            <fieldset>
                <legend>Payroll Information</legend>
                <div class="form-group">
                    <label for="payroll_period">Payroll Period:</label>
                    <select name="payroll_period" id="payroll_period" onchange="makeEnableAnnualFields()">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="semi_monthly">Semi-Monthly</option>
                        <option value="monthly">Monthly</option>
                        <option value="annual">Annual</option>
                    </select>
                </div>
            </fieldset>

            <fieldset>
                <legend>Taxable Regular Compensation</legend>
                <div class="form-group">
                    <label for="basicSalary">Basic Salary:</label>
                    <input type="text" name="basicSalary" id="basicSalary" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="representationAllowance">Representation Allowance:</label>
                    <input type="text" name="representationAllowance" id="representationAllowance" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="transportationAllowance">Transportation Allowance:</label>
                    <input type="text" name="transportationAllowance" id="transportationAllowance" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="costOfLivingAllowance">Cost of Living Allowance:</label>
                    <input type="text" name="costOfLivingAllowance" id="costOfLivingAllowance" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="fixedHousingAllowance">Fixed Housing Allowance:</label>
                    <input type="text" name="fixedHousingAllowance" id="fixedHousingAllowance" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="otherTaxableRegular">Other Taxable Regular Compensation:</label>
                    <input type="text" name="otherTaxableRegular" id="otherTaxableRegular" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
            </fieldset>

            <fieldset>
                <legend>Taxable Supplementary Compensation</legend>
                <div class="form-group">
                    <label for="commission">Commission:</label>
                    <input type="text" name="commission" id="commission" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="profitSharing">Profit Sharing:</label>
                    <input type="text" name="profitSharing" id="profitSharing" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="fees">Fees including Director's Fee:</label>
                    <input type="text" name="fees" id="fees" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="otherBenefits">Taxable 13th Month Pay & Other Benefits:</label>
                    <input type="text" name="otherBenefits" id="otherBenefits" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="hazardPay">Hazard Pay:</label>
                    <input type="text" name="hazardPay" id="hazardPay" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="overtimePay">Overtime Pay:</label>
                    <input type="text" name="overtimePay" id="overtimePay" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="otherTaxableSupplementary">Other Taxable Supplementary Compensation:</label>
                    <input type="text" name="otherTaxableSupplementary" id="otherTaxableSupplementary" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
            </fieldset>

            <fieldset>
                <legend>Non-Taxable/Exempt Compensation Income</legend>
                <div class="form-group">
                    <label for="basicSalaryNt">Basic Salary/Statutory Minimun Wage Earner (MWE):</label>
                    <input type="text" name="basicSalaryNt" id="basicSalaryNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="holidayPayNt">Holiday Pay (MWE):</label>
                    <input type="text" name="holidayPayNt" id="holidayPayNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="overtimePayNt">Overtime Pay (MWE):</label>
                    <input type="text" name="overtimePayNt" id="overtimePayNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="nightShiftDifferentialNt">Night Shift Differential (MWE):</label>
                    <input type="text" name="nightShiftDifferentialNt" id="nightShiftDifferentialNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="hazardPayNt">Hazard Pay (MWE):</label>
                    <input type="text" name="hazardPayNt" id="hazardPayNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="otherBenefitsNt">13th Month Pay & Other Benefits:</label>
                    <input type="text" name="otherBenefitsNt" id="otherBenefitsNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="deMinimisBenefitsNt">De Minimis Benefits:</label>
                    <input type="text" name="deMinimisBenefitsNt" id="deMinimisBenefitsNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="sssGsisPagibigNt">SSS, GSIS, PAG-IBIG Contributions and Union Dues (Employee's Share Only):</label>
                    <input type="text" name="sssGsisPagibigNt" id="sssGsisPagibigNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="salariesOtherComepnsationNt">Salaries and Other Forms of Compensation:</label>
                    <input type="text" name="salariesOtherComepnsationNt" id="salariesOtherComepnsationNt" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
                <div class="form-group">
                    <label for="otherCompensationNt1">Other Non-Taxable/Exempt Compensation Income:</label>
                    <input type="text" name="otherCompensationNt1" id="otherCompensationNt1" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                </div>
            </fieldset>

            <fieldset>
                <legend>Exemptions</legend>
                <div class="form-group">
                    <label for="paidInsurance">Health/Hospitalization Insurance Premium:</label>
                    <input type="text" name="paidInsurance" id="paidInsurance" onkeypress="return allowNumericOnly(event)" onkeyup="numFormat(this.value);" onblur="calculate()">
                    <small><a href="#" onclick="insurancePopper()">What's this?</a></small>
                </div>
            </fieldset>

            <div class="btn-group">
                <button type="button" id="calculateBtn" onclick="calculateForm()">Calculate Withholding Tax</button>
                <button type="button" id="clearBtn" onclick="clearForm()">Clear Form</button>
            </div>

            <div class="results">
                <h3>Computation Results</h3>
                <div class="result-item">
                    <span class="result-label">Gross Compensation Income:</span>
                    <span><input type="text" name="grossCompensationIncome" id="grossCompensationIncome" readonly></span>
                </div>
                <div class="result-item">
                    <span class="result-label">Total Non-Taxable/Exempt Compensation Income:</span>
                    <span><input type="text" name="totalNonTaxableNt" id="totalNonTaxableNt" readonly></span>
                </div>
                <div class="result-item">
                    <span class="result-label">Net Taxable Compensation Income:</span>
                    <span><input type="text" name="netIncome" id="netIncome" readonly></span>
                </div>
                <div class="result-item">
                    <span class="result-label">Your Withholding Tax for the Period:</span>
                    <span><input type="text" name="totalWithholdingTax" id="totalWithholdingTax" readonly></span>
                </div>
            </div>        
          <div class="btn-group">
                <button type="submit" name="submit_calculate" style="background-color: #2ecc71; color: white;" 
                      onlick = >Submit</button>
            </div>    

        </form>
    </div>

    <script src="taxtcalc.js">
    </script>
</body>
</html>