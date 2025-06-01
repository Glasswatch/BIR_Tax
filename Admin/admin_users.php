<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    die("Unauthorized access.");
}

// 1. Pagination settings
$limit = 10; // users per page

// Determine current page from ?page=
if (isset($_GET['page']) && is_numeric($_GET['page']) && (int)$_GET['page'] > 0) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}

$offset = ($page - 1) * $limit;

// 2. Handle any POST actions (approve/revoke/set_access/make_admin/revoke_admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action  = $_POST['action'];    

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
    // After any action, redirect to avoid resubmission
    $redirectUrl = 'admin_users.php';
    $queryParams = [];
    if (!empty($_GET['search'])) {
        $queryParams['search'] = $_GET['search'];
    }
    if ($page > 1) {
        $queryParams['page'] = $page;
    }
    if (!empty($queryParams)) {
        $redirectUrl .= '?' . http_build_query($queryParams);
    }
    header("Location: $redirectUrl");
    exit;
}

// 3. Build search/filter clause
$search = '';
$filterQuery = '';
if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $search = trim($_GET['search']);
    $escaped = $conn->real_escape_string($search);
    $filterQuery = "AND (
        first_name   LIKE '%$escaped%' OR
        last_name    LIKE '%$escaped%' OR
        email        LIKE '%$escaped%' OR
        access_level LIKE '%$escaped%'
    )";
}

// 4. Count total users (excluding the current admin) with filter
$countSql = "
    SELECT COUNT(*) AS total
    FROM users
    WHERE id != {$_SESSION['user_id']} 
    $filterQuery
";
$countResult = $conn->query($countSql);
if (! $countResult) {
    die("Count query failed: " . $conn->error);
}
$totalRow   = $countResult->fetch_assoc();
$totalUsers = (int)$totalRow['total'];
$totalPages = (int)ceil($totalUsers / $limit);

// 5. Fetch users for this page
// Note: we include the same filter and skip the current admin
$usersSql = "
    SELECT id, first_name, last_name, email, is_approved, access_level, is_admin
    FROM users
    WHERE id != {$_SESSION['user_id']} 
    $filterQuery
    ORDER BY id ASC
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($usersSql);
if (! $stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$usersResult = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Manage Users</title>
    <link rel="stylesheet" href="AdminDesign.css">
</head>
<body>
    <div class="top-nav">
        <div class="bir-header">
            <img src="../BIR_Employee/picture.png" alt="BIR Logo" class="bir-logo">
            <h1>BIR Admin Panel</h1>
        </div>
        <nav>
            <a href="admin_users.php">👥 Manage Users</a>
            <a href="generate_report.php">📄 Generate Report</a>
            <a href="../BIR_Taxpayer/Login.php">❌ Log out</a>
        </nav>
    </div>

    <div style="padding: 2rem;">
        <h3 style="font-size: 2.5vw;">Manage Users</h3>

        <!-- 🔍 Search Form -->
        <form method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search by name or email"
                   value="<?= htmlspecialchars($search, ENT_QUOTES) ?>">
            <button type="submit">Search</button>
            <?php if ($search !== ''): ?>
                <a href="admin_users.php">Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <tr>
                <th>First Name</th>
                <th>Email</th>
                <th>Approved</th>
                <th>Access Level</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
            <?php while ($user = $usersResult->fetch_assoc()): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($user['first_name'] . " " . $user['last_name'], ENT_QUOTES) ?>
                </td>
                <td><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></td>
                <td><?= $user['is_approved'] ? '✅ Approved' : '❌ Not Approved' ?></td>
                <td><?= htmlspecialchars($user['access_level'], ENT_QUOTES) ?></td>
                <td><?= $user['is_admin'] ? '✅ Yes' : '❌ No' ?></td>
                <td class="action-cell" style="width: 29rem; padding: 0;">
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
                            <option value="user" <?= $user['access_level'] === 'user' ? 'selected' : '' ?>>
                                User
                            </option>
                            <option value="Employee" <?= $user['access_level'] === 'Employee' ? 'selected' : '' ?>>
                                Employee
                            </option>
                            <option value="Bank" <?= $user['access_level'] === 'Bank' ? 'selected' : '' ?>>
                                Bank
                            </option>
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

        <!-- ────────────────────────────────────────────────────────────────────── -->
        <!-- Pagination Links -->
        <div class="pagination">
            <?php
                // Preserve search parameter in pagination links
                $baseParams = [];
                if ($search !== '') {
                    $baseParams['search'] = $search;
                }
            ?>

            <!-- Previous -->
            <?php if ($page > 1): ?>
                <?php
                    $baseParams['page'] = $page - 1;
                    $prevUrl = 'admin_users.php?' . http_build_query($baseParams);
                ?>
                <a href="<?= $prevUrl ?>">&laquo; Previous</a>
            <?php endif; ?>

            <!-- Page Numbers -->
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php
                    $baseParams['page'] = $p;
                    $pageUrl = 'admin_users.php?' . http_build_query($baseParams);
                ?>
                <?php if ($p === $page): ?>
                    <span class="current"><?= $p ?></span>
                <?php else: ?>
                    <a href="<?= $pageUrl ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <!-- Next -->
            <?php if ($page < $totalPages): ?>
                <?php
                    $baseParams['page'] = $page + 1;
                    $nextUrl = 'admin_users.php?' . http_build_query($baseParams);
                ?>
                <a href="<?= $nextUrl ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
