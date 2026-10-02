<?php
/**
 * إدارة القوالب
 */
require_once __DIR__ . '/../config/app.php';
requireAdmin();

$pageTitle = 'إدارة القوالب';
$currentPage = 'admin-templates';
$db = getDB();

// إضافة/تعديل قالب
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $toolSlug = sanitize($_POST['tool_slug'] ?? '');
    $type = sanitize($_POST['type'] ?? 'default');
    $name = sanitize($_POST['name'] ?? '');
    $content = $_POST['content'] ?? '';
    
    if (isset($_POST['template_id'])) {
        $stmt = $db->prepare("UPDATE templates SET tool_slug = ?, type = ?, name = ?, content = ? WHERE id = ?");
        $stmt->execute([$toolSlug, $type, $name, $content, (int)$_POST['template_id']]);
        setFlash('تم تحديث القالب', 'success');
    } else {
        $stmt = $db->prepare("INSERT INTO templates (tool_slug, type, name, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$toolSlug, $type, $name, $content]);
        setFlash('تم إضافة القالب', 'success');
    }
    redirect('admin/templates.php');
}

// حذف
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM templates WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    setFlash('تم حذف القالب', 'success');
    redirect('admin/templates.php');
}

$templates = $db->query("SELECT t.*, (SELECT name FROM tools WHERE slug = t.tool_slug LIMIT 1) as tool_name FROM templates t ORDER BY t.tool_slug, t.type")->fetchAll();
$tools = getAllTools(false);

$editTemplate = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM templates WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editTemplate = $stmt->fetch();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title"><i class="fas fa-file-alt"></i> إدارة القوالب النصية</h1>
</div>

<div class="card" style="margin-bottom:1.5rem">
    <h3 class="section-title"><i class="fas fa-<?= $editTemplate ? 'edit' : 'plus' ?>"></i> <?= $editTemplate ? 'تعديل قالب' : 'إضافة قالب جديد' ?></h3>
    <form method="POST">
        <?php if ($editTemplate): ?><input type="hidden" name="template_id" value="<?= $editTemplate['id'] ?>"><?php endif; ?>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الأداة</label>
                <select name="tool_slug" class="form-control" required>
                    <?php foreach ($tools as $t): ?>
                    <option value="<?= $t['slug'] ?>" <?= ($editTemplate && $editTemplate['tool_slug'] === $t['slug']) ? 'selected' : '' ?>><?= sanitize($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label class="form-label">النوع</label><input type="text" name="type" class="form-control" value="<?= sanitize($editTemplate['type'] ?? 'default') ?>" required></div>
            <div class="form-group"><label class="form-label">الاسم</label><input type="text" name="name" class="form-control" value="<?= sanitize($editTemplate['name'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label class="form-label">المحتوى</label><textarea name="content" class="form-control" rows="10" required><?= $editTemplate ? htmlspecialchars($editTemplate['content']) : '' ?></textarea></div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            <?php if ($editTemplate): ?><a href="templates.php" class="btn btn-ghost">إلغاء</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="table-container">
    <table>
        <thead><tr><th>#</th><th>الأداة</th><th>النوع</th><th>الاسم</th><th>إجراءات</th></tr></thead>
        <tbody>
            <?php foreach ($templates as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= sanitize($t['tool_name'] ?? $t['tool_slug']) ?></td>
                <td><span class="badge badge-primary"><?= sanitize($t['type']) ?></span></td>
                <td><?= sanitize($t['name'] ?? '-') ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="?edit=<?= $t['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></a>
                        <a href="?delete=<?= $t['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="return confirm('حذف هذا القالب؟')"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
