<?php
session_start();
require_once "include/conn.php";
require_once "include/auth.php"; // has require_login() function

$userId = $_SESSION['user_id'] ?? null;

// Fetch popular menu items
$menu_items = mysqli_query($conn, "SELECT * FROM menu LIMIT 6");

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['menu_id'])) {

    if (!$userId) {
        header("Location: login.php");
        exit;
    }

    $menuId   = (int)$_POST['menu_id'];
    $quantity = 1; // default for popular dishes

    // Check if item already exists in cart
    $sql = "SELECT id, quantity FROM cart WHERE user_id = ? AND menu_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $menuId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $existing = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $updateSql = "UPDATE cart SET quantity = ? WHERE id = ?";
        $stmt2 = mysqli_prepare($conn, $updateSql);
        mysqli_stmt_bind_param($stmt2, "ii", $newQty, $existing['id']);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    } else {
        $insertSql = "INSERT INTO cart (user_id, menu_id, quantity) VALUES (?, ?, ?)";
        $stmt2 = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($stmt2, "iii", $userId, $menuId, $quantity);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    }

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>56Food</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .features {
            display: flex;
            justify-content: center;
            gap: 50px;
            flex-wrap: wrap;
            text-align: center;
            margin-bottom: 50px;
        }

        .feature img {
            width: 96px;
            height: 96px;
            margin-bottom: 10px;
        }

        .feature h3 {
            margin: 10px 0 5px;
        }

        /* ===== MENU GRID ===== */
        .menu-items {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* 3 per row */
            gap: 20px;
            max-width: 800px;
            margin: auto;
        }

        .menu-item {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            height: 400px;
            /* height sawa */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
        }

        .menu-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            /* image zisipindike */
            border-radius: 8px;
        }

        .menu-item h4 {
            margin: 10px 0 5px;
        }

        .menu-item p {
            flex-grow: 1;
            /* description ijaze space */
            font-size: 14px;
        }

        .order-btn {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #ff6347;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .order-btn:hover {
            background-color: #e5533d;
        }

        /* ===== Responsive ===== */
        @media (max-width: 900px) {
            .menu-items {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .menu-items {
                grid-template-columns: 1fr;
            }
        }
    </style>

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
            <?php if ($userId): ?>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>)</a>
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
        <h2 style="width:100%; text-align:center; font-size:36px; color:#ff6347; font-weight:bold; text-transform:uppercase; letter-spacing:2px; margin-bottom:30px; border-bottom:3px solid #ff6347; display:inline-block;">
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

    <!-- Menu Section / Popular Dishes -->
    <section id="menu" class="menu">
        <h2 style="text-align:center;">Our Dishes</h2>
        <div class="menu-items">
            <?php
            // Fetch 6 latest available menu items
            $menu_items = mysqli_query($conn, "SELECT * FROM menu WHERE status='available' ORDER BY created_at DESC LIMIT 6");

            while ($item = mysqli_fetch_assoc($menu_items)):
                // Full image path
                $imgPath = !empty($item['image'])
                    ? 'assets/images/foods/' . htmlspecialchars($item['image'])
                    : 'https://via.placeholder.com/250x150.png?text=No+Image';
            ?>
                <div class="menu-item">
                    <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                    <p>Tsh <?= number_format($item['price'], 0) ?></p>
                    <p><?= htmlspecialchars($item['description'] ?? '') ?></p>
                    <form method="POST" action="cart.php">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="menu_id" value="<?= (int) $item['id'] ?>">
                        <button type="submit" class="order-btn">Add to Cart</button>
                    </form>
                </div>
            <?php endwhile; ?>
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
    <button id="back-to-top" style="display:none; position:fixed; bottom:20px; right:20px; padding:10px;">↑ Top</button>
    <script>
        //button ya kurudi juu//
        const backBtn = document.getElementById("back-to-top");

        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) backBtn.style.display = "block";
            else backBtn.style.display = "none";
        });

        backBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
        //user scoll//
        document.querySelectorAll('a[href^="#"]').forEach(link => {
            link.addEventListener("click", function(e) {
                const target = document.querySelector(this.getAttribute("href"));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: "smooth"
                    });
                }
            });
        });
    </script>



</body>

</html>