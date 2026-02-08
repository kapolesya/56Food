<?php
require_once __DIR__ . "/include/conn.php";
require_once __DIR__ . "/include/auth.php";
require_once __DIR__ . "/include/activity_log.php";

require_login();

$userId   = (int)($_SESSION['user_id'] ?? 0);
$userName = $_SESSION['user_name'] ?? '';

$errors  = [];
$success = '';

// ================================
// Log: View Orders Page
// ================================
log_activity(
    $conn,
    $userId,
    'VIEW_ORDERS',
    'User viewed their orders'
);

// ================================
// Handle Cancel Order
// ================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['cancel_order_id'])) {
    $orderId = (int)$_POST['cancel_order_id'];

    $sql = "SELECT status FROM orders WHERE id=? AND user_id=? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order  = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($order && in_array($order['status'], ['pending', 'confirmed'])) {
        $updateSql = "UPDATE orders SET status='cancelled' WHERE id=? AND user_id=?";
        $uStmt = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($uStmt, "ii", $orderId, $userId);
        mysqli_stmt_execute($uStmt);
        mysqli_stmt_close($uStmt);

        // Log cancel activity
        log_activity(
            $conn,
            $userId,
            'CANCEL_ORDER',
            "Cancelled order ID #$orderId"
        );

        $success = "Order #$orderId has been cancelled.";
    } else {
        $errors[] = "This order cannot be cancelled.";
    }
}

// ================================
// Fetch Orders
// ================================
$orders = [];

$sql = "
    SELECT o.id, o.total_amount, o.status, o.order_date,
           COALESCE(p.status,'pending') AS payment_status,
           COALESCE(p.payment_method,'cash') AS payment_method
    FROM orders o
    LEFT JOIN payments p ON p.order_id=o.id
    WHERE o.user_id=?
    ORDER BY o.order_date DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($res)) {
    $orders[$row['id']] = $row;
    $orders[$row['id']]['items'] = [];
}
mysqli_stmt_close($stmt);

// ================================
// Fetch Order Items
// ================================
if (!empty($orders)) {
    $orderIds = implode(',', array_keys($orders));
    $sqlItems = "
        SELECT oi.order_id, m.name, oi.quantity
        FROM order_items oi
        JOIN menu m ON m.id = oi.menu_id
        WHERE oi.order_id IN ($orderIds)
    ";
    $resItems = mysqli_query($conn, $sqlItems);

    while ($row = mysqli_fetch_assoc($resItems)) {
        $orders[$row['order_id']]['items'][] = $row;
    }
}

// ================================
// Totals
// ================================
$totalOrders = count($orders);
$totalSpent  = 0;
foreach ($orders as $o) {
    $totalSpent += (float)$o['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Orders | 56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        header {
            background: #ff6347;
            padding: 15px;
            color: #fff;
            text-align: center;
        }

        header nav a {
            color: white;
            margin: 0 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .orders-section {
            max-width: 1000px;
            margin: auto;
            padding: 40px 20px;
        }

        .orders-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        th {
            background: #f5f5f5;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #cce5ff;
            color: #004085;
        }

        .status-delivered {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .btn-print {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 4px;
            cursor: pointer;
        }

        .cancel-btn {
            background: red;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <header class="no-print">
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="logout.php">Logout (<?= htmlspecialchars($userName) ?>)</a>
        </nav>
    </header>

    <section class="orders-section">
        <h2>My Orders</h2>

        <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>
        <?php foreach ($errors as $e): ?><p style="color:red;"><?= $e ?></p><?php endforeach; ?>

        <div class="orders-summary">
            <p><strong>Total Orders:</strong> <?= $totalOrders ?></p>
            <p><strong>Total Spent:</strong> $<?= number_format($totalSpent, 2) ?></p>
        </div>

        <?php if (empty($orders)): ?>
            <p>No orders yet. <a href="menu.php">View Menu</a></p>
        <?php else: ?>
            <table>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th class="no-print">Action</th>
                </tr>

                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td><?= $order['order_date'] ?></td>
                        <td>
                            <ul>
                                <?php foreach ($order['items'] as $item): ?>
                                    <li><?= htmlspecialchars($item['name']) ?> x <?= $item['quantity'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td><?= ucfirst($order['payment_method']) ?> (<?= $order['payment_status'] ?>)</td>
                        <td class="no-print">
                            <?php if (in_array($order['status'], ['pending', 'confirmed'])): ?>
                                <form method="post">
                                    <input type="hidden" name="cancel_order_id" value="<?= $order['id'] ?>">
                                    <button class="cancel-btn">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <br>
            <button class="btn-print no-print" onclick="window.print()">Print Orders</button>
        <?php endif; ?>
    </section>

</body>

</html>