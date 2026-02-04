<?php
session_start();

// User must be logged in to place order
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Example cart items (replace with actual session or DB cart)
$cartItems = $_SESSION['cart'] ?? [
    ["name" => "Cheese Burger", "quantity" => 1, "price" => 5.99],
    ["name" => "Pepperoni Pizza", "quantity" => 1, "price" => 9.99]
];

// Calculate totals
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
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

        <div class="order-container">

            <!-- ORDER SUMMARY -->
            <div class="order-summary">
                <h3>Order Summary</h3>

                <?php foreach ($cartItems as $item): ?>
                    <div class="summary-item">
                        <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                        <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>

                <hr>

                <div class="summary-total">
                    <strong>Total:</strong>
                    <strong>$<?= number_format($totalPrice, 2) ?></strong>
                </div>
            </div>

            <!-- ORDER FORM -->
            <form class="order-form" id="orderForm" method="POST" action="process_order.php">
                <h3>Delivery Details</h3>

                <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($_SESSION['user_name']) ?>" required>
                <input type="tel" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Delivery Address" required>

                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cash">Cash on Delivery</option>
                    <option value="mobile_money">Mobile Money</option>
                </select>

                <button type="submit">Confirm Order</button>
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