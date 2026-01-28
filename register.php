<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | 56Food</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <!-- Home Button -->
    <a href="index.php" class="home-btn">Home</a>

    <div class="login-container">
        <div class="form-box">
            <h2>Create Your Account</h2>
            <form id="registerForm" action="register_action.php" method="POST">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Register</button>
                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>
    <script src="assets/js/login.js"></script>

</body>

</html>