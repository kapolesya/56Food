<?php
session_start();
require_once "include/conn.php";
require_once "include/auth.php";

require_login();
$userId = (int)$_SESSION['user_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $menuId   = (int)($_POST['menu_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        if ($menuId > 0) {
            $sql = "SELECT id, quantity FROM cart WHERE user_id=? AND menu_id=? LIMIT 1";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ii", $userId, $menuId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $existing = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);

                if ($existing) {
                    $newQty = $existing['quantity'] + $quantity;
                    $updateSql = "UPDATE cart SET quantity=? WHERE id=?";
                    if ($uStmt = mysqli_prepare($conn, $updateSql)) {
                        mysqli_stmt_bind_param($uStmt, "ii", $newQty, $existing['id']);
                        mysqli_stmt_execute($uStmt);
                        mysqli_stmt_close($uStmt);
                    }
                } else {
                    $insertSql = "INSERT INTO cart (user_id, menu_id, quantity) VALUES (?,?,?)";
                    if ($iStmt = mysqli_prepare($conn, $insertSql)) {
                        mysqli_stmt_bind_param($iStmt, "iii", $userId, $menuId, $quantity);
                        mysqli_stmt_execute($iStmt);
                        mysqli_stmt_close($iStmt);
                    }
                }
            }
        }

        // If request is AJAX (frontend sends ajax=1), return JSON with updated cart count
        if (!empty($_POST['ajax']) && $_POST['ajax'] === '1') {
            $countSql = "SELECT COALESCE(SUM(quantity),0) AS total FROM cart WHERE user_id = ?";
            if ($cstmt = mysqli_prepare($conn, $countSql)) {
                mysqli_stmt_bind_param($cstmt, "i", $userId);
                mysqli_stmt_execute($cstmt);
                $cres = mysqli_stmt_get_result($cstmt);
                $crow = mysqli_fetch_assoc($cres);
                mysqli_stmt_close($cstmt);
                $totalItems = (int)($crow['total'] ?? 0);
            } else {
                $totalItems = 0;
            }

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'message' => 'Added to cart', 'totalItems' => $totalItems]);
            exit();
        }

        header("Location: cart.php");
        exit();
    }

    // ✅ UPDATE OR REMOVE FROM SAME FORM
    if ($action === 'update') {
        // Remove single item
        if (!empty($_POST['remove_id'])) {
            $cartId = (int)$_POST['remove_id'];
            $deleteSql = "DELETE FROM cart WHERE id=? AND user_id=?";
            if ($stmt = mysqli_prepare($conn, $deleteSql)) {
                mysqli_stmt_bind_param($stmt, "ii", $cartId, $userId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        // Update quantities
        if (!empty($_POST['items']) && is_array($_POST['items'])) {
            foreach ($_POST['items'] as $cartId => $qty) {
                $cartId = (int)$cartId;
                $qty = max(1, (int)$qty);
                $updateSql = "UPDATE cart SET quantity=? WHERE id=? AND user_id=?";
                if ($stmt = mysqli_prepare($conn, $updateSql)) {
                    mysqli_stmt_bind_param($stmt, "iii", $qty, $cartId, $userId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }
        }

        header("Location: cart.php");
        exit();
    }
}

// Fetch cart items
$cartItems = [];
$totalItems = 0;
$totalPrice = 0;
$sql = "SELECT c.id, c.quantity, m.name, m.price, m.image
        FROM cart c
        INNER JOIN menu m ON c.menu_id = m.id
        WHERE c.user_id=?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $cartItems[] = $row;
        $totalItems += $row['quantity'];
        $totalPrice += $row['quantity'] * $row['price'];
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Your Cart | 56Food</title>
    <link rel="stylesheet" href="assets/css/cart.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .cart-section {
            max-width: 900px;
            margin: auto;
            padding: 50px 20px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 15px;
        }

        .cart-item img {
            width: 100px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-details {
            flex: 1;
        }

        .cart-details h4 {
            margin: 0;
            color: #ff6347;
        }

        .cart-details p {
            margin: 5px 0;
        }

        .cart-details input {
            width: 60px;
            padding: 5px;
        }

        .remove-btn,
        .checkout-btn {
            background: #ff6347;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .remove-btn {
            background: red;
            padding: 5px 10px;
        }

        .cart-summary {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <header>
        <h1>56Food</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">My Orders</a>
            <a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
        </nav>
    </header>

    <section class="cart-section">
        <h2>Your Cart</h2>

        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="menu.php">Browse the menu</a> to add some food.</p>
        <?php else: ?>
            <form method="POST" action="cart.php">
                <input type="hidden" name="action" value="update">

                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <?php $img = !empty($item['image']) ? 'assets/images/foods/' . htmlspecialchars($item['image']) : 'https://images.unsplash.com/photo-1600891964599-f61ba0e24092?fit=crop&w=400&q=80'; ?>
                        <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="cart-details">
                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                            <p>Tsh <?= number_format($item['price'], 2) ?></p>
                            <label>Qty: <input type="number" name="items[<?= (int)$item['id'] ?>]" value="<?= (int)$item['quantity'] ?>" min="1"></label>
                        </div>
                        <button type="submit" name="remove_id" value="<?= (int)$item['id'] ?>" class="remove-btn">Remove</button>
                    </div>
                <?php endforeach; ?>

                <div class="cart-summary">
                    <p>Total Items: <?= $totalItems ?></p>
                    <p>Total Price: <strong>Tsh <?= number_format($totalPrice, 2) ?></strong></p>
                    <button type="submit" class="checkout-btn">Update Cart</button>
                    <a href="order.php" class="checkout-btn" style="text-decoration:none;">Proceed to Checkout</a>
                </div>

            </form>
        <?php endif; ?>
    </section>
</body>

</html>