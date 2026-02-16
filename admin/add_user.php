<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// NOTE: form submission is handled via AJAX at /api/admin/users_create.php
// This page only renders the form and shows client-side messages.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add User | 56Food Admin</title>
    <style>
        /* ===== ADD USER FORM ===== */

        .form-box {
            max-width: 600px;
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 16px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
        }

        .form-actions .btn {
            padding: 10px 20px;
        }

        .form-actions .btn.add {
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-actions .btn.cancel {
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 18px;
            cursor: pointer;
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
                <li><a href="menus.php">Menus</a></li>
                <li class="active"><a href="users.php">Users</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Add New User</h3>
            </header>

            <section class="tab-panel active">

                <?php if (!empty($errors)): ?>
                    <div style="color:#721c24; background:#f8d7da; padding:10px; margin-bottom:15px; border-radius:6px;">
                        <strong>Error:</strong><br>
                        • <?= implode('<br>• ', array_map('htmlspecialchars', $errors)) ?>
                    </div>
                <?php endif; ?>

                <div id="add-user-messages"></div>
                <form id="add-user-form" class="form-box">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" placeholder="Enter full name" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter email" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="+255..." required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter password" required>
                    </div>

                    <div class="form-group">
                        <label>User Role</label>
                        <select name="role" required>
                            <option value="">-- Select Role --</option>
                            <option value="admin">Admin</option>
                            <option value="customer">Customer</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn add">Save User</button>
                        <a href="users.php" class="btn cancel">Cancel</a>
                    </div>

                </form>

                <script src="../assets/js/admin.js"></script>

            </section>

        </main>
    </div>

</body>

</html>