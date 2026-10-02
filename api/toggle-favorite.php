<?php
/**
 * API - تبديل المفضلة
 */
require_once __DIR__ . '/../config/app.php';
header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'يجب تسجيل الدخول']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'طريقة غير مسموحة']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$toolId = (int)($data['tool_id'] ?? 0);

if (empty($toolId)) {
    echo json_encode(['success' => false, 'message' => 'معرّف الأداة مطلوب']);
    exit;
}

try {
    $isFav = toggleFavorite($_SESSION['user_id'], $toolId);
    echo json_encode(['success' => true, 'is_favorite' => $isFav]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ']);
}
