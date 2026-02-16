<?php
// api/register.php
header('Content-Type: application/json; charset=utf-8');

// Include new PDO config
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../helpers/validator.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Get JSON input
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        throw new Exception('Invalid JSON request', 400);
    }

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $confirm = $data['confirm_password'] ?? '';

    $errors = [];

    // Validation
    if (empty($name) || mb_strlen($name) < 2 || mb_strlen($name) > 80) {
        $errors[] = 'Name must be 2-80 characters.';
    }

    if (!validate_email($email)) {
        $errors[] = 'A valid email is required.';
    }

    if (!validate_password($password)) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['errors' => $errors]);
        exit();
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        http_response_code(409); // Conflict
        echo json_encode(['errors' => ['Email is already registered.']]);
        exit();
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $insert->execute([$name, $email, $hashed]);

    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'Registration successful']);

} catch (Exception $e) {
    // Handle expected errors
    $code = $e->getCode() ?: 500;
    // Ensure code is a valid HTTP status
    if ($code < 100 || $code > 599) $code = 500;
    
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    // Handle database errors (don't expose details in production)
    // error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Registration failed due to a server error.']);
}
?>
