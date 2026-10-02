<?php
/**
 * API - حفظ نتيجة
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
$title = sanitize($data['title'] ?? '');
$content = $data['content'] ?? '';

if (empty($toolId) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'بيانات ناقصة']);
    exit;
}

try {
    $outputId = saveOutput($_SESSION['user_id'], $toolId, $title, $content);
    incrementToolUsage($toolId);
    logAction($_SESSION['user_id'], 'save_output', "حفظ نتيجة - أداة #{$toolId}");
    echo json_encode(['success' => true, 'id' => $outputId, 'message' => 'تم الحفظ بنجاح']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطأ في الحفظ']);
}
