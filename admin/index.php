<?php
/**
 * لوحة تحكم الأدمن - الرئيسية
 */
require_once __DIR__ . '/../config/app.php';
requireAdmin();

$pageTitle = 'لوحة الأدمن';
$currentPage = 'admin-dashboard';
$stats = getStats();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-shield-alt"></i> لوحة تحكم الأدمن</h1>
    <p class="page-subtitle">نظرة عامة على المنصة</p>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-users"></i></div><div><div class="stat-value"><?= $stats['users'] ?></div><div class="stat-label">المستخدمين</div></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-tools"></i></div><div><div class="stat-value"><?= $stats['tools'] ?></div><div class="stat-label">الأدوات النشطة</div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-save"></i></div><div><div class="stat-value"><?= $stats['outputs'] ?></div><div class="stat-label">النتائج المحفوظة</div></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-history"></i></div><div><div class="stat-value"><?= $stats['logs'] ?></div><div class="stat-label">سجل النشاطات</div></div></div>
</div>

<!-- أكثر الأدوات استخداماً -->
<div class="card">
    <h3 class="section-title"><i class="fas fa-chart-bar"></i> أكثر الأدوات استخداماً</h3>
    <?php if (!empty($stats['top_tools'])): ?>
    <div style="display:flex;flex-direction:column;gap:0.75rem">
        <?php 
        $maxUsage = max(array_column($stats['top_tools'], 'usage_count')) ?: 1;
        foreach ($stats['top_tools'] as $t): 
            $width = ($t['usage_count'] / $maxUsage) * 100;
        ?>
        <div>
            <div class="d-flex align-center justify-between mb-1">
                <span style="font-size:0.85rem"><?= sanitize($t['name']) ?></span>
                <span style="font-size:0.8rem;color:var(--text-muted)"><?= $t['usage_count'] ?> استخدام</span>
            </div>
            <div style="height:8px;background:var(--bg-input);border-radius:4px;overflow:hidden">
                <div style="height:100%;width:<?= $width ?>%;background:var(--gradient-accent);border-radius:4px;transition:width 0.5s"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text-muted)">لا توجد بيانات بعد</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
