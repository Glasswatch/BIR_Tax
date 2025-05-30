<?php
session_start();
require_once '../db.php';

// Check if logged in and is an employee (official)
if (!isset($_SESSION['user_id']) || $_SESSION['access_level'] !== 'Employee') {
    die("Access denied. Please log in as an Employee/Official.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id'], $_POST['new_status'])) {
    $report_id = intval($_POST['report_id']);
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['Pending', 'Approved', 'Rejected'];
    if (!in_array($new_status, $allowed_statuses)) {
        die("Invalid status.");
    }

    $stmt = $conn->prepare("UPDATE reports SET status = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $new_status, $report_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch reports with user info
$sql = "SELECT r.id, r.description, r.status, r.created_at, u.first_name, u.last_name, u.email
        FROM reports r
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>  
    <meta charset="UTF-8" />
    <title>Official Dashboard - BIR Employee</title>
   <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="top-nav">
        <div class="logo-container">
            <img src="picture.png" alt="BIR Logo" class="bir-logo">
            <h2>BIR Employee Dashboard</h2>
           
        </div>
        <nav>
            <a href="../BIR_Taxpayer/login.php" class="logout-btn">Logout</a>
        </nav>
    </div>
    
    <div class="main-nav">
        <ul>
            <li><a href="official_dashboard.php">Dashboard</a></li>
        </ul>
    </div>

    <div class="content">
        <h3>Reports Submitted by Users</h3>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Report Content</th>
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
                            <td><?= nl2br(htmlspecialchars($row['description'])) ?></td>
                            <td class="status-<?= htmlspecialchars($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars(date("F j, Y, g:i a", strtotime($row['created_at']))) ?></td>
                            <td>
                                <?php if ($row['status'] === 'Pending'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="report_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="new_status" value="Approved" style="background-color: green; color: white;">Approve</button>
                                    <button type="submit" name="new_status" value="Rejected" style="background-color: red; color: white;">Reject</button>
                                </form>
                                <?php else: ?>
                                    <em>No actions available</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reports found.</p>
        <?php endif; ?>
    </div>
</body>
</html>