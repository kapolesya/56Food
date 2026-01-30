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

        </nav>
    </header>


    <div class="login-container"></div>
    <section class="cart-section">
        <h2>Your Cart</h2>

        <div class="cart-container">

            <div class="cart-item">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80">
                <div class="cart-details">
                    <h4>Cheese Burger</h4>
                    <p>$5.99</p>
                    <input type="number" value="1" min="1">
                </div>
                <button class="remove-btn">Remove</button>
            </div>

            <div class="cart-item">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80">
                <div class="cart-details">
                    <h4>Pepperoni Pizza</h4>
                    <p>$9.99</p>
                    <input type="number" value="1" min="1">
                </div>
                <button class="remove-btn">Remove</button>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <p>Total Items: <span>2</span></p>
                <p>Total Price: <strong>$15.98</strong></p>
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