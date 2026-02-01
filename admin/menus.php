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

                <button class="btn add" id="addFoodBtn">Add Food</button>

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
                        <tr>
                            <td>1</td>
                            <td>Cheese Burger</td>
                            <td>$5.99</td>
                            <td>Available</td>
                            <td>
                                <button class="btn add"><a href="add_menus.php" style="color: white; text-decoration: none;">Add</a></button>
                                <button class="btn edit"><a href="edit_menus.php" style="color: white; text-decoration: none;">Edit</a></button>
                                <button class="btn delete">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </section>

        </main>
    </div>


</body>

</html>