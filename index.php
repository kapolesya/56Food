<?php
session_start(); // Start session to check if user is logged in
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>

<body>
    <!-- Header -->
    <header>
        <h1>56Food</h1>
        <nav>
            <a href="#hero">Home</a>
            <a href="#features">Features</a>
            <a href="menu.php">Menu</a>
            <a href="#about">About</a>

            <!-- Dynamic Navigation -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="hero" class="hero">
        <h2>Delicious Food Delivered Fast</h2>
        <p>Order your favorite meals online and get them at your doorstep.</p>
        <a href="#menu">Order Now</a>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <h2 style="text-align: center; font-size: 36px; color: #ff6347; margin: 40px 0 30px 0; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1); padding: 20px 0; border-bottom: 3px solid #ff6347; display: inline-block; width: 100%;">
            Why You Choose Us?</h2>
        <div class="feature">
            <img src="https://img.icons8.com/color/96/rocket.png" alt="Fast Delivery">
            <h3>Fast Delivery</h3>
            <p>Get your food hot and fresh in record time, right at your doorstep.</p>
        </div>
        <div class="feature">
            <img src="https://img.icons8.com/emoji/96/000000/credit-card-emoji.png" alt="Secure Payment">
            <h3>Secure Payment</h3>
            <p>Pay safely using card, mobile money, or cash on delivery.</p>
        </div>
        <div class="feature">
            <img src="https://img.icons8.com/emoji/96/000000/fork-and-knife-emoji.png" alt="Variety Menu">
            <h3>Variety Menu</h3>
            <p>Choose from hundreds of dishes, from local favorites to international cuisine.</p>
        </div>
    </section>

    <!-- Menu Section -->
    <section id="menu" class="menu">
        <h2>Our Popular Dishes</h2>

        <!-- Search Box with Button -->
        <div id="searchBox" style="margin-bottom:30px;">
            <input type="text" id="searchInput" placeholder="Search for a dish..."
                style="padding:10px; width:70%; max-width:300px; border-radius:5px; border:1px solid #ccc;">
            <button id="searchBtn"
                style="padding:10px 15px; border:none; background-color:#ff6347; color:white; border-radius:5px; cursor:pointer;">Search</button>
        </div>

        <div class="menu-items">
            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80"
                    alt="Burger">
                <h4>Cheese Burger</h4>
                <p>$5.99</p>
                <p id="food-discription">Join thousands of happy customers who trust 56Food for their daily meals!</p>
                <button class="order-btn">Add to Cart</button>
            </div>
            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80" alt="Pizza">
                <h4>Pepperoni Pizza</h4>
                <p>$9.99</p>
                <p id="food-discription">Join thousands of happy customers who trust 56Food for their daily meals!</p>
                <button class="order-btn">Add to Cart</button>
            </div>
            <div class="menu-item">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80" alt="Pasta">
                <h4>Italian Pasta</h4>
                <p>$7.99</p>
                <p id="food-discription">Join thousands of happy customers who trust 56Food for their daily meals!</p>
                <button class="order-btn">Add to Cart</button>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <h2>About Us</h2>
        <p>56Food is your go-to online food ordering platform, delivering delicious meals from our restaurant directly
            to your doorstep. Our mission is to make ordering food fast, simple, and reliable while offering a wide
            variety of dishes to satisfy every craving.</p>
        <p>We value quality, speed, and customer satisfaction. Join thousands of happy customers who trust 56Food for
            their daily meals!</p>
    </section>

    <footer>
        <p>&copy; 2026 56Food. All rights reserved.</p>
        <p>Designed with ❤️ for food lovers</p>
    </footer>

</body>

</html>