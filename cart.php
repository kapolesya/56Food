<?php
session_start();

// User must be logged in to view cart
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Example: Cart items stored in session (replace with DB if you have)
$cartItems = $_SESSION['cart'] ?? [
    [
        "name" => "Cheese Burger",
        "price" => 5.99,
        "quantity" => 1,
        "img" => "https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80"
    ],
    [
        "name" => "Pepperoni Pizza",
        "price" => 9.99,
        "quantity" => 1,
        "img" => "https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80"
    ]
];

// Calculate totals
$totalItems = 0;
$totalPrice = 0;
foreach ($cartItems as $item) {
    $totalItems += $item['quantity'];
    $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart | 56Food</title>
    <link rel="stylesheet" href="assets/css/cart.css">
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>

    <header>
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#features">Features</a>
            <a href="menu.php">Menu</a>
            <a href="index.php#about">About</a>

            <!-- Show Cart and Logout if user is logged in -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="cart-section">
        <h2>Your Cart</h2>

        <div class="cart-container">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item">
                    <img src="<?= $item['img'] ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="cart-details">
                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                        <p>$<?= number_format($item['price'], 2) ?></p>
                        <input type="number" value="<?= $item['quantity'] ?>" min="1">
                    </div>
                    <button class="remove-btn">Remove</button>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <p>Total Items: <span><?= $totalItems ?></span></p>
                <p>Total Price: <strong>$<?= number_format($totalPrice, 2) ?></strong></p>
                <button class="checkout-btn"><a href="order.php">Proceed to Checkout</a></button>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 56Food. All rights reserved.</p>
        <p>Designed with ❤️ for food lovers</p>
    </footer>

</body>

</html>