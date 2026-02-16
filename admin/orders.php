<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $orderId = (int) ($_POST['order_id'] ?? 0);

    if ($orderId > 0 && $action === 'update_status') {
        $newStatus = $_POST['status'] ?? 'pending';
        if (!in_array($newStatus, ['pending', 'confirmed', 'delivered', 'cancelled'], true)) {
            $newStatus = 'pending';
        }
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $newStatus, $orderId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: orders.php");
    exit();
}

// Fetch all orders with user + payment info
$orders = [];
$sql = "SELECT o.id,
               o.total_amount,
               o.status,
               o.order_date,
               u.name AS customer_name,
               u.email AS customer_email,
               COALESCE(p.payment_method, 'cash') AS payment_method,
               COALESCE(p.status, 'pending') AS payment_status
        FROM orders o
        JOIN users u       ON o.user_id = u.id
        LEFT JOIN payments p ON p.order_id = o.id
        ORDER BY o.order_date DESC";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders | 56Food</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php" class="active">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="activity_logs.php">Activity Logs</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Manage Orders</h3>
            </header>

            <!-- ================= ORDERS ================= -->
            <section class="tab-panel active" id="orders">
                <h4>All Orders</h4>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Order Status</th>
                            <th>Payment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= (int) $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['order_date']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($order['customer_name']) ?><br>
                                        <small><?= htmlspecialchars($order['customer_email']) ?></small>
                                    </td>
                                    <td>$<?= number_format((float) $order['total_amount'], 2) ?></td>
                                    <td>
                                        <form method="POST" action="orders.php">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                            <select name="status" onchange="this.form.submit()">
                                                <option value="pending"<?= $order['status'] === 'pending' ? ' selected' : '' ?>>Pending</option>
                                                <option value="confirmed"<?= $order['status'] === 'confirmed' ? ' selected' : '' ?>>Confirmed</option>
                                                <option value="cancelled"<?= $order['status'] === 'cancelled' ? ' selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars(ucfirst($order['payment_method'])) ?>
                                        (<?= htmlspecialchars($order['payment_status']) ?>)
                                    </td>
                                    <td>
                                        <a href="view_order.php?id=<?= (int) $order['id'] ?>" class="btn view" style="color:green; text-decoration:none;">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

        </main>
    </div>

</body>

</html>