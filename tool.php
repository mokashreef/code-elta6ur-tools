<?php
/**
 * موجه الأدوات (Tool Router)
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/config/app.php';

$slug = sanitize($_GET['slug'] ?? '');

if (empty($slug)) {
    redirect('index.php');
}

// جلب الأداة
$tool = getToolBySlug($slug);

if (!$tool) {
    setFlash('الأداة غير موجودة', 'error');
    redirect('index.php');
}

// التحقق من وجود ملف الأداة
$toolFile = __DIR__ . '/tools/' . $slug . '.php';

if (!file_exists($toolFile)) {
    setFlash('ملف الأداة غير موجود: ' . $slug, 'error');
    redirect('index.php');
}

// متغيرات مشتركة لجميع الأدوات
$currentTool = $slug;
$pageTitle = $tool['name'];
$toolId = $tool['id'];
$seoDescription = $tool['seoDescription'] ?? $tool['description'];
$seoKeywords = !empty($tool['keywords']) ? implode(', ', $tool['keywords']) : '';
$canonicalUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . "tool.php?slug=" . urlencode($slug);

// زيادة عداد الاستخدام إن أمكن
if (function_exists('incrementToolUsage')) {
    try { incrementToolUsage($toolId); } catch(Throwable $e) {}
}

// تضمين ملف الأداة
include $toolFile;
