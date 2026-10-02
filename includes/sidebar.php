<?php
/**
 * Sidebar مشترك
 */
$categories = getAppCategories();

// جلب الأدوات
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
                <i class="fas fa-cubes"></i>
            </div>
            <div class="logo-text">
                <span class="logo-title">كود التطور</span>
                <span class="logo-subtitle">منصة الأدوات الشاملة</span>
            </div>
        </a>
    </div>

    <!-- التنقل -->
    <nav class="sidebar-nav">
        <!-- الرئيسية -->
        <a href="<?= BASE_URL ?>" class="nav-item <?= $currentPage === 'home' ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            <span>الرئيسية (<?= count($allTools) ?> أداة)</span>
        </a>

        <!-- منظومة كود التطور -->
        <a href="<?= BASE_URL ?>ecosystem.php" class="nav-item <?= $currentPage === 'ecosystem' ? 'active' : '' ?>">
            <i class="fas fa-network-wired text-accent"></i>
            <span>منظومة كود التطور</span>
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
        <?php 
        $hasTools = isset($toolsByCategory[$catKey]) && count($toolsByCategory[$catKey]) > 0;
        if ($hasTools):
            $isGroupActive = false;
            if (isset($currentTool)) {
                foreach ($toolsByCategory[$catKey] as $t) {
                    if ($t['slug'] === $currentTool) {
                        $isGroupActive = true;
                        break;
                    }
                }
            }
        ?>
        <div class="nav-group <?= $isGroupActive ? '' : 'collapsed' ?>">
            <div class="nav-group-title">
                <i class="fas <?= $catInfo['icon'] ?>"></i>
                <span><?= $catInfo['short_name'] ?? $catInfo['name'] ?></span>
                <span class="nav-count-badge"><?= count($toolsByCategory[$catKey]) ?></span>
                <i class="fas fa-chevron-down nav-group-arrow"></i>
            </div>
            <div class="nav-group-items">
                <?php foreach ($toolsByCategory[$catKey] as $tool): ?>
                <a href="<?= BASE_URL ?>tool.php?slug=<?= $tool['slug'] ?>" 
                   class="nav-item nav-sub-item <?= (isset($currentTool) && $currentTool === $tool['slug']) ? 'active' : '' ?>"
                   title="<?= sanitize($tool['name']) ?>">
                    <i class="fas <?= $tool['icon'] ?>"></i>
                    <span><?= sanitize($tool['name']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
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
            <span>النسخة <?= APP_VERSION ?> | كود التطور</span>
            <span>تطوير: <a href="https://mohammad.code-elta6ur.com" target="_blank" rel="noopener noreferrer" style="color:var(--text-accent-light);text-decoration:none">م. محمد أبو خشريف</a></span>
        </div>
    </div>
</aside>
