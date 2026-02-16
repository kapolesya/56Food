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

$action = $data['action'] ?? '';
$menuId = (int)($data['menu_id'] ?? 0);

if ($menuId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid menu id']);
    exit();
}

if ($action === 'delete') {
    $stmt = mysqli_prepare($conn, 'DELETE FROM menu WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'i', $menuId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'toggle_status') {
    $stmt = mysqli_prepare($conn, "UPDATE menu SET status = IF(status = 'available','unavailable','available') WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $menuId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
    exit();
}

http_response_code(400);
echo json_encode(['error' => 'Unknown action']);
exit();

?>
