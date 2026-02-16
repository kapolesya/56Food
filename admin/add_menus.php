<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// NOTE: menu creation handled via AJAX at /api/admin/menus_create.php
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Menu | 56Food Admin</title>
    <style>
        .form-box {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
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
            margin-right: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .form-actions .btn.add {
            background-color: #28a745;
            color: white;
        }

        .form-actions .btn.delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
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

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Add New Menu</h3>
            </header>

            <!-- ADD MENU FORM -->
            <section class="tab-panel active">

                <div id="add-menu-messages"></div>

                <form id="add-menu-form" enctype="multipart/form-data" class="form-box">

                    <div class="form-group">
                        <label>Food Name</label>
                        <input type="text" name="food_name" placeholder="Enter food name" required>
                    </div>

                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" placeholder="Enter price" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"
                            rows="4"
                            placeholder="Enter food description..."
                            required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Food Image</label>
                        <input type="file" name="food_image" accept="image/*" required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn add">Save Menu</button>
                        <a href="menus.php" class="btn delete" style="text-decoration: none;">Cancel</a>
                    </div>

                </form>

                <script src="../assets/js/admin.js"></script>

            </section>

        </main>
    </div>

</body>

</html>