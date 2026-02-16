<?php
// admin/reports.php - Admin reports & analytics for 56Food

require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// ---------- Helpers ----------------------------------------------------------

function sanitize_date(string $d): ?string
{
    $ts = strtotime($d);
    return $ts ? date('Y-m-d', $ts) : null;
}

// Default filters
$view    = $_GET['view']   ?? 'daily';   // daily | weekly | monthly
$fromRaw = $_GET['from']   ?? date('Y-m-01');
$toRaw   = $_GET['to']     ?? date('Y-m-d');
$format  = $_GET['format'] ?? 'html';    // html | csv

$from = sanitize_date($fromRaw);
$to   = sanitize_date($toRaw);

// Ensure from/to valid
if (!$from || !$to || $from > $to) {
    $from = date('Y-m-01');
    $to   = date('Y-m-d');
}

// WHERE clause for orders in date range
$whereRange = "o.order_date BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59'";

// ---------- 1. Summary: total orders + sales in range -----------------------

$summary = [
    'total_orders' => 0,
    'total_sales'  => 0.0,
];

$sql = "SELECT COUNT(*) AS total_orders,
               COALESCE(SUM(o.total_amount),0) AS total_sales
        FROM orders o
        WHERE {$whereRange}";

if ($res = mysqli_query($conn, $sql)) {
    if ($row = mysqli_fetch_assoc($res)) {
        $summary = $row;
    }
    mysqli_free_result($res);
}

// ---------- 2. Time-based breakdown -----------------------------------------

$timeRows = [];

if ($view === 'daily') {
    $sql = "SELECT DATE(o.order_date) AS period,
                   COUNT(*) AS orders_count,
                   COALESCE(SUM(o.total_amount),0) AS sales
            FROM orders o
            WHERE {$whereRange}
            GROUP BY DATE(o.order_date)
            ORDER BY period DESC";
} elseif ($view === 'weekly') {
    $sql = "SELECT YEARWEEK(o.order_date, 1) AS period,
                   COUNT(*) AS orders_count,
                   COALESCE(SUM(o.total_amount),0) AS sales
            FROM orders o
            WHERE {$whereRange}
            GROUP BY YEARWEEK(o.order_date,1)
            ORDER BY period DESC";
} else { // monthly
    $sql = "SELECT DATE_FORMAT(o.order_date,'%Y-%m') AS period,
                   COUNT(*) AS orders_count,
                   COALESCE(SUM(o.total_amount),0) AS sales
            FROM orders o
            WHERE {$whereRange}
            GROUP BY DATE_FORMAT(o.order_date,'%Y-%m')
            ORDER BY period DESC";
}

if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $timeRows[] = $row;
    }
    mysqli_free_result($res);
}

// ---------- 3. Most ordered foods (using existing `menu` table) -------------

$topFoods = [];

$sql = "SELECT m.name,
               SUM(oi.quantity) AS total_qty,
               COALESCE(SUM(oi.quantity * oi.price),0) AS revenue
        FROM order_items oi
        JOIN orders o ON oi.order_id = o.id
        JOIN menu m   ON oi.menu_id  = m.id
        WHERE {$whereRange}
        GROUP BY oi.menu_id
        ORDER BY total_qty DESC
        LIMIT 10";

if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $topFoods[] = $row;
    }
    mysqli_free_result($res);
}

// ---------- 4. Revenue per day ----------------------------------------------

$dailyRevenue = [];

$sql = "SELECT DATE(o.order_date) AS day,
               COALESCE(SUM(o.total_amount),0) AS revenue
        FROM orders o
        WHERE {$whereRange}
        GROUP BY DATE(o.order_date)
        ORDER BY day ASC";

if ($res = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($res)) {
        $dailyRevenue[] = $row;
    }
    mysqli_free_result($res);
}

// ---------- Optional CSV export ---------------------------------------------

