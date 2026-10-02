<?php
/**
 * إدارة الأدوات
 */
require_once __DIR__ . '/../config/app.php';
requireAdmin();

$pageTitle = 'إدارة الأدوات';
$currentPage = 'admin-tools';
$db = getDB();

// تفعيل/تعطيل
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $stmt = $db->prepare("UPDATE tools SET status = NOT status WHERE id = ?");
    $stmt->execute([(int)$_GET['toggle']]);
    setFlash('تم تحديث حالة الأداة', 'success');
    redirect('admin/tools-manage.php');
}

// تحديث أداة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_tool'])) {
    $id = (int)$_POST['tool_id'];
    $name = sanitize($_POST['name'] ?? '');
    $desc = sanitize($_POST['description'] ?? '');
    $icon = sanitize($_POST['icon'] ?? 'fa-wrench');
    $order = (int)($_POST['sort_order'] ?? 0);
    $stmt = $db->prepare("UPDATE tools SET name = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $icon, $order, $id]);
    setFlash('تم تحديث الأداة', 'success');
    redirect('admin/tools-manage.php');
}

$tools = $db->query("SELECT * FROM tools ORDER BY sort_order ASC")->fetchAll();

// تعديل أداة
$editTool = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM tools WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editTool = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-tools"></i> إدارة الأدوات</h1>
</div>

<?php if ($editTool): ?>
<div class="card" style="margin-bottom:1.5rem">
    <h3 class="section-title"><i class="fas fa-edit"></i> تعديل: <?= sanitize($editTool['name']) ?></h3>
    <form method="POST">
        <input type="hidden" name="update_tool" value="1">
        <input type="hidden" name="tool_id" value="<?= $editTool['id'] ?>">
        <div class="form-row">
            <div class="form-group"><label class="form-label">الاسم</label><input type="text" name="name" class="form-control" value="<?= sanitize($editTool['name']) ?>" required></div>
            <div class="form-group"><label class="form-label">الأيقونة (Font Awesome)</label><input type="text" name="icon" class="form-control" value="<?= sanitize($editTool['icon']) ?>" style="direction:ltr"></div>
        </div>
        <div class="form-group"><label class="form-label">الوصف</label><textarea name="description" class="form-control" rows="2"><?= sanitize($editTool['description']) ?></textarea></div>
        <div class="form-group"><label class="form-label">الترتيب</label><input type="number" name="sort_order" class="form-control" value="<?= $editTool['sort_order'] ?>" style="max-width:100px"></div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            <a href="tools-manage.php" class="btn btn-ghost">إلغاء</a>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="table-container">
    <table>
        <thead><tr><th>#</th><th>الأيقونة</th><th>الاسم</th><th>الفئة</th><th>الحالة</th><th>الاستخدام</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php foreach ($tools as $t): ?>
            <tr style="<?= !$t['status'] ? 'opacity:0.5' : '' ?>">
                <td><?= $t['sort_order'] ?></td>
                <td><i class="fas <?= $t['icon'] ?>" style="color:var(--text-accent)"></i></td>
                <td><strong><?= sanitize($t['name']) ?></strong></td>
                <td><span class="badge badge-primary"><?= getCategoryName($t['category']) ?></span></td>
                <td><span class="badge badge-<?= $t['status'] ? 'success' : 'danger' ?>"><?= $t['status'] ? 'مفعل' : 'معطل' ?></span></td>
                <td><?= $t['usage_count'] ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="?edit=<?= $t['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></a>
                        <a href="?toggle=<?= $t['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-<?= $t['status'] ? 'eye-slash' : 'eye' ?>"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
