<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | 56Food</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fc;
            color: #333;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 220px;
            background: #1f2937;
            color: white;
            display: flex;
            flex-direction: column;
        }

        .sidebar h2 {
            text-align: center;
            padding: 20px 0;
            color: #f59e0b;
            font-size: 24px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: #f59e0b;
            color: white;
        }

        /* MAIN */
        .main-content {
            flex: 1;
            padding: 20px 30px;
        }

        .admin-header {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* CARDS */
        .cards-container {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            flex: 1;
            min-width: 200px;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
        }

        .card h3 {
            margin: 0;
            font-size: 18px;
            color: #1f2937;
        }

        .card p {
            margin: 5px 0 0;
            font-size: 28px;
            font-weight: bold;
            color: #f59e0b;
        }

        /* TABLE inside card */
        .card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .card table th,
        .card table td {
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card button {
            padding: 5px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 5px;
        }

        .card .btn.confirm {
            background: #10b981;
            color: white;
        }

        .card .btn.delete {
            background: #ef4444;
            color: white;
        }

        .card .btn.edit {
            background: #3b82f6;
            color: white;
        }

        .card .btn.add {
            background: #f59e0b;
            color: white;
            margin-top: 10px;
        }
    </style>
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="menus.php">Manage Menu</a></li>
                <li><a href="users.php">Manage Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN -->
        <main class="main-content">
            <header class="admin-header">Admin Dashboard</header>
            <div class="cards-container">

                <!-- ORDERS CARD -->
                <div class="card" id="ordersCard">
                    <h3>Orders</h3>
                    <p id="ordersCount">0</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTable">
                            <tr>
                                <td>John Doe</td>
                                <td>$15.98</td>
                                <td>Pending</td>
                            </tr>
                            <tr>
                                <td>Mary Ann</td>
                                <td>$9.50</td>
                                <td>Delivered</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- MENU CARD -->
                <div class="card" id="menuCard">
                    <h3>Menu Items</h3>
                    <p id="menuCount">0</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Food</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="menuTable">
                            <tr>
                                <td>Cheese Burger</td>
                                <td>$5.99</td>
                                <td>Available</td>
                            </tr>
                            <tr>
                                <td>Pepperoni Pizza</td>
                                <td>$9.99</td>
                                <td>Unavailable</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn add" id="addFoodBtn">Add Food</button>
                </div>

                <!-- USERS CARD -->
                <div class="card" id="usersCard">
                    <h3>Users</h3>
                    <p id="userCount">0</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody id="userTable">
                            <tr>
                                <td>Admin</td>
                                <td>Admin</td>
                            </tr>
                            <tr>
                                <td>John Doe</td>
                                <td>Customer</td>
                            </tr>
                        </tbody>
                    </table>
                    <button class="btn add" id="addUserBtn">Add User</button>
                </div>

            </div>
        </main>
    </div>

    <script>
        // ====== UPDATE COUNTS ======
        function updateCounts() {
            // Orders count
            const ordersTable = document.getElementById("ordersTable");
            document.getElementById("ordersCount").innerText = ordersTable.rows.length;

            // Menu count (numeric price only)
            const menuTable = document.getElementById("menuTable");
            // remove rows with non-numeric price
            Array.from(menuTable.rows).forEach(row => {
                const price = row.cells[1].innerText.replace("$", "").trim();
                if (isNaN(price)) row.remove();
            });
            document.getElementById("menuCount").innerText = menuTable.rows.length;

            // Users count
            const userTable = document.getElementById("userTable");
            document.getElementById("userCount").innerText = userTable.rows.length;
        }
        updateCounts();

        // ====== ADD FOOD ======
        document.getElementById("addFoodBtn").addEventListener("click", () => {
            const name = prompt("Food name:");
            const price = prompt("Price:");
            const status = prompt("Status (Available/Unavailable):");

            if (name && !isNaN(price)) {
                const tbody = document.getElementById("menuTable");
                const row = tbody.insertRow();
                row.insertCell(0).innerText = name;
                row.insertCell(1).innerText = "$" + price;
                row.insertCell(2).innerText = status || "Available";
                updateCounts();
            } else {
                alert("Price must be a number!");
            }
        });

        // ====== ADD USER ======
        document.getElementById("addUserBtn").addEventListener("click", () => {
            const name = prompt("User name:");
            const role = prompt("Role:");
            if (name && role) {
                const tbody = document.getElementById("userTable");
                const row = tbody.insertRow();
                row.insertCell(0).innerText = name;
                row.insertCell(1).innerText = role;
                updateCounts();
            }
        });

        // ====== DELETE ROW ======
        document.addEventListener("click", function(e) {
            if (e.target.classList.contains("delete")) {
                if (confirm("Are you sure?")) {
                    e.target.closest("tr").remove();
                    updateCounts();
                }
            }
        });
    </script>

</body>

</html>