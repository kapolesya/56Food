<?php
require_once __DIR__ . "/../include/conn.php";
require_once __DIR__ . "/../include/auth.php";

require_admin();

// Handle delete and toggle availability
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $menuId = (int) ($_POST['menu_id'] ?? 0);

    if ($menuId > 0 && $action === 'delete') {
        $sql = "DELETE FROM menu WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $menuId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    if ($menuId > 0 && $action === 'toggle_status') {
        $sql = "UPDATE menu 
                SET status = IF(status = 'available', 'unavailable', 'available') 
                WHERE id = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $menuId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header("Location: menus.php");
    exit();
}

// Fetch all menu items
$menus = [];
$sql = "SELECT id, name, price, status FROM menu ORDER BY created_at DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menus[] = $row;
    }
    mysqli_free_result($result);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Menu | 56Food Admin</title>
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
                <li><a href="menus.php" class="active">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="activity_logs.php">Activity Logs</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Manage Menus</h3>
            </header>

            <!-- MENU CONTENT -->
            <section class="tab-panel active">

                <button class="btn add" id="addFoodBtn">
                    <a href="add_menus.php" style="color: white; text-decoration: none;">Add Food</a>
                </button>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Food Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody id="menuTable">
                        <?php if (empty($menus)): ?>
                            <tr>
                                <td colspan="5">No menu items found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($menus as $menu): ?>
                                <tr>
                                    <td><?= (int) $menu['id'] ?></td>
                                    <td><?= htmlspecialchars($menu['name']) ?></td>
                                    <td>$<?= number_format($menu['price'], 2) ?></td>
                                    <td><?= htmlspecialchars(ucfirst($menu['status'])) ?></td>
                                    <td>
                                        <a href="edit_menus.php?id=<?= (int) $menu['id'] ?>" class="btn edit" style="color:white; text-decoration:none;">Edit</a>

                                        <form class="ajax-form" data-api="/56Food/api/admin/menus_action.php" style="display:inline-block; margin-left:5px;">
                                            <input type="hidden" name="menu_id" value="<?= (int) $menu['id'] ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <button type="submit" class="btn add">
                                                <?= $menu['status'] === 'available' ? 'Disable' : 'Enable' ?>
                                            </button>
                                        </form>

                                        <form class="ajax-form" data-api="/56Food/api/admin/menus_action.php" data-confirm="Delete this menu item?" style="display:inline-block; margin-left:5px;">
                                            <input type="hidden" name="menu_id" value="<?= (int) $menu['id'] ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn delete">Delete</button>
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
<script src="../assets/js/admin.js"></script>