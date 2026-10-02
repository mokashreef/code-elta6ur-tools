<?php
/**
 * إحصائيات الاستخدام
 */
require_once __DIR__ . '/../config/app.php';
requireAdmin();

$pageTitle = 'الإحصائيات';
$currentPage = 'admin-stats';
$db = getDB();

// إحصائيات عامة
$stats = getStats();

// آخر النشاطات
$logs = $db->query("SELECT l.*, u.name as user_name FROM logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 30")->fetchAll();

// المستخدمين الجدد (آخر 7 أيام)
$newUsers = $db->query("SELECT COUNT(*) as c FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch()['c'];

// النتائج اليوم
$todayOutputs = $db->query("SELECT COUNT(*) as c FROM user_outputs WHERE DATE(created_at) = CURDATE()")->fetch()['c'];

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-chart-pie"></i> إحصائيات الموقع</h1>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-icon purple"><i class="fas fa-users"></i></div><div><div class="stat-value"><?= $stats['users'] ?></div><div class="stat-label">إجمالي المستخدمين</div></div></div>
    <div class="stat-card"><div class="stat-icon blue"><i class="fas fa-user-plus"></i></div><div><div class="stat-value"><?= $newUsers ?></div><div class="stat-label">مستخدمين جدد (7 أيام)</div></div></div>
    <div class="stat-card"><div class="stat-icon green"><i class="fas fa-save"></i></div><div><div class="stat-value"><?= $stats['outputs'] ?></div><div class="stat-label">إجمالي النتائج</div></div></div>
    <div class="stat-card"><div class="stat-icon orange"><i class="fas fa-calendar-day"></i></div><div><div class="stat-value"><?= $todayOutputs ?></div><div class="stat-label">نتائج اليوم</div></div></div>
</div>

<div class="card">
    <h3 class="section-title"><i class="fas fa-history"></i> آخر النشاطات</h3>
    <?php if (!empty($logs)): ?>
    <div class="table-container">
        <table>
            <thead><tr><th>المستخدم</th><th>الإجراء</th><th>التفاصيل</th><th>الوقت</th></tr></thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= sanitize($log['user_name'] ?? 'ضيف') ?></td>
                    <td><span class="badge badge-primary"><?= sanitize($log['action']) ?></span></td>
                    <td style="color:var(--text-secondary);font-size:0.8rem"><?= sanitize($log['details'] ?? '-') ?></td>
                    <td style="color:var(--text-muted);font-size:0.8rem"><?= formatDate($log['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <p style="color:var(--text-muted)">لا توجد نشاطات مسجلة</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
