<?php
session_start();

require_once "include/conn.php";
require_once "include/auth.php";

// User must be logged in to place order
require_login();

$userId   = (int) $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? '';

$errors = [];
$successMessage = '';

// ── Fetch cart items from DB ─────────────────────────────────────────────
$cartItems = [];
$totalPrice = 0.0;

$sql = "SELECT c.id, c.menu_id, c.quantity, m.name, m.price 
        FROM cart c
        INNER JOIN menu m ON c.menu_id = m.id
        WHERE c.user_id = ?";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $cartItems[] = $row;
        $totalPrice += $row['price'] * $row['quantity'];
    }

    mysqli_stmt_close($stmt);
}

// Handle case: empty cart
if (empty($cartItems) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errors[] = "Your cart is empty. Please add items from the menu first.";
}

// ── Handle checkout submission ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($cartItems)) {
    $fullName      = trim($_POST['full_name'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? '';

    if ($fullName === '') {
        $errors[] = "Full name is required.";
    }
    if ($phone === '') {
        $errors[] = "Phone number is required.";
    }
    if ($address === '') {
        $errors[] = "Delivery address is required.";
    }
    if (!in_array($paymentMethod, ['cash', 'mobile', 'card'], true)) {
        $errors[] = "Please select a valid payment method.";
    }

    if (empty($errors)) {
        // We will use a transaction so everything succeeds or fails together.
        mysqli_begin_transaction($conn);

        try {
            // 1. Create order
            $orderSql = "INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')";
            if (!$stmtOrder = mysqli_prepare($conn, $orderSql)) {
                throw new Exception("Failed to prepare order insert.");
            }
            mysqli_stmt_bind_param($stmtOrder, "id", $userId, $totalPrice);
            mysqli_stmt_execute($stmtOrder);
            $orderId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmtOrder);

            // 2. Create order items
            $itemSql = "INSERT INTO order_items (order_id, menu_id, quantity, price) 
                        VALUES (?, ?, ?, ?)";
            if (!$stmtItem = mysqli_prepare($conn, $itemSql)) {
                throw new Exception("Failed to prepare order items insert.");
            }

            foreach ($cartItems as $item) {
                $menuId   = (int) $item['menu_id'];
                $qty      = (int) $item['quantity'];
                $price    = (float) $item['price']; // price per unit at time of order

                mysqli_stmt_bind_param($stmtItem, "iiid", $orderId, $menuId, $qty, $price);
                mysqli_stmt_execute($stmtItem);
            }
            mysqli_stmt_close($stmtItem);

            // 3. Record payment entry
            // Decide initial payment status: for simplicity, card = completed, others = pending
            $paymentStatus = $paymentMethod === 'card' ? 'completed' : 'pending';

            $paySql = "INSERT INTO payments (order_id, amount, payment_method, status)
                       VALUES (?, ?, ?, ?)";
            if (!$stmtPay = mysqli_prepare($conn, $paySql)) {
                throw new Exception("Failed to prepare payment insert.");
            }

            mysqli_stmt_bind_param($stmtPay, "idss", $orderId, $totalPrice, $paymentMethod, $paymentStatus);
            mysqli_stmt_execute($stmtPay);
            mysqli_stmt_close($stmtPay);

            // 4. Clear cart
            $clearSql = "DELETE FROM cart WHERE user_id = ?";
            if ($stmtClear = mysqli_prepare($conn, $clearSql)) {
                mysqli_stmt_bind_param($stmtClear, "i", $userId);
                mysqli_stmt_execute($stmtClear);
                mysqli_stmt_close($stmtClear);
            }

            mysqli_commit($conn);

            $successMessage = "Order placed successfully! Your order ID is #" . $orderId;
            // Recompute cart items as empty
            $cartItems = [];
            $totalPrice = 0.0;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $errors[] = "Failed to place order. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout | 56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/order.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- ORDER SECTION -->
    <section class="order-section">
        <h2>Place Your Order</h2>

        <?php if (!empty($errors)): ?>
            <div style="color:#721c24; background:#f8d7da; padding:12px; margin:10px auto; max-width:600px; border:1px solid #f5c6cb; border-radius:6px;">
                <strong>There was a problem:</strong><br>
                • <?= implode('<br>• ', array_map('htmlspecialchars', $errors)) ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div style="color:#155724; background:#d4edda; padding:12px; margin:10px auto; max-width:600px; border:1px solid #c3e6cb; border-radius:6px;">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <div class="order-container">

            <!-- ORDER SUMMARY -->
            <div class="order-summary">
                <h3>Order Summary</h3>

                <?php if (empty($cartItems)): ?>
                    <p>Your cart is empty. <a href="menu.php">Go back to menu</a>.</p>
                <?php else: ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="summary-item">
                            <span><?= htmlspecialchars($item['name']) ?> x<?= (int) $item['quantity'] ?></span>
                            <span>Tsh <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>

                    <hr>

                    <div class="summary-total">
                        <strong>Total:</strong>
                        <strong>Tsh <?= number_format($totalPrice, 2) ?></strong>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ORDER FORM -->
            <form class="order-form" id="orderForm" method="POST" action="order.php">
                <h3>Delivery Details</h3>

                <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($userName) ?>" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Delivery Address" required>

                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash">Cash on Delivery</option>
                    <option value="mobile">Mobile Payment</option>
                    <option value="card">Card</option>
                </select>

                <button type="submit" <?= empty($cartItems) ? ' disabled' : '' ?>>Confirm Order</button>
            </form>

        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 56Food. All rights reserved.</p>
        <p>Designed with ❤️ for food lovers</p>
    </footer>

</body>

</html>