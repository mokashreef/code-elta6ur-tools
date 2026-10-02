<?php
/**
 * إعدادات التطبيق العامة
 * Code Elta6ur Tools - النسخة السورية
 */

// اسم التطبيق
define('APP_NAME', 'Code Elta6ur Tools');
define('APP_NAME_AR', 'كود التطور - منصة الأدوات الشاملة');
define('APP_VERSION', '2.0.0');
define('APP_DESCRIPTION', 'منصة عربية شاملة للأدوات العملية: أدوات مالية، حاسبات البناء والطاقة، نصوص، تعليم، وأدوات المطورين');

// المسار الأساسي التلقائي
if (!defined('BASE_URL')) {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = dirname($scriptName);
    $scriptDir = str_replace('\\', '/', $scriptDir);
    // إذا كان المجلد الجذر أو داخل مجلد فرعي
    if (in_array(basename($scriptDir), ['admin', 'auth', 'user', 'api'])) {
        $baseUrl = dirname($scriptDir);
    } else {
        $baseUrl = $scriptDir;
    }
    $baseUrl = rtrim(str_replace('\\', '/', $baseUrl), '/') . '/';
    if ($baseUrl === '//' || $baseUrl === '') $baseUrl = '/';
    define('BASE_URL', $baseUrl);
}

// إعدادات الجلسة
define('SESSION_LIFETIME', 86400); // 24 ساعة

// إعدادات الأمان
define('CSRF_TOKEN_NAME', '_csrf_token');

// إعدادات الأدمن الافتراضية
define('DEFAULT_ADMIN_EMAIL', 'contact@gmail.com');
define('DEFAULT_ADMIN_PASS', 'BBS_)AhQIM]nOdQ.mXhp[m1nW}m$ng%t~j]c03QDaiBZTIyA06');

// المنطقة الزمنية
date_default_timezone_set('Asia/Damascus');

// بدء الجلسة بشكل آمن
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// تضمين ملفات الإعدادات
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
