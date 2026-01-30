<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Orders | 56Food</title>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active" data-tab="orders.php">Orders</li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Manage Orders</h3>
            </header>

            <!-- ================= ORDERS ================= -->
            <section class="tab-panel active" id="orders">
                <h4>Orders</h4>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <tr>
                        <td>#101</td>
                        <td>John Doe</td>
                        <td>$15.98</td>
                        <td class="status">Pending</td>
                        <td>
                            <button class="btn confirm">Confirm</button>
                            <button class="btn delete">Delete</button>
                        </td>
                    </tr>
                </table>
            </section>

            <!-- ================= MENU ================= -->
            <section class="tab-panel" id="menu">
                <h4>Menu</h4>

                <button class="btn add" id="addFood">Add Food</button>

                <table>
                    <tr>
                        <th>Food</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>

                    <tbody id="menuTable">
                        <tr>
                            <td>Cheese Burger</td>
                            <td>$5.99</td>
                            <td><button class="btn delete">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- ================= USERS ================= -->
            <section class="tab-panel" id="users">
                <h4>Users</h4>

                <button class="btn add-user" id="addUser">Add User</button>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>

                    <tbody id="userTable">
                        <tr>
                            <td>1</td>
                            <td>Admin</td>
                            <td>Admin</td>
                            <td><button class="btn delete">Remove</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>

        </main>
    </div>



</body>

</html>