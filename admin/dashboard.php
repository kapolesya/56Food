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

$totalSales = 0.0;
// Use confirmed/delivered orders for dashboard 'Total Sales' so pending payments don't underreport
$res = mysqli_query($conn, "SELECT COALESCE(SUM(`total_amount`),0) AS total FROM `orders` WHERE `status` IN ('confirmed','delivered')");
if ($res) {
    $row = mysqli_fetch_assoc($res);
    $totalSales = (float) $row['total'];
} else {
    $totalSales = 0.0;
}

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
                <li><a href="activity_logs.php">Activity Logs</a></li>
                <li><a href="reports.php">Reports</a></li>
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
                    <p>$<?= number_format((float)$totalSales, 2) ?></p>
                </div>
            </div>

            <h3>Activity Logs</h3>

            <p><a href="activity_logs.php">View full activity logs &raquo;</a></p>

        </main>
    </div>

</body>

</html>