<?php
/**
 * الصفحة الرئيسية
 * Code Elta6ur Tools
 */
require_once __DIR__ . '/config/app.php';

$pageTitle = 'الرئيسية';
$currentPage = 'home';

// جلب الأدوات مصنفة
$categories = [
    'career' => ['name' => 'العمل الحر والمهني', 'icon' => 'fa-rocket'],
    'generators' => ['name' => 'المولدات', 'icon' => 'fa-magic'],
    'text' => ['name' => 'النصوص والأكواد', 'icon' => 'fa-code'],
    'productivity' => ['name' => 'الإنتاجية', 'icon' => 'fa-chart-line']
];

try {
    $allTools = getAllTools();
    $toolsByCategory = [];
    foreach ($allTools as $tool) {
        $toolsByCategory[$tool['category']][] = $tool;
    }
    
    // المفضلات
    $userFavorites = [];
    if (isLoggedIn()) {
        $favs = getFavorites($_SESSION['user_id']);
        foreach ($favs as $f) {
            $userFavorites[] = $f['id'];
        }
    }
} catch (Exception $e) {
    $toolsByCategory = [];
    $userFavorites = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-badge">
        <span class="dot"></span>
        <span>25 أداة عملية جاهزة للاستخدام</span>
    </div>
    <h1>أدوات <span>المبرمج السوري</span></h1>
    <p>منصة أدوات عملية تساعدك على بدء العمل الحر، تجهيز ملفاتك، وتسريع إنتاجيتك. بدون تعليم… فقط أدوات.</p>
</section>

<!-- الأدوات -->
<?php foreach ($categories as $catKey => $catInfo): ?>
<?php if (isset($toolsByCategory[$catKey]) && count($toolsByCategory[$catKey]) > 0): ?>
<div class="category-header">
    <div class="category-icon">
        <i class="fas <?= $catInfo['icon'] ?>"></i>
    </div>
    <h2 class="category-title"><?= $catInfo['name'] ?></h2>
    <span class="category-count"><?= count($toolsByCategory[$catKey]) ?> أداة</span>
</div>

<div class="tools-grid">
    <?php foreach ($toolsByCategory[$catKey] as $tool): ?>
    <a href="tool.php?slug=<?= $tool['slug'] ?>" class="tool-card" id="tool-<?= $tool['slug'] ?>">
        <div class="tool-card-icon">
            <i class="fas <?= $tool['icon'] ?>"></i>
        </div>
        <h3 class="tool-card-title"><?= sanitize($tool['name']) ?></h3>
        <p class="tool-card-desc"><?= sanitize($tool['description']) ?></p>
        <div class="tool-card-footer">
            <span class="tool-card-category"><?= getCategoryName($tool['category']) ?></span>
            <?php if (isLoggedIn()): ?>
            <button class="tool-card-fav <?= in_array($tool['id'], $userFavorites) ? 'active' : '' ?>" 
                    onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite(<?= $tool['id'] ?>, this)">
                <i class="<?= in_array($tool['id'], $userFavorites) ? 'fas' : 'far' ?> fa-heart"></i>
            </button>
            <?php endif; ?>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
<?php endforeach; ?>

<?php if (empty($toolsByCategory)): ?>
<div class="empty-state">
    <i class="fas fa-database"></i>
    <h3>لم يتم العثور على أدوات</h3>
    <p>تأكد من إعداد قاعدة البيانات وتشغيل ملف seed.sql</p>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
