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

// تضمين ملف الأداة
include $toolFile;
