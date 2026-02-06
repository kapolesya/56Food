<?php
require_once "include/conn.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name             = trim($_POST["name"] ?? '');
    $email            = trim($_POST["email"] ?? '');
    $password         = $_POST["password"] ?? '';           // do NOT trim password
    $confirm_password = $_POST["confirm_password"] ?? '';

    // ── Basic validation ───────────────────────────────────────
    if (empty($name)) {
        $errors[] = "Full name is required.";
    } elseif (strlen($name) < 2 || strlen($name) > 80) {
        $errors[] = "Name should be 2–80 characters.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        // Show errors nicely (you can style this better later)
        echo '<div style="color:#721c24; background:#f8d7da; padding:15px; margin:20px auto; max-width:500px; border:1px solid #f5c6cb; border-radius:6px;">';
        echo '<strong>Please fix the following:</strong><br>';
        echo '• ' . implode('<br>• ', $errors);
        echo '</div>';
    } else {
        // ── Check if email exists ────────────────────────────────
        $stmt = mysqli_prepare($conn, "SELECT 1 FROM users WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "This email is already registered.";
        }
        mysqli_stmt_close($stmt);

        if (empty($errors)) {
            // ── Register user ───────────────────────────────────────
            // NOTE: role column has default 'customer', so we don't need to set it explicitly.
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php?success=1");
                exit();
            } else {
                $errors[] = "Registration failed. Please try again later.";
                // In development you can show: mysqli_stmt_error($stmt)
            }

            mysqli_stmt_close($stmt);
        }

        // Show DB/validation errors if any
        if (!empty($errors)) {
            echo '<div style="color:#721c24; background:#f8d7da; padding:15px; margin:20px auto; max-width:500px; border:1px solid #f5c6cb; border-radius:6px;">';
            echo '<strong>Error:</strong><br>';
            echo '• ' . implode('<br>• ', $errors);
            echo '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | 56Food</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <a href="index.php" class="home-btn">Home</a>

    <div class="login-container">
        <div class="form-box">
            <h2>Create Your Account</h2>

            <form method="POST" novalidate>
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email address" required>
                <input type="password" name="password" placeholder="Password" required minlength="8">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>

                <button type="submit">Register</button>

                <p>Already have an account? <a href="login.php">Login</a></p>
            </form>
        </div>
    </div>

    <script src="assets/js/login.js"></script>

</body>

</html>