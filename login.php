<?php
// login.php: Render login page; actual authentication happens at /api/login.php via JSON.
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | 56Food</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <!-- Home Button -->
    <a href="index.php" class="home-btn">Home</a>

    <div class="login-container">
        <div class="form-box">
            <h2>Login to 56Food</h2>

            <div id="login-messages"></div>
            <form id="login-form" novalidate>
                <input type="hidden" name="redirect" value="index.php">

                <input
                    type="email"
                    name="email"
                    placeholder="Email address"
                    required
                    autofocus>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required>

                <button type="submit">Login</button>

                <p>Don’t have an account? <a href="register.php">Register</a></p>
            </form>
        </div>
    </div>

        <script src="assets/js/auth.js"></script>

    </body>

    </html>