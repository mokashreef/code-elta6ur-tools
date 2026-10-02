<?php
/**
 * إدارة المستخدمين
 */
require_once __DIR__ . '/../config/app.php';
requireAdmin();

$pageTitle = 'إدارة المستخدمين';
$currentPage = 'admin-users';
$db = getDB();

// حذف مستخدم
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('تم حذف المستخدم', 'success');
    }
    redirect('admin/users.php');
}

// تغيير الدور
if (isset($_GET['toggle_role']) && is_numeric($_GET['toggle_role'])) {
    $id = (int)$_GET['toggle_role'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $db->prepare("UPDATE users SET role = IF(role='admin','user','admin') WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('تم تغيير الصلاحية', 'success');
    }
    redirect('admin/users.php');
}

$users = $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-users-cog"></i> إدارة المستخدمين</h1>
    <p class="page-subtitle"><?= count($users) ?> مستخدم مسجل</p>
</div>

<div class="table-container">
    <table>
        <thead><tr><th>#</th><th>الاسم</th><th>البريد</th><th>الدور</th><th>تاريخ التسجيل</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><strong><?= sanitize($u['name']) ?></strong></td>
                <td style="direction:ltr"><?= sanitize($u['email']) ?></td>
                <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'danger' : 'primary' ?>"><?= $u['role'] === 'admin' ? 'أدمن' : 'مستخدم' ?></span></td>
                <td style="color:var(--text-muted)"><?= formatDate($u['created_at']) ?></td>
                <td>
                    <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                    <div class="d-flex gap-1">
                        <a href="?toggle_role=<?= $u['id'] ?>" class="btn btn-ghost btn-sm" title="تغيير الدور"><i class="fas fa-user-shield"></i></a>
                        <a href="?delete=<?= $u['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="return confirm('حذف هذا المستخدم وجميع بياناته؟')" title="حذف"><i class="fas fa-trash"></i></a>
                    </div>
                    <?php else: ?>
                    <span style="color:var(--text-muted);font-size:0.8rem">أنت</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
