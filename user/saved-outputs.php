<?php
/**
 * النتائج المحفوظة
 */
require_once __DIR__ . '/../config/app.php';
requireLogin();

$pageTitle = 'النتائج المحفوظة';
$currentPage = 'saved';

// حذف نتيجة
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteOutput((int)$_GET['delete'], $_SESSION['user_id']);
    setFlash('تم حذف النتيجة', 'success');
    redirect('user/saved-outputs.php');
}

// عرض نتيجة
$viewOutput = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $viewOutput = getOutputById((int)$_GET['view'], $_SESSION['user_id']);
}

// تحديث نتيجة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateId = (int)$_POST['update_id'];
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    updateOutput($updateId, $_SESSION['user_id'], $title, $content);
    setFlash('تم تحديث النتيجة بنجاح', 'success');
    redirect('user/saved-outputs.php');
}

$outputs = getUserOutputs($_SESSION['user_id'], 50);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-save"></i>
        النتائج المحفوظة
    </h1>
    <p class="page-subtitle">جميع النتائج التي حفظتها من الأدوات</p>
</div>

<?php if ($viewOutput): ?>
<!-- عرض نتيجة -->
<div class="card" style="margin-bottom:1.5rem">
    <div class="d-flex align-center justify-between mb-2">
        <h3><?= sanitize($viewOutput['title'] ?: $viewOutput['tool_name']) ?></h3>
        <a href="saved-outputs.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-right"></i> رجوع</a>
    </div>
    
    <form method="POST">
        <input type="hidden" name="update_id" value="<?= $viewOutput['id'] ?>">
        <div class="form-group">
            <label class="form-label">العنوان</label>
            <input type="text" name="title" class="form-control" value="<?= sanitize($viewOutput['title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label class="form-label">المحتوى</label>
            <textarea name="content" class="form-control" rows="12"><?= sanitize($viewOutput['content']) ?></textarea>
        </div>
        <div class="d-flex gap-1">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <button type="button" class="btn btn-ghost" onclick="copyToClipboard(document.querySelector('textarea[name=content]').value)">
                <i class="fas fa-copy"></i> نسخ
            </button>
        </div>
    </form>
</div>
<?php else: ?>

<?php if (!empty($outputs)): ?>
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>العنوان</th>
                <th>الأداة</th>
                <th>التاريخ</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($outputs as $i => $output): ?>
            <tr>
                <td style="color:var(--text-muted)"><?= $i + 1 ?></td>
                <td><?= sanitize($output['title'] ?: 'بدون عنوان') ?></td>
                <td>
                    <span class="badge badge-primary">
                        <i class="fas <?= $output['tool_icon'] ?>" style="margin-left:0.3rem"></i>
                        <?= sanitize($output['tool_name']) ?>
                    </span>
                </td>
                <td style="color:var(--text-muted);font-size:0.8rem"><?= formatDate($output['created_at']) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="?view=<?= $output['id'] ?>" class="btn btn-ghost btn-sm" title="عرض">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="?delete=<?= $output['id'] ?>" class="btn btn-ghost btn-sm" title="حذف"
                           onclick="return confirm('هل أنت متأكد من الحذف؟')" style="color:#ef4444">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-inbox"></i>
    <h3>لا توجد نتائج محفوظة</h3>
    <p>استخدم الأدوات واحفظ نتائجك لتراها هنا</p>
</div>
<?php endif; ?>

<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
