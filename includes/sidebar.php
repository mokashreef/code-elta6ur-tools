<?php
/**
 * Sidebar مشترك
 */
$categories = [
    'career' => ['name' => 'العمل الحر والمهني', 'icon' => 'fa-rocket'],
    'generators' => ['name' => 'المولدات', 'icon' => 'fa-magic'],
    'text' => ['name' => 'النصوص والأكواد', 'icon' => 'fa-code'],
    'productivity' => ['name' => 'الإنتاجية', 'icon' => 'fa-chart-line']
];

// جلب الأدوات من قاعدة البيانات
try {
    $allTools = getAllTools();
    $toolsByCategory = [];
    foreach ($allTools as $tool) {
        $toolsByCategory[$tool['category']][] = $tool;
    }
} catch (Exception $e) {
    $toolsByCategory = [];
}
?>
<aside class="sidebar" id="sidebar">
    <!-- شعار الموقع -->
    <div class="sidebar-header">
        <a href="<?= BASE_URL ?>" class="sidebar-logo">
            <div class="logo-icon">
                <i class="fas fa-terminal"></i>
            </div>
            <div class="logo-text">
                <span class="logo-title">Code Elta6ur</span>
                <span class="logo-subtitle">أدوات المبرمج السوري</span>
            </div>
        </a>
    </div>

    <!-- التنقل -->
    <nav class="sidebar-nav">
        <!-- الرئيسية -->
        <a href="<?= BASE_URL ?>" class="nav-item <?= $currentPage === 'home' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>الرئيسية</span>
        </a>

        <?php if (isLoggedIn()): ?>
        <a href="<?= BASE_URL ?>user/dashboard.php" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>لوحة التحكم</span>
        </a>
        <a href="<?= BASE_URL ?>user/favorites.php" class="nav-item <?= $currentPage === 'favorites' ? 'active' : '' ?>">
            <i class="fas fa-heart"></i>
            <span>المفضلة</span>
        </a>
        <?php endif; ?>

        <div class="nav-divider"></div>

        <!-- الأدوات حسب الفئات -->
        <?php foreach ($categories as $catKey => $catInfo): ?>
        <div class="nav-group">
            <div class="nav-group-title">
                <i class="fas <?= $catInfo['icon'] ?>"></i>
                <span><?= $catInfo['name'] ?></span>
                <i class="fas fa-chevron-down nav-group-arrow"></i>
            </div>
            <div class="nav-group-items">
                <?php if (isset($toolsByCategory[$catKey])): ?>
                <?php foreach ($toolsByCategory[$catKey] as $tool): ?>
                <a href="<?= BASE_URL ?>tool.php?slug=<?= $tool['slug'] ?>" 
                   class="nav-item nav-sub-item <?= (isset($currentTool) && $currentTool === $tool['slug']) ? 'active' : '' ?>">
                    <i class="fas <?= $tool['icon'] ?>"></i>
                    <span><?= sanitize($tool['name']) ?></span>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (isAdmin()): ?>
        <div class="nav-divider"></div>
        <div class="nav-group">
            <div class="nav-group-title admin-title">
                <i class="fas fa-shield-alt"></i>
                <span>إدارة الموقع</span>
                <i class="fas fa-chevron-down nav-group-arrow"></i>
            </div>
            <div class="nav-group-items">
                <a href="<?= BASE_URL ?>admin/index.php" class="nav-item nav-sub-item <?= $currentPage === 'admin-dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span>الإحصائيات</span>
                </a>
                <a href="<?= BASE_URL ?>admin/users.php" class="nav-item nav-sub-item <?= $currentPage === 'admin-users' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i>
                    <span>المستخدمين</span>
                </a>
                <a href="<?= BASE_URL ?>admin/tools-manage.php" class="nav-item nav-sub-item <?= $currentPage === 'admin-tools' ? 'active' : '' ?>">
                    <i class="fas fa-tools"></i>
                    <span>الأدوات</span>
                </a>
                <a href="<?= BASE_URL ?>admin/templates.php" class="nav-item nav-sub-item <?= $currentPage === 'admin-templates' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>القوالب</span>
                </a>
            </div>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Footer Sidebar -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-text">
            <span>النسخة <?= APP_VERSION ?></span>
            <span>صنع بـ ❤️ في سوريا</span>
        </div>
    </div>
</aside>
