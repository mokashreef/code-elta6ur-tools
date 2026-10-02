<?php
/**
 * أداة: قائمة مهام (To-Do)
 */
require_once __DIR__ . '/../config/app.php';

// التأكد من تسجيل الدخول لهذه الأداة
$needsLogin = !isLoggedIn();

if (isLoggedIn()) {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    // إضافة مهمة
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
        $title = sanitize($_POST['title'] ?? '');
        $priority = sanitize($_POST['priority'] ?? 'medium');
        $dueDate = sanitize($_POST['due_date'] ?? '') ?: null;
        if ($title) {
            $stmt = $db->prepare("INSERT INTO todos (user_id, title, priority, due_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $title, $priority, $dueDate]);
        }
    }
    
    // تبديل حالة المهمة
    if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
        $stmt = $db->prepare("UPDATE todos SET completed = NOT completed WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['toggle'], $userId]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?slug=todo-list");
        exit;
    }
    
    // حذف مهمة
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $stmt = $db->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['del'], $userId]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?slug=todo-list");
        exit;
    }
    
    // جلب المهام
    $stmt = $db->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY completed ASC, priority DESC, created_at DESC");
    $stmt->execute([$userId]);
    $todos = $stmt->fetchAll();
}

include __DIR__ . '/../includes/header.php';
?>

<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<?php if ($needsLogin): ?>
<div class="empty-state">
    <i class="fas fa-lock"></i>
    <h3>يجب تسجيل الدخول</h3>
    <p>هذه الأداة تتطلب تسجيل الدخول لحفظ المهام</p>
    <a href="<?= BASE_URL ?>auth/login.php" class="btn btn-primary" style="margin-top:1rem">تسجيل الدخول</a>
</div>
<?php else: ?>

<!-- إضافة مهمة -->
<div class="card" style="margin-bottom:1.5rem">
    <form method="POST" class="d-flex gap-1" style="flex-wrap:wrap;align-items:flex-end">
        <input type="hidden" name="add_task" value="1">
        <div class="form-group" style="flex:3;min-width:200px;margin-bottom:0">
            <input type="text" name="title" class="form-control" placeholder="أضف مهمة جديدة..." required>
        </div>
        <div class="form-group" style="flex:1;min-width:120px;margin-bottom:0">
            <select name="priority" class="form-control">
                <option value="low">منخفضة</option>
                <option value="medium" selected>متوسطة</option>
                <option value="high">عالية</option>
            </select>
        </div>
        <div class="form-group" style="flex:1;min-width:130px;margin-bottom:0">
            <input type="date" name="due_date" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0"><i class="fas fa-plus"></i> إضافة</button>
    </form>
</div>

<!-- قائمة المهام -->
<?php if (!empty($todos)): ?>
<div class="card">
    <?php 
    $completed = 0;
    foreach ($todos as $t) if ($t['completed']) $completed++;
    $total = count($todos);
    $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
    ?>
    <div class="d-flex align-center justify-between mb-2">
        <span style="color:var(--text-secondary);font-size:0.85rem"><?= $completed ?> من <?= $total ?> مكتملة (<?= $percentage ?>%)</span>
    </div>
    <div style="height:4px;background:var(--bg-input);border-radius:2px;margin-bottom:1rem;overflow:hidden">
        <div style="height:100%;width:<?= $percentage ?>%;background:var(--gradient-success);border-radius:2px;transition:width 0.3s"></div>
    </div>
    
    <?php foreach ($todos as $todo): ?>
    <div class="d-flex align-center gap-1" style="padding:0.75rem;border-bottom:1px solid var(--border-color);<?= $todo['completed'] ? 'opacity:0.5' : '' ?>">
        <a href="?slug=todo-list&toggle=<?= $todo['id'] ?>" style="font-size:1.1rem;color:<?= $todo['completed'] ? '#10b981' : 'var(--text-muted)' ?>;text-decoration:none">
            <i class="fas fa-<?= $todo['completed'] ? 'check-circle' : 'circle' ?>"></i>
        </a>
        <span style="flex:1;<?= $todo['completed'] ? 'text-decoration:line-through' : '' ?>"><?= sanitize($todo['title']) ?></span>
        <span class="badge badge-<?= $todo['priority'] === 'high' ? 'danger' : ($todo['priority'] === 'medium' ? 'warning' : 'primary') ?>">
            <?= $todo['priority'] === 'high' ? 'عالية' : ($todo['priority'] === 'medium' ? 'متوسطة' : 'منخفضة') ?>
        </span>
        <?php if ($todo['due_date']): ?>
        <span style="font-size:0.75rem;color:var(--text-muted)"><i class="fas fa-calendar"></i> <?= $todo['due_date'] ?></span>
        <?php endif; ?>
        <a href="?slug=todo-list&del=<?= $todo['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="return confirm('حذف؟')">
            <i class="fas fa-trash"></i>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-check-circle"></i>
    <h3>لا توجد مهام</h3>
    <p>أضف مهمتك الأولى للبدء</p>
</div>
<?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
