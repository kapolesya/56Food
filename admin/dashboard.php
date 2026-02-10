<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

function count_table(mysqli $conn, string $table): int
{
    $res = mysqli_query($conn, "SELECT COUNT(*) AS total FROM `$table`");
    $row = mysqli_fetch_assoc($res);
    return (int)$row['total'];
}

$orderCount = count_table($conn, 'orders');
$menuCount  = count_table($conn, 'menu');
$userCount  = count_table($conn, 'users');

$totalSales = 0;
$res = mysqli_query($conn, "
    SELECT COALESCE(SUM(amount),0) AS total
    FROM payments
    WHERE status='completed'
");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $totalSales = $row['total'];
}

$logs = mysqli_query($conn, "
    SELECT 
        al.action,
        al.description,
        al.ip_address,
        al.created_at,
        u.name AS user_name
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 50
");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard | 56Food</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px
        }

        th {
            background: #f4f4f4
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a class="active" href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <h2>Admin Dashboard</h2>

            <div class="cards-container">
                <div class="card">
                    <h3>Orders</h3>
                    <p><?= $orderCount ?></p>
                </div>
                <div class="card">
                    <h3>Menus</h3>
                    <p><?= $menuCount ?></p>
                </div>
                <div class="card">
                    <h3>Users</h3>
                    <p><?= $userCount ?></p>
                </div>
                <div class="card">
                    <h3>Total Sales</h3>
                    <p>Tsh <?= number_format($totalSales) ?></p>
                </div>
            </div>

            <h3>Recent Activity Logs</h3>

            <table>
                <tr>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP</th>
                    <th>Date</th>
                </tr>

                <?php while ($log = mysqli_fetch_assoc($logs)): ?>
                    <tr>
                        <td><?= htmlspecialchars($log['user_name'] ?? 'System') ?></td>
                        <td><?= htmlspecialchars($log['action']) ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= $log['ip_address'] ?></td>
                        <td><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>

        </main>
    </div>

</body>

</html>