<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Unauthorized access.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE users SET is_approved = 1 WHERE id = $user_id");
    } elseif ($action === 'revoke') {
        $conn->query("UPDATE users SET is_approved = 0 WHERE id = $user_id");
    } elseif ($action === 'set_access') {
        $access_level = $conn->real_escape_string($_POST['access_level']);
        $conn->query("UPDATE users SET access_level = '$access_level' WHERE id = $user_id");
    } elseif ($action === 'make_admin') {
        $conn->query("UPDATE users SET is_admin = 1 WHERE id = $user_id");
    } elseif ($action === 'revoke_admin') {
        $conn->query("UPDATE users SET is_admin = 0 WHERE id = $user_id");
    }
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filterQuery = $search ? "AND (first_name LIKE '%$search%' OR last_name LIKE '  %$search%' OR email LIKE '%$search%' OR access_level LIKE '%$search%')" : '';


$result = $conn->query("SELECT id, first_name, last_name, email, is_approved, access_level, is_admin 
                        FROM users 
                        WHERE id != {$_SESSION['user_id']} $filterQuery");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="AdminDesign.css">
    <style>
   
    </style>
</head>
<body>
    <div class="top-nav">
        <div class="bir-header">
            <img src="../taskb/picture.png" alt="BIR Logo" class="bir-logo">
           <h1>BIR Admin Panel</h1>
        </div>  

        
        <nav>
            <a href="admin_users.php">👥 Manage Users</a>
            <a href="generate_report.php">📄 Generate Report</a>
            <a href="../taska/Login.php">❌     Log out</a>
        </nav>
    </div>

    <div style="padding: 2rem;">
        <h3 style="font-size: 2.5vw;">Manage Users</h3>

        <!-- 🔍 Search Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by name or email" value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <?php if ($search): ?>
                <a href="admin_users.php" >Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>     
                <th>Email</th>
                <th>Approved</th>
                <th>Access Level</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
            <?php while($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($user['first_name']) ?></td>
                <td><?= htmlspecialchars($user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['is_approved'] ? '✅ Approved' : '❌ Not Approved' ?></td>
                <td><?= htmlspecialchars($user['access_level']) ?></td>
                <td><?= $user['is_admin'] ? '✅ Yes' : '❌ No' ?></td>
                <td class="action-cell">
                    <!-- Approve / Revoke -->
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <?php if (!$user['is_approved']): ?>
                            <button name="action" value="approve">Approve</button>
                        <?php else: ?>
                            <button name="action" value="revoke">Revoke</button>
                        <?php endif; ?>
                    </form>

                    <!-- Set Access -->
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <select name="access_level">
                            <option value="user" <?= $user['access_level'] === 'user' ? 'selected' : '' ?>>User</option>
                            <option value="Employee" <?= $user['access_level'] === 'Employee' ? 'selected' : '' ?>>Employee</option>
                            <option value="Bank" <?= $user['access_level'] === 'Bank' ? 'selected' : '' ?>>Bank</option>
                        </select>
                        <button name="action" value="set_access">Set Access</button>
                    </form>

                    <!-- Make / Revoke Admin -->
                    <form method="POST">
                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                        <?php if (!$user['is_admin']): ?>
                            <button name="action" value="make_admin">Make Admin</button>
                        <?php else: ?>
                            <button name="action" value="revoke_admin">Revoke Admin</button>
                        <?php endif; ?>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
