<?php
/**
 * API - البحث في الأدوات
 */
require_once __DIR__ . '/../config/app.php';
header('Content-Type: application/json; charset=utf-8');

$query = sanitize($_GET['q'] ?? '');

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $results = searchTools($query);
    echo json_encode($results);
} catch (Exception $e) {
    echo json_encode([]);
}
