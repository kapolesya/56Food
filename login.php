<?php
session_start();

require_once __DIR__ . "/include/conn.php";
require_once __DIR__ . "/include/activity_log.php";

// Optional redirect (user alikuwa anatoka page gani)
$redirectTo = $_GET['redirect'] ?? 'index.php';

$errors = [];
$email  = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email       = trim($_POST["email"] ?? '');
    $password    = $_POST["password"] ?? '';
    $redirectTo  = $_POST["redirect"] ?? $redirectTo;

    // =========================
    // Validation
    // =========================
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // =========================
    // Login Logic
    // =========================
    if (empty($errors)) {

        $sql = "SELECT id, name, email, password, role 
                FROM users 
                WHERE email = ? 
                LIMIT 1";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            if (password_verify($password, $user['password'])) {

                // =========================
                // Login success
                // =========================
                $_SESSION['user_id']    = $user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role']  = $user['role'];

                // Log activity
                log_activity(
                    $conn,
                    $user['id'],
                    'LOGIN',
                    'User logged into the system'
                );

                // Admin → dashboard
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                    exit();
                }

                // Normal user
                header("Location: $redirectTo");
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "No account found with that email.";
        }

        mysqli_stmt_close($stmt);
    }
}
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

            <?php if (!empty($errors)): ?>
                <div style="
                color:#721c24;
                background:#f8d7da;
                padding:12px;
                margin-bottom:20px;
                border:1px solid #f5c6cb;
                border-radius:6px;
                font-size:0.95rem;">
                    <strong>Login failed:</strong><br>
                    • <?= implode('<br>• ', $errors) ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTo) ?>">

                <input
                    type="email"
                    name="email"
                    placeholder="Email address"
                    value="<?= htmlspecialchars($email) ?>"
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

</body>

</html>