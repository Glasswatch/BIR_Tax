    <?php
    session_start();
    require_once '../db.php';

    // Check if logged in and is an employee (official)
    if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] !== 'Employee') {
        die("Access denied. Please log in as an Employee/Official.");
    }

    // Handle payment verification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_id'], $_POST['action'])) {
        $payment_id = intval($_POST['payment_id']);
        $action = $_POST['action'];

        $allowed_actions = ['approve', 'reject'];
        if (!in_array($action, $allowed_actions)) {
            die("Invalid action.");
        }

        $status = ($action === 'approve') ? 'Approved' : 'Rejected';
        $processed_by = $_SESSION['user_id'];

        $stmt = $conn->prepare("UPDATE tax_payments 
                            SET payment_status = ?, 
                                processed_by = ?, 
                                processed_at = NOW() 
                            WHERE id = ?");
        $stmt->bind_param("sii", $status, $processed_by, $payment_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Payment has been $status successfully.";
        header("Location: payment_approval.php");
        exit();
    }

    // Fetch all tax payments with user info
    $sql = "SELECT p.id, p.payment_method, p.amount, p.payment_status, p.created_at,
                u.first_name, u.last_name, u.email
            FROM tax_payments p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC";

    $result = $conn->query($sql);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>  
        <meta charset="UTF-8" />
        <title>Payment Verification - BIR Employee</title>
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
        </style>
    </head>
    <body>
        <div class="top-nav">
            <div class="logo-container">
                <img src="picture.png" alt="BIR Logo" class="bir-logo">
                <h2>BIR Employee Dashboard</h2>
            </div>
            <nav>
                <a href="../logout.php" class="logout-btn">Logout</a>
            </nav>
        </div>

        <div class="main-nav">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="payment_verification.php">Payment Verification</a></li>
                <li><a href="taxpayers.php">Taxpayers</a></li>
                <li><a href="settings.php">Account Settings</a></li>
            </ul>
        </div>

        <div class="content">
            <h3>Tax Payment Verification</h3>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="message success"><?= $_SESSION['message'] ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Taxpayer</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td>₱<?= number_format($row['amount'], 2) ?></td>
                                <td><?= ucfirst(htmlspecialchars($row['payment_method'])) ?></td>
                                <td class="status-<?= htmlspecialchars($row['payment_status']) ?>">
                                    <?= htmlspecialchars($row['payment_status']) ?>
                                </td>
                                <td><?= htmlspecialchars(date("M j, Y g:i A", strtotime($row['created_at']))) ?></td>
                                <td>
                                    <?php if ($row['payment_status'] === 'pending'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="payment_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="action" value="approve" style="background-color: green; color: white;">Approve</button>
                                        <button type="submit" name="action" value="reject" style="background-color: red; color: white;">Reject</button>
                                    </form>
                                    <?php else: ?>
                                        <em>Verified</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No tax payments found for verification.</p>
            <?php endif; ?>
        </div>
    </body>
    </html>

    <?php $conn->close(); ?>
