<?php
/**
 * صفحة المفضلة
 */
require_once __DIR__ . '/../config/app.php';
requireLogin();

$pageTitle = 'المفضلة';
$currentPage = 'favorites';

$favorites = getFavorites($_SESSION['user_id']);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-heart"></i>
        أدواتي المفضلة
    </h1>
    <p class="page-subtitle">الأدوات التي أضفتها إلى مفضلتك للوصول السريع</p>
</div>

<?php if (!empty($favorites)): ?>
<div class="tools-grid">
    <?php foreach ($favorites as $tool): ?>
    <a href="<?= BASE_URL ?>tool.php?slug=<?= $tool['slug'] ?>" class="tool-card">
        <div class="tool-card-icon">
            <i class="fas <?= $tool['icon'] ?>"></i>
        </div>
        <h3 class="tool-card-title"><?= sanitize($tool['name']) ?></h3>
        <p class="tool-card-desc"><?= sanitize($tool['description']) ?></p>
        <div class="tool-card-footer">
            <span class="tool-card-category"><?= getCategoryName($tool['category']) ?></span>
            <button class="tool-card-fav active" onclick="event.preventDefault();event.stopPropagation();toggleFavorite(<?= $tool['id'] ?>,this);this.closest('.tool-card').style.display='none'">
                <i class="fas fa-heart"></i>
            </button>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-heart"></i>
    <h3>لا توجد أدوات في المفضلة</h3>
    <p>اضغط على أيقونة القلب في أي أداة لإضافتها للمفضلة</p>
    <a href="<?= BASE_URL ?>" class="btn btn-primary" style="margin-top:1rem">تصفح الأدوات</a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
