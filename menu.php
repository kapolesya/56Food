<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menu | 56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/menu.css">
</head>

<body>

    <!-- HEADER -->
    <header>
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php" class="active">Menu</a>
            <a href="login.php">Login</a>
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
                <p>$5.99</p>
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
                <p>$9.99</p>
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
                <p>$7.50</p>
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