if ($format === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=admin-report-' . date('Ymd-His') . '.csv');

    $out = fopen('php://output', 'w');

    // Summary
    fputcsv($out, ['Summary']);
    fputcsv($out, ['From', $from, 'To', $to]);
    fputcsv($out, ['Total Orders', $summary['total_orders']]);
    fputcsv($out, ['Total Sales', $summary['total_sales']]);
    fputcsv($out, []);

    // Time breakdown
    fputcsv($out, ['Time Breakdown (' . $view . ')']);
    fputcsv($out, ['Period', 'Orders', 'Sales']);
    foreach ($timeRows as $r) {
        fputcsv($out, [$r['period'], $r['orders_count'], $r['sales']]);
    }
    fputcsv($out, []);

    // Top foods
    fputcsv($out, ['Top Foods']);
    fputcsv($out, ['Food', 'Total Qty', 'Revenue']);
    foreach ($topFoods as $r) {
        fputcsv($out, [$r['name'], $r['total_qty'], $r['revenue']]);
    }
    fputcsv($out, []);

    // Daily revenue
    fputcsv($out, ['Daily Revenue']);
    fputcsv($out, ['Day', 'Revenue']);
    foreach ($dailyRevenue as $r) {
        fputcsv($out, [$r['day'], $r['revenue']]);
    }

    fclose($out);
    exit();
}

// ---------- HTML output ------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Reports | 56Food</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <style>
        main {
            padding: 20px;
        }

        .filters {
            margin-bottom: 20px;
        }

        .filters form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filters label {
            font-size: 0.9rem;
            display: block;
        }

        .filters input,
        .filters select {
            padding: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 0.9rem;
        }

        table th {
            background: #f5f5f5;
            text-align: left;
        }

        .summary-box {
            background: #f9fafb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .summary-box p {
            margin: 4px 0;
        }

        .btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-secondary {
            background: #6b7280;
            color: #fff;
        }

        .btn-print {
            background: #10b981;
            color: #fff;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: #fff;
            }
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar no-print">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="activity_logs.php">Activity Logs</a></li>
                <li><a href="reports.php" class="active">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main>

            <header class="no-print" style="margin-bottom:20px;">
                <h3>Reports & Analytics</h3>
            </header>

            <!-- Filters -->
            <section class="filters no-print">
                <form method="GET" action="reports.php">
                    <div>
                        <label>View Type</label>
                        <select name="view">
                            <option value="daily" <?= $view === 'daily' ? 'selected' : '' ?>>Daily</option>
                            <option value="weekly" <?= $view === 'weekly' ? 'selected' : '' ?>>Weekly</option>
                            <option value="monthly" <?= $view === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                        </select>
                    </div>
                    <div>
                        <label>From</label>
                        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
                    </div>
                    <div>
                        <label>To</label>
                        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    <div>
                        <a href="reports.php?view=<?= urlencode($view) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>&format=csv"
                           class="btn btn-secondary">Export CSV</a>
                    </div>
                    <div>
                        <button type="button" onclick="window.print();" class="btn btn-print">Print</button>
                    </div>
                </form>
            </section>

            <!-- Summary -->
            <section class="summary-box">
                <h4>Summary (<?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?>)</h4>
                <p><strong>Total Orders:</strong> <?= (int) $summary['total_orders'] ?></p>
                <p><strong>Total Sales:</strong> $<?= number_format((float) $summary['total_sales'], 2) ?></p>
            </section>

            <!-- Time breakdown -->
            <section>
                <h4><?= ucfirst($view) ?> Breakdown</h4>
                <?php if (empty($timeRows)): ?>
                    <p>No data available for this period.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th><?= $view === 'weekly' ? 'Year-Week' : ($view === 'monthly' ? 'Month' : 'Date') ?></th>
                                <th>Orders</th>
                                <th>Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timeRows as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['period']) ?></td>
                                    <td><?= (int) $row['orders_count'] ?></td>
                                    <td>$<?= number_format((float) $row['sales'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- Top foods -->
            <section>
                <h4>Most Ordered Foods</h4>
                <?php if (empty($topFoods)): ?>
                    <p>No food orders in this period.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Food</th>
                                <th>Total Quantity</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topFoods as $f): ?>
                                <tr>
                                    <td><?= htmlspecialchars($f['name']) ?></td>
                                    <td><?= (int) $f['total_qty'] ?></td>
                                    <td>$<?= number_format((float) $f['revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

            <!-- Daily revenue -->
            <section>
                <h4>Revenue Per Day</h4>
                <?php if (empty($dailyRevenue)): ?>
                    <p>No revenue data for this period.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailyRevenue as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['day']) ?></td>
                                    <td>$<?= number_format((float) $r['revenue'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </section>

        </main>

    </div>

</body>

</html>

