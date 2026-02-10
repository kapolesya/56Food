<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

$orderId = (int) ($_GET['id'] ?? 0);
if ($orderId <= 0) {
    header("Location: orders.php");
    exit();
}

// Fetch order + user + payment
$order = null;

$sql = "SELECT o.id,
               o.total_amount,
               o.status,
               o.order_date,
               u.name AS customer_name,
               u.email AS customer_email,
               u.phone AS customer_phone,
               COALESCE(p.payment_method, 'cash') AS payment_method,
               COALESCE(p.status, 'pending') AS payment_status
        FROM orders o
        JOIN users u        ON o.user_id = u.id
        LEFT JOIN payments p ON p.order_id = o.id
        WHERE o.id = ?
        LIMIT 1";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch items
$items = [];
$sql = "SELECT oi.quantity,
               oi.price,
               m.name
        FROM order_items oi
        JOIN menu m ON oi.menu_id = m.id
        WHERE oi.order_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Order | 56Food</title>
    <style>
        /* ===== VIEW ORDER SIMPLE ===== */

        .detail-card {
            background: #fff;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .detail-card h4 {
            margin-bottom: 12px;
            color: #ff6b00;
        }

        .detail-card p {
            margin: 8px 0;
            font-size: 15px;
            color: #444;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }
    </style>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR (Admin) -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active"><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Order Details #<?= (int) $order['id'] ?></h3>
            </header>

            <section class="tab-panel active">

                <!-- CUSTOMER & PAYMENT DETAILS -->
                <div class="detail-card">
                    <h4>Customer & Payment</h4>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['customer_phone'] ?? 'N/A') ?></p>
                    <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                    <p><strong>Order Status:</strong> <?= htmlspecialchars(ucfirst($order['status'])) ?></p>
                    <p><strong>Payment:</strong> <?= htmlspecialchars(ucfirst($order['payment_method'])) ?> (<?= htmlspecialchars($order['payment_status']) ?>)</p>
                </div>

                <!-- ORDER ITEMS -->
                <div class="detail-card">
                    <h4>Items</h4>
                    <?php if (empty($items)): ?>
                        <p>No items found for this order.</p>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <p>
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                &times; <?= (int) $item['quantity'] ?>
                                — $<?= number_format((float) $item['price'] * $item['quantity'], 2) ?>
                            </p>
                        <?php endforeach; ?>
                        <p><strong>Total Price:</strong> <span class="price">$<?= number_format((float) $order['total_amount'], 2) ?></span></p>
                    <?php endif; ?>
                </div>

                <!-- ACTION -->
                <div class="form-actions">
                    <a href="orders.php" class="btn delete">Back</a>
                </div>

            </section>

        </main>
    </div>

</body>

</html>