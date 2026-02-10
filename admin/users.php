<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// Detect if 'status' column exists
$hasStatusColumn = false;
$colCheckSql = "
    SELECT 1 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'users' 
      AND COLUMN_NAME = 'status'
    LIMIT 1
";
if ($res = mysqli_query($conn, $colCheckSql)) {
    if (mysqli_fetch_assoc($res)) {
        $hasStatusColumn = true;
    }
    mysqli_free_result($res);
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        header("Location: users.php");
        exit();
    }

    // Prevent admin deleting self
    if ($action === 'delete_user' && $userId === (int)$_SESSION['user_id']) {
        die("You cannot delete your own account.");
    }

    if ($action === 'delete_user') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }

    if ($action === 'change_role') {
        $newRole = $_POST['role'] ?? 'customer';
        if (!in_array($newRole, ['admin', 'customer'], true)) {
            $newRole = 'customer';
        }
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $newRole, $userId);
        $stmt->execute();
        $stmt->close();
    }

    if ($action === 'toggle_status' && $hasStatusColumn) {
        $stmt = $conn->prepare("
            UPDATE users 
            SET status = IF(status = 'active', 'inactive', 'active') 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $userId);
        @mysqli_stmt_execute($stmt);
        $stmt->close();
    }

    header("Location: users.php");
    exit();
}

// Fetch all users
$users = [];
$sql = $hasStatusColumn
    ? "SELECT id, name, email, role, status FROM users"
    : "SELECT id, name, email, role FROM users";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (!$hasStatusColumn) {
            $row['status'] = 'active';
        }
        $users[] = $row;
    }
    mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users | 56Food Admin</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        .btn {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            text-decoration: none;
            font-size: 13px;
        }

        .btn-edit {
            background: #4CAF50;
        }

        .btn-delete {
            background: #f44336;
            border: none;
            cursor: pointer;
        }

        .btn-toggle {
            background: #2196F3;
            border: none;
            cursor: pointer;
        }

        .btn-select {
            padding: 3px;
            font-size: 13px;
        }

        .add-user {
            margin-bottom: 10px;
            background: #4CAF50;
            color: #fff;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
        }

        form {
            display: inline-block;
            margin: 0;
        }
    </style>
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
                <li><a href="users.php" class="active">Users</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <header class="admin-header">
                <h3>Manage Users</h3>
            </header>

            <section class="tab-panel active">
                <button class="add-user">
                    <a href="add_user.php" style="text-decoration:none; color:white;">Add User</a>
                </button>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="6">No users found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= (int)$user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($user['status'] ?? 'active')) ?></td>
                                    <td>
                                        <!-- Edit -->
                                        <a href="edit_user.php?id=<?= (int)$user['id'] ?>" class="btn btn-edit">Edit</a>

                                        <!-- Delete (POST method) -->
                                        <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
                                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                <button type="submit" class="btn btn-delete">Delete</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Change role -->
                                        <form method="POST" action="users.php">
                                            <input type="hidden" name="action" value="change_role">
                                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                            <select name="role" class="btn-select" onchange="this.form.submit()">
                                                <option value="customer" <?= $user['role'] === 'customer' ? 'selected' : '' ?>>Customer</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </form>

                                        <!-- Activate / Deactivate -->
                                        <form method="POST" action="users.php">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                            <button type="submit" class="btn-toggle">
                                                <?= ($user['status'] ?? 'active') === 'active' ? 'Deactivate' : 'Activate' ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>