<?php
session_start();

require_once "include/conn.php";

// Check if user is logged in (for navbar + add-to-cart behaviour)
$isLoggedIn = isset($_SESSION['user_id']); // true ikiwa amelogin
$username = $isLoggedIn ? $_SESSION['user_name'] : ''; // jina la user

// Fetch available menu items from database
$menuItems = [];

$sql = "SELECT id, name, description, price, image 
        FROM menu 
        WHERE status = 'available'
        ORDER BY created_at DESC";

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $menuItems[] = $row;
    }
    mysqli_stmt_close($stmt);
}
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
            <?php if ($isLoggedIn): ?>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($username) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- MENU SECTION -->
    <section class="menu-section">
        <h2>Our Menu</h2>

        <div class="menu-container">

            <?php if (empty($menuItems)): ?>
                <p>No menu items available at the moment. Please check back later.</p>
            <?php else: ?>
                <?php foreach ($menuItems as $item): ?>
                    <div class="food-card">
                        <?php
                        // Use uploaded image if present, otherwise a placeholder
                        $imgSrc = !empty($item['image'])
                            ? 'assets/images/foods/' . htmlspecialchars($item['image'])
                            : 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80';
                        ?>
                        <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                        <p id="price">$<?= number_format($item['price'], 2) ?></p>
                        <p id="description">
                            <?= htmlspecialchars($item['description'] ?? '') ?>
                        </p>

                        <!-- Add to cart uses a simple POST form -->
                        <form method="POST" action="cart.php" style="margin-top:10px;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="menu_id" value="<?= (int) $item['id'] ?>">
                            <input type="number" name="quantity" value="1" min="1"
                                style="width:60px; padding:6px; margin-right:6px;">
                            <button type="submit" class="add-to-cart">
                                Add to Cart
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2026 56Food. All rights reserved.</p>
        <p>Designed with ❤️ for food lovers</p>
    </footer>

</body>

</html>