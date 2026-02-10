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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $price       = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$name || !$price || !$description) {
        $error_msg = "All fields are required.";
    } else {
        // Handle image upload if new image provided
        $image_to_save = $image;
        if (!empty($_FILES['food_image']['name'])) {
            $upload_dir = __DIR__ . "/../uploads/";
            $tmp_name   = $_FILES['food_image']['tmp_name'];
            $filename   = time() . '_' . basename($_FILES['food_image']['name']);
            $target     = $upload_dir . $filename;

            if (move_uploaded_file($tmp_name, $target)) {
                // Delete old image
                if ($image && file_exists($upload_dir . $image)) {
                    unlink($upload_dir . $image);
                }
                $image_to_save = $filename;
            } else {
                $error_msg = "Failed to upload new image.";
            }
        }

        if (empty($error_msg)) {
            // Update menu in DB
            $stmt = $conn->prepare("UPDATE menu SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sdssi", $name, $price, $description, $image_to_save, $menu_id);
            if ($stmt->execute()) {
                $success_msg = "Menu updated successfully!";
                $image = $image_to_save; // update current image variable
            } else {
                $error_msg = "Failed to update menu.";
            }
            $stmt->close();
        }
    }
}
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
                <form action="" method="POST" enctype="multipart/form-data" class="form-box">
                    <?php if (!empty($success_msg)) echo "<div class='success'>$success_msg</div>"; ?>
                    <?php if (!empty($error_msg)) echo "<div class='error'>$error_msg</div>"; ?>

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
                            <img src="../uploads/<?= htmlspecialchars($image) ?>" class="menu-image" alt="Menu Image">
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
            </section>
        </main>
    </div>
</body>

</html>