<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// users for the filter dropdown
$users_res = mysqli_query($conn, "SELECT id, name FROM users ORDER BY name");

// pagination & inputs
$perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 25;
if ($perPage <= 0 || $perPage > 200) $perPage = 25;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$actionFilter = isset($_GET['action']) ? trim($_GET['action']) : '';
$ipFilter = isset($_GET['ip']) ? trim($_GET['ip']) : '';
$from = isset($_GET['from']) ? trim($_GET['from']) : '';
$to = isset($_GET['to']) ? trim($_GET['to']) : '';
$dedup = isset($_GET['dedup']) && $_GET['dedup'] == '1';

// build filters (only reference activity_logs columns so queries work for dedup and normal)
$logFilters = [];
if ($user_id) {
    $logFilters[] = "user_id = " . $user_id;
}
if ($actionFilter !== '') {
    $safeAction = mysqli_real_escape_string($conn, $actionFilter);
    $logFilters[] = "action LIKE '%" . $safeAction . "%'";
}
if ($ipFilter !== '') {
    $safeIp = mysqli_real_escape_string($conn, $ipFilter);
    $logFilters[] = "ip_address LIKE '%" . $safeIp . "%'";
}
if ($from !== '') {
    $safeFrom = mysqli_real_escape_string($conn, $from);
    $logFilters[] = "created_at >= '" . $safeFrom . " 00:00:00'";
}
if ($to !== '') {
    $safeTo = mysqli_real_escape_string($conn, $to);
    $logFilters[] = "created_at <= '" . $safeTo . " 23:59:59'";
}

if ($q !== '') {
    $safeQ = mysqli_real_escape_string($conn, $q);
    $qConds = [];
    $qConds[] = "action LIKE '%" . $safeQ . "%'";
    $qConds[] = "description LIKE '%" . $safeQ . "%'";
    $qConds[] = "ip_address LIKE '%" . $safeQ . "%'";
    // find matching users by name and include their IDs (if any)
    $matchingUserIds = [];
    $mu = mysqli_query($conn, "SELECT id FROM users WHERE name LIKE '%" . $safeQ . "%'");
    if ($mu) {
        while ($r = mysqli_fetch_assoc($mu)) {
            $matchingUserIds[] = (int)$r['id'];
        }
    }
    if (!empty($matchingUserIds)) {
        $qConds[] = 'user_id IN (' . implode(',', $matchingUserIds) . ')';
    }
    $logFilters[] = '(' . implode(' OR ', $qConds) . ')';
}

$whereSql = $logFilters ? 'WHERE ' . implode(' AND ', $logFilters) : '';

// total count (handle dedup separately)
if ($dedup) {
    $countSql = "SELECT COUNT(*) AS total FROM (SELECT 1 FROM activity_logs " . $whereSql . " GROUP BY user_id, action, description, ip_address) t";
} else {
    $countSql = "SELECT COUNT(*) AS total FROM activity_logs " . $whereSql;
}
$cRes = mysqli_query($conn, $countSql);
$totalRows = 0;
if ($cRes) {
    $r = mysqli_fetch_assoc($cRes);
    $totalRows = (int)$r['total'];
}
$totalPages = $totalRows ? (int)ceil($totalRows / $perPage) : 1;

// fetch logs
if ($dedup) {
    $sql = "SELECT al2.*, u.name AS user_name
            FROM activity_logs al2
            JOIN (
                SELECT MAX(created_at) AS created_at, user_id, action, description, ip_address
                FROM activity_logs
                " . $whereSql . "
                GROUP BY user_id, action, description, ip_address
            ) latest
              ON al2.user_id = latest.user_id
             AND al2.action = latest.action
             AND al2.description = latest.description
             AND al2.ip_address = latest.ip_address
             AND al2.created_at = latest.created_at
            LEFT JOIN users u ON al2.user_id = u.id
            ORDER BY latest.created_at DESC
            LIMIT " . $offset . ", " . $perPage;
} else {
    $sql = "SELECT al.*, u.name AS user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id " . $whereSql . " ORDER BY al.created_at DESC LIMIT " . $offset . ", " . $perPage;
}
$logs = mysqli_query($conn, $sql);

function build_query(array $overrides = []) {
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        $params[$k] = $v;
    }
    return htmlspecialchars(http_build_query($params));
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Activity Logs | 56Food</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <style>
        .filters { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px }
        .filters input[type="text"], .filters select, .filters input[type="date"] { padding:6px; }
        .filters label { display:flex; align-items:center; gap:6px }
        .pagination { margin-top:12px; display:flex; gap:8px; align-items:center }
        table { width: 100%; border-collapse: collapse; margin-top: 8px }
        th, td { padding: 10px; border: 1px solid #ddd; font-size: 14px }
        th { background: #f4f4f4 }
        .small-muted { color:#666; font-size:13px }
    </style>
</head>

<body>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a class="active" href="activity_logs.php">Activity Logs</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Activity Logs</h2>

            <form method="get" class="filters" onsubmit="return true">
                <input type="text" name="q" placeholder="Search (user, action, description, IP)" value="<?= htmlspecialchars($q) ?>">
                <select name="user_id">
                    <option value="">All users</option>
                    <?php while ($u = mysqli_fetch_assoc($users_res)): ?>
                        <option value="<?= $u['id'] ?>" <?= ($user_id && $user_id == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
                    <?php endwhile; ?>
                </select>
                <!--<input type="text" name="action" placeholder="Action" value="<?= htmlspecialchars($actionFilter) ?>">-->
                <!--<input type="text" name="ip" placeholder="IP" value="<?= htmlspecialchars($ipFilter) ?>">-->
                <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
                <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
                <!--<label><input type="checkbox" name="dedup" value="1" <?= $dedup ? 'checked' : '' ?>> Deduplicate</label>-->
                <button type="submit">Filter</button>
                <div style="margin-left:auto" class="small-muted">Showing <?= $totalRows ?> result(s)</div>
            </form>

            <table>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                 <!-- <th>IP</th>-->
                    <th>Date</th>
                </tr>

                <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <!--<td><?= htmlspecialchars($log['ip_address']) ?></td>-->
                        <td><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?<?= build_query(['page' => $page - 1]) ?>">&laquo; Prev</a>
                <?php endif; ?>

                <span>Page <?= $page ?> of <?= $totalPages ?></span>

                <?php if ($page < $totalPages): ?>
                    <a href="?<?= build_query(['page' => $page + 1]) ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>

        </main>
    </div>

</body>

</html>