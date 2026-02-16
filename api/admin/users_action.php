<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../include/conn.php';
require_once __DIR__ . '/../../include/auth.php';

// Admin only
if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Expect JSON
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit();
}

$action = $data['action'] ?? '';
$userId = (int)($data['user_id'] ?? 0);

if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid user id']);
    exit();
}

// Prevent deleting self
if ($action === 'delete_user' && $userId === (int)$_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['error' => 'Cannot delete own account']);
    exit();
}

if ($action === 'delete_user') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM users WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'change_role') {
    $role = $data['role'] ?? 'customer';
    if (!in_array($role, ['admin','customer'], true)) $role = 'customer';
    $stmt = mysqli_prepare($conn, 'UPDATE users SET role = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'si', $role, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true, 'role' => $role]);
    exit();
}

if ($action === 'toggle_status') {
    // toggle status if column exists
    $stmt = mysqli_prepare($conn, "UPDATE users SET status = IF(status = 'active','inactive','active') WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    @mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    exit();
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
exit();

?>
