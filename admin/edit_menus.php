<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";


require_admin();

// Check menu ID
if (!isset($_GET['id'])) {
    die("Menu ID missing.");
}

$menu_id = (int)$_GET['id'];

// Fetch menu from DB
$stmt = $conn->prepare("SELECT * FROM menu WHERE id = ?");
$stmt->bind_param("i", $menu_id);
$stmt->execute();
$result = $stmt->get_result();
$menu = $result->fetch_assoc();
$stmt->close();

if (!$menu) {
    die("Menu not found.");
}

// Initialize variables
$name        = $menu['name'] ?? '';
$price       = $menu['price'] ?? '';
$description = $menu['description'] ?? '';
$image       = $menu['image'] ?? '';
$success_msg = '';
$error_msg   = '';

// Submission handled via AJAX at /api/admin/menus_update.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Menu | 56Food Admin</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <style>
        .form-box {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #f9f9f9;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-actions {
            margin-top: 20px;
        }

        .form-actions .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
        }

        .btn.add {
            background: #28a745;
        }

        .btn.delete {
            background: #dc3545;
        }

        img.menu-image {
            max-width: 200px;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li class="active"><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="admin-header">
                <h3>Edit Menu</h3>
            </header>

            <section class="tab-panel active">
                <div id="edit-menu-messages"></div>

                <form id="edit-menu-form" data-menu-id="<?= (int)$menu['id'] ?>" enctype="multipart/form-data" class="form-box">

                    <div class="form-group">
                        <label>Food Name</label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($name) ?>">
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" required value="<?= htmlspecialchars($price) ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" required><?= htmlspecialchars($description) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Food Image</label>
                        <?php if (!empty($image)): ?>
                            <br>
                            <img src="../assets/images/foods/<?= htmlspecialchars($image) ?>" class="menu-image" alt="Menu Image">
                            <br>
                        <?php endif; ?>
                        <input type="file" name="food_image" accept="image/*">
                        <small>Leave blank to keep existing image.</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn add">Update Menu</button>
                        <a href="menus.php" class="btn delete">Cancel</a>
                    </div>
                </form>

                <script src="../assets/js/admin.js"></script>
            </section>
        </main>
    </div>
</body>

</html>