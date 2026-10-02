<?php
/**
 * أداة: حفظ ملاحظات
 */
require_once __DIR__ . '/../config/app.php';
$needsLogin = !isLoggedIn();

if (isLoggedIn()) {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['add_note'])) {
            $title = sanitize($_POST['title'] ?? '') ?: 'ملاحظة جديدة';
            $content = $_POST['content'] ?? '';
            $color = sanitize($_POST['color'] ?? '#6c63ff');
            $stmt = $db->prepare("INSERT INTO notes (user_id, title, content, color) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $content, $color]);
        } elseif (isset($_POST['update_note'])) {
            $id = (int)$_POST['note_id'];
            $title = sanitize($_POST['title'] ?? '');
            $content = $_POST['content'] ?? '';
            $stmt = $db->prepare("UPDATE notes SET title = ?, content = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$title, $content, $id, $userId]);
        }
    }
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $stmt = $db->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['del'], $userId]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?slug=notes");
        exit;
    }
    
    if (isset($_GET['pin']) && is_numeric($_GET['pin'])) {
        $stmt = $db->prepare("UPDATE notes SET pinned = NOT pinned WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['pin'], $userId]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?slug=notes");
        exit;
    }
    
    $stmt = $db->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY pinned DESC, updated_at DESC");
    $stmt->execute([$userId]);
    $notes = $stmt->fetchAll();
    
    $editNote = null;
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
        $stmt = $db->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['edit'], $userId]);
        $editNote = $stmt->fetch();
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<?php if ($needsLogin): ?>
<div class="empty-state"><i class="fas fa-lock"></i><h3>يجب تسجيل الدخول</h3><p>هذه الأداة تتطلب تسجيل الدخول</p><a href="<?= BASE_URL ?>auth/login.php" class="btn btn-primary" style="margin-top:1rem">تسجيل الدخول</a></div>
<?php else: ?>

<?php if ($editNote): ?>
<div class="card">
    <form method="POST">
        <input type="hidden" name="update_note" value="1">
        <input type="hidden" name="note_id" value="<?= $editNote['id'] ?>">
        <div class="form-group"><label class="form-label">العنوان</label><input type="text" name="title" class="form-control" value="<?= sanitize($editNote['title']) ?>"></div>
        <div class="form-group"><label class="form-label">المحتوى</label><textarea name="content" class="form-control" rows="8"><?= sanitize($editNote['content']) ?></textarea></div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            <a href="?slug=notes" class="btn btn-ghost">إلغاء</a>
        </div>
    </form>
</div>
<?php else: ?>

<!-- إضافة ملاحظة -->
<div class="card" style="margin-bottom:1.5rem">
    <form method="POST">
        <input type="hidden" name="add_note" value="1">
        <div class="form-row">
            <div class="form-group" style="flex:3"><input type="text" name="title" class="form-control" placeholder="عنوان الملاحظة..."></div>
            <div class="form-group" style="flex:0"><input type="color" name="color" value="#6c63ff" style="width:40px;height:40px;border:none;border-radius:8px;cursor:pointer"></div>
        </div>
        <div class="form-group"><textarea name="content" class="form-control" rows="3" placeholder="اكتب ملاحظتك..."></textarea></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة ملاحظة</button>
    </form>
</div>

<!-- الملاحظات -->
<?php if (!empty($notes)): ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1rem">
    <?php foreach ($notes as $note): ?>
    <div class="card" style="border-top:3px solid <?= sanitize($note['color']) ?>;position:relative">
        <?php if ($note['pinned']): ?><span class="badge badge-warning" style="position:absolute;top:0.5rem;left:0.5rem"><i class="fas fa-thumbtack"></i></span><?php endif; ?>
        <h3 style="font-size:1rem;margin-bottom:0.5rem"><?= sanitize($note['title']) ?></h3>
        <p style="font-size:0.85rem;color:var(--text-secondary);margin-bottom:0.75rem;white-space:pre-wrap;max-height:120px;overflow:hidden"><?= sanitize($note['content']) ?></p>
        <div style="font-size:0.7rem;color:var(--text-muted);margin-bottom:0.5rem"><?= formatDate($note['updated_at']) ?></div>
        <div class="d-flex gap-1">
            <a href="?slug=notes&edit=<?= $note['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-edit"></i></a>
            <a href="?slug=notes&pin=<?= $note['id'] ?>" class="btn btn-ghost btn-sm"><i class="fas fa-thumbtack"></i></a>
            <a href="?slug=notes&del=<?= $note['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="return confirm('حذف؟')"><i class="fas fa-trash"></i></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state"><i class="fas fa-sticky-note"></i><h3>لا توجد ملاحظات</h3><p>أضف ملاحظتك الأولى</p></div>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
