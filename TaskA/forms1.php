<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Report</title>
    <style>
         html {
            scroll-behavior: smooth !important; 
        }
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
        .divtitle{
            font-weight: 700;
            font-size: 16px;
            margin:5px;
        }
        .divdesc1{
            font-weight:600;
            font-size: 14px;
            margin:7px;
        }
        .divdesc2{
            color: rgb(96, 103, 116);
            font-size:14px;
            margin:7px;
        }
        .linkstyle{
            font-size:14px;
            margin:5px;
            font-weight:540;
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
            <div class= "divtitle" id="1">
                BIR Form No. 0605 - Payment Form
            </div>
            <div class = "divdesc1">
                Description
            </div>
            <div class = "divdesc2">
                This form shall be accomplished every time a taxpayer pays taxes 
                and fees which do not require the use of a tax return such as 
                second installment payment for income tax, deficiency tax, 
                delinquency tax, registration fees, penalties, advance payments,
                deposits, installment payments, etc.
            </div>
            <div class = "divdesc1">
                Filing Date
            </div>
            <div class = "divdesc2">
                This form shall be accomplished:
                <ol>
                    <li>Every time a tax payment or penalty is due
                        or an advance payment is made; and</li>
                    
                    <li> Upon receipt of a demand letter / assessment notice 
                        and/or collection letter from the BIR.</li>
                </ol>
                <p>&nbsp</p>

                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/0605version1999_09.02.2022_copy.xls" download="" target="_blank" rel="no referrer">
                    View BIR Form No. 0605 excel</a>
                
                <br>    
                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/0605version1999_09.02.2022_copy.pdf" download="" target="_blank" rel="no referrer">View BIR Form No. 0605 pdf</a>
            </div>
        </div>

        <div class="report-form">
            <div class= "divtitle" id = "2">
                BIR Form No. 0611-A - Payment Form Covered by a Letter Notice
            </div>
            <div class = "divdesc1">
                Description
            </div>
            <div class = "divdesc2">
                This form shall be used by any person, natural or juridical, including estates 
                trusts, who are issued Letter Notices generated through the following third-party 
                information (TPI)data matching programs: 
                <ol>
                    <li>Reconciliation of Listings for Enforcement (RELIEF)/Bureau of Customs (BOC) Data Program; 
                        and</li>
                    
                    <li>Tax Reconciliation Systems (TRS).</li>
                </ol>
                <p>&nbsp</p>
 
                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/0611-A Oct 2014 ENCS.pdf" download="" target="_blank" rel="no referrer">View BIR Form No. 0611-A pdf</a>
            </div>
        </div>

        <div class="report-form">
            <div class= "divtitle" id="3">
                BIR Form No. 0613 - Payment Form Under Tax Compliance Verification Drive/Tax Mapping </div>
            <div class = "divdesc1">
                Description
            </div>
            <div class = "divdesc2">
                This form shall be used in paying penalties assessed under the Tax Compliance 
                Verification Drive/Tax Mapping.
            </div>
            <div class = "divdesc1">
                Filing Date
            </div>
            <div class = "divdesc2">
                This form shall be accomplished:
                <ol>
                    <li>This form shall be accomplished every time a penalty is due.</li>
                    
                </ol>
                <p>&nbsp</p>

                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/166790613.zip" download="" target="_blank" rel="no referrer">View BIR Form No. 0613 excel</a>
                
                <br>    
                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/0613dec2004.pdf" download="" target="_blank" rel="no referrer">View BIR Form No. 0613 pdf</a>
            </div>
        </div>

        <div class="report-form">
            <div class= "divtitle" id="4">
                BIR Form No. 0619-E - Monthly Remittance Form of Creditable Income Taxes Withheld (Expanded)
            </div>
            <div class = "divdesc1">
                Description
            </div>
            <div class = "divdesc2">
                This monthly remittance form shall be filed in triplicate by every withholding agent/payor required to 
                deduct and withhold taxes on income payments subject to Expanded/Creditable Withholding Taxes. This form 
                shall be used in paying penalties assessed under the Tax Compliance Verification Drive/Tax Mapping.
            </div>
            <div class = "divdesc1">
                Filing Date
            </div>
            <div class = "divdesc2">
                Manual Filers
                <br>
                <br>
                This form shall be filed and the tax remitted on or before the 10th day following the month 
                in which withholding was made. This shall be filed for the first two (2) months of each 
                calendar quarter.
                <br>
                <br>
                eFPS Filers
                <br>
                In accordance with the schedule set forth in RR No. 26-2002 as follows:
                <br>
                <br>
                <br>
                Group A:Fifteen (15) days following the month in which withholding was made <br>
                Group B: Fourteen (14) days following the month in which withholding was made <br>
                Group C: Thirteen (13) days following the month in which withholding was made <br>
                Group D: Twelve (12) days following the month in which withholding was made <br>
                Group E: Eleven (11) days following the month in which withholding was made <br>

                <br>
                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/166790613.zip" download="" target="_blank" rel="no referrer">View BIR Form No. 0613 excel</a>
                
                <br>    
                <a class="linkstyle" href="https://bir-cdn.bir.gov.ph/local/pdf/0613dec2004.pdf" download="" target="_blank" rel="no referrer">View BIR Form No. 0613 pdf</a>
            </div>
        </div>
        
        
    </div>
</body>
</html>