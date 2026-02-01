<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit User | 56Food Admin</title>
    <style>
        /* ===== EDIT USER FORM ===== */
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

        .form-actions .btn.update {
            background-color: #007bff;
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
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Edit User</h3>
            </header>

            <section class="tab-panel active">

                <!-- Example: PHP will pre-fill values -->
                <form action="update-user.php?id=1" method="POST" class="form-box">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="John Doe" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="john@example.com" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="+255 712 345 678" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter new password if you want to change">
                    </div>

                    <div class="form-group">
                        <label>User Role</label>
                        <select name="role" required>
                            <option value="admin" selected>Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn update">Update User</button>
                        <a href="users.php" class="btn cancel">Cancel</a>
                    </div>

                </form>

            </section>

        </main>
    </div>

</body>

</html>