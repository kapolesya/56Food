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
            <form id="loginForm" action="login_action.php" method="POST">
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
                <p>Don’t have an account? <a href="register.php">Register</a></p>
            </form>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
</body>

</html>