<?php
session_start();

// Hapa unaweza ku-check kama mtu amelogin
$isLoggedIn = isset($_SESSION['user_id']); // true ikiwa amelogin
$username = $isLoggedIn ? $_SESSION['user_name'] : ''; // jina la user
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menu | 56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/menu.css">
    <style>
        .menu-section {
            padding: 50px 20px;
            background: #f8f8f8;
            text-align: center;
        }

        .menu-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .food-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 15px;
            transition: transform 0.2s;
        }

        .food-card:hover {
            transform: translateY(-5px);
        }

        .food-card img {
            width: 100%;
            border-radius: 10px;
        }

        .food-card h4 {
            margin: 10px 0 5px;
            color: #ff6347;
        }

        .food-card p#price {
            font-weight: bold;
            margin: 5px 0;
        }

        .food-card p#description {
            font-size: 0.9rem;
            color: #555;
            height: 50px;
            overflow: hidden;
        }

        .add-to-cart {
            background: #ff6347;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .add-to-cart:hover {
            background: #e5533d;
        }

        nav a {
            margin-right: 15px;
            text-decoration: none;
            color: white;
            font-weight: 600;
        }

        nav a.active {
            color: #ff6347;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <header>
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="index.php#features">Features</a>
            <a href="index.php#about">About</a>

            <?php if ($isLoggedIn): ?>
                <!-- Ikiwa amelogin onyesha jina na logout -->
                <a href="logout.php">Logout (<?= htmlspecialchars($username) ?>)</a>
            <?php else: ?>
                <!-- Ikiwa bado hajalogin onyesha login -->
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- MENU SECTION -->
    <section class="menu-section">
        <h2>Our Menu</h2>

        <div class="menu-container">

            <!-- FOOD CARD -->
            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80">
                <h4>Cheese Burger</h4>
                <p id="price">$5.99</p>
                <p id="description">Crispy fried chicken with herbs and spices.
                    Crispy fried chicken with herbs and spices
                </p>
                <button class="add-to-cart"
                    data-id="1"
                    data-name="Cheese Burger"
                    data-price="5.99">
                    Add to Cart
                </button>
            </div>

            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1548365328-9f547fb0953c?fit=crop&w=400&q=80">
                <h4>Pepperoni Pizza</h4>
                <p id="price">$9.99</p>
                <p id="description">Crispy fried chicken with herbs and spices.
                    Crispy fried chicken with herbs and spices
                </p>
                <button class="add-to-cart"
                    data-id="2"
                    data-name="Pepperoni Pizza"
                    data-price="9.99">
                    Add to Cart
                </button>
            </div>

            <div class="food-card">
                <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?fit=crop&w=400&q=80">
                <h4>Fried Chicken</h4>
                <p id="price">$7.50</p>
                <p id="description">Crispy fried chicken with herbs and spices.
                    Crispy fried chicken with herbs and spices
                </p>
                <button class="add-to-cart"
                    data-id="3"
                    data-name="Fried Chicken"
                    data-price="7.50">
                    Add to Cart
                </button>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 56Food. All rights reserved.</p>
        <p>Designed with ❤️ for food lovers</p>
    </footer>

</body>

</html>