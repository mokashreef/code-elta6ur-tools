<?php
/**
 * Header مشترك
 */
$pageTitle = $pageTitle ?? APP_NAME_AR;
$currentPage = $currentPage ?? '';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="description" content="<?= APP_DESCRIPTION ?>">
    <title><?= sanitize($pageTitle) ?> - <?= APP_NAME ?></title>
    
    <!-- الخطوط -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- أيقونات -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- التصميم -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <?php if (isset($extraCSS)): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/<?= $extraCSS ?>">
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar Toggle للموبايل -->
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="القائمة">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- المحتوى الرئيسي -->
    <main class="main-content" id="mainContent">
        <!-- Top Bar -->
        <header class="top-bar">
            <div class="top-bar-right">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="globalSearch" placeholder="ابحث عن أداة..." autocomplete="off">
                    <div class="search-results" id="searchResults"></div>
                </div>
            </div>
            <div class="top-bar-left">
                <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <button class="user-menu-btn" id="userMenuBtn">
                        <div class="user-avatar">
                            <?= mb_substr($_SESSION['user_name'], 0, 1) ?>
                        </div>
                        <span class="user-name"><?= sanitize($_SESSION['user_name']) ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?= BASE_URL ?>user/dashboard.php"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
                        <a href="<?= BASE_URL ?>user/profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                        <a href="<?= BASE_URL ?>user/saved-outputs.php"><i class="fas fa-save"></i> النتائج المحفوظة</a>
                        <?php if (isAdmin()): ?>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>admin/index.php"><i class="fas fa-shield-alt"></i> لوحة الأدمن</a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a href="<?= BASE_URL ?>auth/logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                    </div>
                </div>
                <?php else: ?>
                <div class="auth-buttons">
                    <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-ghost btn-sm">تسجيل الدخول</a>
                    <a href="<?= BASE_URL ?>auth/register.php" class="btn btn-primary btn-sm">إنشاء حساب</a>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- Flash Messages -->
        <?php $flash = getFlash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>" id="flashAlert">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
            <span><?= $flash['message'] ?></span>
            <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
        <?php endif; ?>

        <!-- محتوى الصفحة -->
        <div class="page-content">
