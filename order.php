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
            <a href="login.php">Login</a>
        </nav>
    </header>

    <!-- ORDER SECTION -->
    <section class="order-section">
        <h2>Place Your Order</h2>

        <div class="order-container">

            <!-- ORDER SUMMARY -->
            <div class="order-summary">
                <h3>Order Summary</h3>

                <div class="summary-item">
                    <span>Cheese Burger x1</span>
                    <span>$5.99</span>
                </div>

                <div class="summary-item">
                    <span>Pepperoni Pizza x1</span>
                    <span>$9.99</span>
                </div>

                <hr>

                <div class="summary-total">
                    <strong>Total:</strong>
                    <strong>$15.98</strong>
                </div>
            </div>

            <!-- ORDER FORM -->
            <form class="order-form" id="orderForm">
                <h3>Delivery Details</h3>

                <input type="text" name="full_name" placeholder="Full Name" required>
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