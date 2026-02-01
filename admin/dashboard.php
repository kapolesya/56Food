<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | 56Food Admin</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Admin Dashboard</h3>
            </header>

            <!-- DASHBOARD CARDS -->
            <section class="cards-container">
                <div class="card">
                    <h3>Orders</h3>
                    <p id="ordersCount">2</p>
                </div>
                <div class="card">
                    <h3>Menu Items</h3>
                    <p id="menuCount">2</p>
                </div>
                <div class="card">
                    <h3>Users</h3>
                    <p id="userCount">2</p>
                </div>
            </section>

        </main>

    </div>

</body>

</html>