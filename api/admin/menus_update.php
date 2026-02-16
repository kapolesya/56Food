<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../include/conn.php';
require_once __DIR__ . '/../../include/auth.php';

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

$menuId = (int)($_POST['menu_id'] ?? 0);
// Accept either 'food_name' (new forms) or 'name' (edit form) for compatibility
$name = '';
if (isset($_POST['food_name'])) {
    $name = trim($_POST['food_name']);
} elseif (isset($_POST['name'])) {
    $name = trim($_POST['name']);
}
$price = (float)($_POST['price'] ?? 0);
$description = trim($_POST['description'] ?? '');

$errors = [];
if ($menuId <= 0) $errors[] = 'Invalid menu id.';
if ($name === '') $errors[] = 'Food name required.';
if ($price <= 0) $errors[] = 'Price must be > 0.';
if ($description === '') $errors[] = 'Description required.';

$imageName = null;
if (!empty($_FILES['food_image']['name'])) {
    $uploadDir = __DIR__ . '/../../assets/images/foods/';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);
    $original = basename($_FILES['food_image']['name']);
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    $imageName = uniqid('food_', true) . '.' . strtolower($ext);
    $target = $uploadDir . $imageName;
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array(strtolower($ext), $allowed, true)) {
        $errors[] = 'Invalid image type.';
    } else {
        if (!move_uploaded_file($_FILES['food_image']['tmp_name'], $target)) {
            $errors[] = 'Failed to upload image.';
        }
    }
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
    exit();
}

// Build update query
if ($imageName !== null) {
    $stmt = mysqli_prepare($conn, 'UPDATE menu SET name = ?, price = ?, description = ?, image = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sdssi', $name, $price, $description, $imageName, $menuId);
} else {
    $stmt = mysqli_prepare($conn, 'UPDATE menu SET name = ?, price = ?, description = ? WHERE id = ?');
    mysqli_stmt_bind_param($stmt, 'sdsi', $name, $price, $description, $menuId);
}

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Menu updated']);
    exit();
}

mysqli_stmt_close($stmt);
http_response_code(500);
echo json_encode(['error' => 'Failed to update menu']);
exit();

?>
