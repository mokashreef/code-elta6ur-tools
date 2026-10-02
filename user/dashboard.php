<?php
/**
 * لوحة تحكم المستخدم
 */
require_once __DIR__ . '/../config/app.php';
requireLogin();

$pageTitle = 'لوحة التحكم';
$currentPage = 'dashboard';

$user = getCurrentUser();
$outputsCount = getUserOutputsCount($_SESSION['user_id']);
$favorites = getFavorites($_SESSION['user_id']);
$recentOutputs = getUserOutputs($_SESSION['user_id'], 5);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i>
        مرحباً، <?= sanitize($user['name']) ?> 👋
    </h1>
    <p class="page-subtitle">هذه لوحة التحكم الخاصة بك</p>
</div>

<!-- الإحصائيات -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-save"></i>
        </div>
        <div>
            <div class="stat-value"><?= $outputsCount ?></div>
            <div class="stat-label">نتائج محفوظة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-heart"></i>
        </div>
        <div>
            <div class="stat-value"><?= count($favorites) ?></div>
            <div class="stat-label">أدوات مفضلة</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <div class="stat-value"><?= formatDate($user['created_at']) ?></div>
            <div class="stat-label">تاريخ الانضمام</div>
        </div>
    </div>
</div>

<!-- الأدوات المفضلة -->
<?php if (!empty($favorites)): ?>
<h2 class="section-title">
    <i class="fas fa-heart"></i>
    أدواتك المفضلة
</h2>
<div class="tools-grid" style="margin-bottom:2rem">
    <?php foreach (array_slice($favorites, 0, 4) as $fav): ?>
    <a href="<?= BASE_URL ?>tool.php?slug=<?= $fav['slug'] ?>" class="tool-card">
        <div class="tool-card-icon">
            <i class="fas <?= $fav['icon'] ?>"></i>
        </div>
        <h3 class="tool-card-title"><?= sanitize($fav['name']) ?></h3>
        <p class="tool-card-desc"><?= sanitize($fav['description']) ?></p>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- آخر النتائج -->
<h2 class="section-title">
    <i class="fas fa-history"></i>
    آخر النتائج المحفوظة
</h2>

<?php if (!empty($recentOutputs)): ?>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>العنوان</th>
                <th>الأداة</th>
                <th>التاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOutputs as $output): ?>
            <tr>
                <td><?= sanitize($output['title'] ?: 'بدون عنوان') ?></td>
                <td>
                    <span class="badge badge-primary">
                        <i class="fas <?= $output['tool_icon'] ?>" style="margin-left:0.3rem"></i>
                        <?= sanitize($output['tool_name']) ?>
                    </span>
                </td>
                <td style="color:var(--text-muted)"><?= formatDate($output['created_at']) ?></td>
                <td>
                    <a href="saved-outputs.php?view=<?= $output['id'] ?>" class="btn btn-ghost btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div style="margin-top:1rem">
    <a href="saved-outputs.php" class="btn btn-ghost btn-sm">عرض الكل <i class="fas fa-arrow-left"></i></a>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-inbox"></i>
    <h3>لا توجد نتائج محفوظة</h3>
    <p>ابدأ باستخدام الأدوات واحفظ نتائجك للرجوع إليها لاحقاً</p>
    <a href="<?= BASE_URL ?>" class="btn btn-primary" style="margin-top:1rem">
        <i class="fas fa-tools"></i> تصفح الأدوات
    </a>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
