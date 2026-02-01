<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Order | 56Food</title>
    <style>
        /* ===== VIEW ORDER SIMPLE ===== */

        .detail-card {
            background: #fff;
            padding: 20px;
            margin-bottom: 18px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
        }

        .detail-card h4 {
            margin-bottom: 12px;
            color: #ff6b00;
        }

        .detail-card p {
            margin: 8px 0;
            font-size: 15px;
            color: #444;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            color: #27ae60;
        }
    </style>
    <link rel="stylesheet" href="../assets/css/admin/dashboard.css">
</head>

<body>

    <div class="admin-wrapper">

        <!-- SIDEBAR (Admin) -->
        <aside class="sidebar">
            <h2>56Food</h2>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active"><a href="orders.php">Orders</a></li>
                <li><a href="menus.php">Menus</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <header class="admin-header">
                <h3>Order Details</h3>
            </header>

            <section class="tab-panel active">

                <!-- DELIVERY DETAILS -->
                <div class="detail-card">
                    <h4>Delivery Details</h4>

                    <p><strong>Full Name:</strong> John Doe</p>
                    <p><strong>Phone Number:</strong> +255 712 345 678</p>
                    <p><strong>Delivery Address:</strong> Mbezi Beach, Dar es Salaam</p>
                    <p><strong>Payment Method:</strong> Cash on Delivery</p>
                </div>

                <!-- ORDER SUMMARY -->
                <div class="detail-card">
                    <h4>Order Summary</h4>

                    <p><strong>Food Name:</strong> Cheese Burger</p>
                    <p><strong>Quantity:</strong> 2</p>
                    <p><strong>Total Price:</strong> <span class="price">$11.98</span></p>
                </div>

                <!-- ACTION -->
                <div class="form-actions">
                    <a href="orders.php" class="btn delete">Back</a>
                </div>

            </section>

        </main>
    </div>

</body>

</html>