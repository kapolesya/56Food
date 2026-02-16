<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../include/conn.php';
require_once __DIR__ . '/../../include/auth.php';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

$id = (int)($data['id'] ?? 0);
$name = trim($data['full_name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? 'customer';

$errors = [];
if ($id <= 0) $errors[] = 'Invalid user id.';
if ($name === '') $errors[] = 'Full name required.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
if (!in_array($role, ['admin','customer'], true)) $role = 'customer';

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit();
}

// Check email uniqueness excluding current user
$stmt = mysqli_prepare($conn, 'SELECT id FROM users WHERE email = ? AND id <> ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'si', $email, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    http_response_code(400);
    echo json_encode(['errors' => ['Email already in use']]);
    exit();
}
mysqli_stmt_close($stmt);

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, 'UPDATE users SET name = ?, email = ?, phone = ?, password = ?, role = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sssssi', $name, $email, $phone, $hash, $role, $id);
} else {
    $stmt = mysqli_prepare($conn, 'UPDATE users SET name = ?, email = ?, phone = ?, role = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'ssssi', $name, $email, $phone, $role, $id);
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'message' => 'User updated']);
    exit();
}
mysqli_stmt_close($stmt);
http_response_code(500);
echo json_encode(['error' => 'Failed to update user']);
exit();

?>
