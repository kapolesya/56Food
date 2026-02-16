<?php
session_start();
require_once "include/conn.php";
require_once "include/auth.php"; // has require_login() function

// Get user ID from session if logged in
$userId = $_SESSION['user_id'] ?? null;
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

        .menu-items {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .menu-item {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            width: 250px;
            text-align: center;
        }

        .menu-item img {
            width: 100%;
            border-radius: 8px;
        }

        .order-btn {
            margin-top: 10px;
            padding: 10px 15px;
            background-color: #ff6347;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
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

    /* ✅ HAPA NDIPO TUMEBADILISHA */
    .menu-items {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 cards per row */
        gap: 20px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .menu-item {
        border: 1px solid #ccc;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        background: #fff;
    }

    .menu-item img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 8px;
    }

    .order-btn {
        margin-top: 10px;
        padding: 10px 15px;
        background-color: #ff6347;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .order-btn:hover {
        background-color: #e5533d;
    }

    /* ✅ Responsive */
    @media (max-width: 992px) {
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
                <?php
                    $cartCount = 0;
                    $cstmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(quantity),0) as total FROM cart WHERE user_id = ?");
                    if ($cstmt) {
                        mysqli_stmt_bind_param($cstmt, "i", $userId);
                        mysqli_stmt_execute($cstmt);
                        $cres = mysqli_stmt_get_result($cstmt);
                        $crow = mysqli_fetch_assoc($cres);
                        $cartCount = (int)($crow['total'] ?? 0);
                        mysqli_stmt_close($cstmt);
                    }
                ?>
                <a href="cart.php">Cart <span id="cartCount"><?= $cartCount ?></span></a>
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
        <h2 style="text-align:center;">Our Popular Dishes</h2>
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

    <!-- Back to top button -->
    <button id="scrollTopBtn" title="Back to top" aria-label="Back to top">▲</button>

    <style>
    /* Back-to-top button styling */
    #scrollTopBtn {
        position: fixed;
        right: 20px;
        bottom: 28px;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: none;
        background: #ff6347;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        display: none; /* shown via JS when scrolled down */
        align-items: center;
        justify-content: center;
    }
    #scrollTopBtn:focus { outline: 2px solid #fff; outline-offset: 2px; }
    </style>

    <script src="assets/js/site.js"></script>

    <script>
    (function () {
        const btn = document.getElementById('scrollTopBtn');
        const showAfter = 300; // px scrolled before showing button

        function toggleBtn() {
            if (window.scrollY > showAfter) {
                btn.style.display = 'flex';
            } else {
                btn.style.display = 'none';
            }
        }

        // Smooth scroll to top
        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // show/hide on scroll and on load
        window.addEventListener('scroll', toggleBtn);
        window.addEventListener('load', toggleBtn);
    })();
    </script>
</body>

</html>