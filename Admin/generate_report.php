<?php
require_once '../db.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Access denied.");
}

// Fetch all user reports
$sql = "SELECT 
            u.first_name, 
            u.last_name, 
            u.email, 
            r.status, 
            r.description,
            r.created_at 
        FROM reports r
        INNER JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Report</title>
    <link rel="stylesheet" href="adminDesign.css">
</head>
<body>
    <!-- Navigation -->
    <div class="top-nav">
        <h2>BIR Admin Panel</h2>
        <nav>
            <a href="admin_users.php">👥 Manage Users</a>
            <a href="generate_report.php">📄 Generate Report</a>
        </nav>
    </div>

    <!-- Report Content -->
    <div class="content">
        <h3>Generated Reports</h3>

        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['description'] ?? '—') ?></td>
                            <td><?= htmlspecialchars(date("F j, Y, g:i a", strtotime($row['created_at']))) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No reports found.</p>
        <?php endif; ?>

        <br>
        <button onclick="window.print()" style="float: right;">🖨️ Print Report</button>
    </div>
</body>
</html>
