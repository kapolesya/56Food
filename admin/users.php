<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users | 56Food Admin</title>
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
                <li><a href="users.php" class="active">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Manage Users</h3>
            </header>

            <section class="tab-panel active">

                <button class="btn add-user" id="addUserBtn"><a href="add_user.php" style="text-decoration: none; color: white;">Add User</a></button>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="userTable">
                        <tr>
                            <td>1</td>
                            <td>Admin</td>
                            <td>admin@56food.com</td>
                            <td>Admin</td>
                            <td>
                                <button class="btn edit"><a href="edit_user.php" style="color: white; text-decoration: none;">Edit</a></button>
                                <button class="btn delete">Remove</button>
                            </td>
                        </tr>
                        <tr>

                        </tr>
                    </tbody>
                </table>

            </section>

        </main>
    </div>


</body>

</html>