<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

$errors = [];

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: users.php");
    exit();
}

// Fetch existing user
$sql = "SELECT id, name, email, phone, role FROM users WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

if (empty($user)) {
    header("Location: users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['full_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? $user['role'];

    if ($name === '') {
        $errors[] = "Full name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (!in_array($role, ['admin', 'customer'], true)) {
        $role = 'customer';
    }

    // Check email unique (excluding current user)
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $email, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $errors[] = "Email already in use by another user.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (empty($errors)) {
        // Build update
        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ?, role = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $phone, $hash, $role, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $role, $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
        }

        header("Location: users.php");
        exit();
    }
}
?>

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
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Edit User</h3>
            </header>

            <section class="tab-panel active">

                <?php if (!empty($errors)): ?>
                    <div style="color:#721c24; background:#f8d7da; padding:10px; margin-bottom:15px; border-radius:6px;">
                        <strong>Error:</strong><br>
                        • <?= implode('<br>• ', array_map('htmlspecialchars', $errors)) ?>
                    </div>
                <?php endif; ?>

                <form action="edit_user.php?id=<?= (int) $user['id'] ?>" method="POST" class="form-box">

                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter new password if you want to change">
                    </div>

                    <div class="form-group">
                        <label>User Role</label>
                        <select name="role" required>
                            <option value="admin"<?= $user['role'] === 'admin' ? ' selected' : '' ?>>Admin</option>
                            <option value="customer"<?= $user['role'] === 'customer' ? ' selected' : '' ?>>Customer</option>
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