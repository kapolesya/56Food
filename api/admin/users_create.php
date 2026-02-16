<?php
// API: Create admin user (JSON)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../include/conn.php';
require_once __DIR__ . '/../../include/auth.php';

// Ensure only admins can create users (session is started in include/auth.php)
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

$name = trim($data['full_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'customer';

$errors = [];
if ($name === '') $errors[] = 'Full name is required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if (!in_array($role, ['admin','customer'], true)) $role = 'customer';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit();
}

// Check uniqueness
$stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE email=? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    http_response_code(400);
    echo json_encode(['errors' => ['Email already exists.']]);
    exit();
}
mysqli_stmt_close($stmt);

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, 'INSERT INTO users (name,email,phone,password,role) VALUES (?,?,?,?,?)');
mysqli_stmt_bind_param($stmt, 'sssss', $name, $email, $phone, $hash, $role);
if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    http_response_code(201);
    echo json_encode(['success' => true, 'message' => 'User created']);
    exit();
}
mysqli_stmt_close($stmt);
http_response_code(500);
echo json_encode(['error' => 'Failed to create user.']);
exit();

?>
