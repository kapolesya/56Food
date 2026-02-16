<?php
// api/login.php
header('Content-Type: application/json; charset=utf-8');

// Include new PDO config
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/validator.php';

// Secure session cookie settings
if (PHP_SAPI !== 'cli') {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
    session_set_cookie_params([
        'lifetime' => 0, // Session cookie
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
session_start();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    
    if (!is_array($data)) {
        throw new Exception('Invalid JSON request', 400);
    }
    
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $redirectTo = $data['redirect'] ?? '';

    // Validation
    if (!validate_email($email)) {
        throw new Exception('A valid email is required', 400);
    }
    if (empty($password)) {
        throw new Exception('Password is required', 400);
    }

    // Rate limiting
    $attempts = $_SESSION['login_attempts']['count'] ?? 0;
    $lastAttempt = $_SESSION['login_attempts']['last'] ?? 0;
    $now = time();
    
    if ($now - $lastAttempt > 600) { // Reset after 10 mins
        $attempts = 0;
    }
    
    if ($attempts >= 5) {
        throw new Exception('Too many login attempts. Try again later.', 429);
    }

    // Fetch user
    $stmt = $pdo->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Success
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        // Reset attempts
        $_SESSION['login_attempts'] = ['count' => 0, 'last' => $now];

        // Redirect logic
        $safeRedirect = sanitize_redirect($redirectTo);
        if ($user['role'] === 'admin') {
            $finalRedirect = 'admin/dashboard.php';
        } elseif ($safeRedirect) {
            $finalRedirect = $safeRedirect;
        } else {
            $finalRedirect = 'index.php';
        }

        echo json_encode([
            'success' => true,
            'redirect' => $finalRedirect,
            'role' => $user['role'],
            'message' => 'Login successful'
        ]);

    } else {
        // Failure
        $attempts++;
        $_SESSION['login_attempts'] = ['count' => $attempts, 'last' => $now];
        throw new Exception('Invalid credentials', 401);
    }

} catch (Exception $e) {
    $code = $e->getCode() ?: 500;
    if ($code < 100 || $code > 599) $code = 500;
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    // error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Login failed due to server error']);
}
?>
