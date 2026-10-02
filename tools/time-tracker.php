<?php
/**
 * أداة: تتبع الوقت
 */
require_once __DIR__ . '/../config/app.php';
$needsLogin = !isLoggedIn();

if (isLoggedIn()) {
    $db = getDB();
    $userId = $_SESSION['user_id'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_entry'])) {
        $project = sanitize($_POST['project_name'] ?? '');
        $desc = sanitize($_POST['description'] ?? '');
        $duration = (int)($_POST['duration_minutes'] ?? 0);
        if ($project && $duration > 0) {
            $stmt = $db->prepare("INSERT INTO time_entries (user_id, project_name, description, start_time, end_time, duration_minutes) VALUES (?, ?, ?, NOW(), NOW(), ?)");
            $stmt->execute([$userId, $project, $desc, $duration]);
        }
    }
    
    if (isset($_GET['del']) && is_numeric($_GET['del'])) {
        $stmt = $db->prepare("DELETE FROM time_entries WHERE id = ? AND user_id = ?");
        $stmt->execute([(int)$_GET['del'], $userId]);
        header("Location: " . $_SERVER['PHP_SELF'] . "?slug=time-tracker");
        exit;
    }
    
    $stmt = $db->prepare("SELECT * FROM time_entries WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$userId]);
    $entries = $stmt->fetchAll();
    
    $stmt = $db->prepare("SELECT project_name, SUM(duration_minutes) as total FROM time_entries WHERE user_id = ? GROUP BY project_name ORDER BY total DESC");
    $stmt->execute([$userId]);
    $summary = $stmt->fetchAll();
}

include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<?php if ($needsLogin): ?>
<div class="empty-state"><i class="fas fa-lock"></i><h3>يجب تسجيل الدخول</h3><a href="<?= BASE_URL ?>auth/login.php" class="btn btn-primary" style="margin-top:1rem">تسجيل الدخول</a></div>
<?php else: ?>

<!-- ساعة إيقاف -->
<div class="card" style="margin-bottom:1.5rem;text-align:center">
    <div style="font-size:3rem;font-weight:800;font-family:monospace;color:var(--text-accent-light);margin:1rem 0" id="stopwatch">00:00:00</div>
    <div class="d-flex gap-1" style="justify-content:center;margin-bottom:1rem">
        <button class="btn btn-primary" id="swStart" onclick="startStopwatch()"><i class="fas fa-play"></i> بدء</button>
        <button class="btn btn-warning" id="swPause" onclick="pauseStopwatch()" style="display:none"><i class="fas fa-pause"></i> إيقاف مؤقت</button>
        <button class="btn btn-danger" id="swStop" onclick="stopStopwatch()" style="display:none"><i class="fas fa-stop"></i> إنهاء</button>
    </div>
</div>

<!-- إدخال يدوي -->
<div class="card" style="margin-bottom:1.5rem">
    <h3 style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:1rem">إدخال يدوي</h3>
    <form method="POST" class="d-flex gap-1" style="flex-wrap:wrap;align-items:flex-end">
        <input type="hidden" name="add_entry" value="1">
        <div class="form-group" style="flex:2;min-width:150px;margin-bottom:0">
            <input type="text" name="project_name" class="form-control" placeholder="اسم المشروع" required id="timerProject">
        </div>
        <div class="form-group" style="flex:2;min-width:150px;margin-bottom:0">
            <input type="text" name="description" class="form-control" placeholder="وصف (اختياري)">
        </div>
        <div class="form-group" style="flex:1;min-width:100px;margin-bottom:0">
            <input type="number" name="duration_minutes" class="form-control" placeholder="الدقائق" required min="1" id="timerDuration">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0"><i class="fas fa-plus"></i></button>
    </form>
</div>

<?php if (!empty($summary)): ?>
<div class="stats-grid">
    <?php $colors = ['purple','blue','green','orange']; foreach ($summary as $i => $s): ?>
    <div class="stat-card">
        <div class="stat-icon <?= $colors[$i % 4] ?>"><i class="fas fa-project-diagram"></i></div>
        <div>
            <div class="stat-value"><?= floor($s['total']/60) ?>h <?= $s['total']%60 ?>m</div>
            <div class="stat-label"><?= sanitize($s['project_name']) ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($entries)): ?>
<div class="table-container">
    <table>
        <thead><tr><th>المشروع</th><th>الوصف</th><th>المدة</th><th>التاريخ</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($entries as $e): ?>
            <tr>
                <td><strong><?= sanitize($e['project_name']) ?></strong></td>
                <td style="color:var(--text-secondary)"><?= sanitize($e['description'] ?: '-') ?></td>
                <td><span class="badge badge-primary"><?= floor($e['duration_minutes']/60) ?>h <?= $e['duration_minutes']%60 ?>m</span></td>
                <td style="color:var(--text-muted);font-size:0.8rem"><?= formatDate($e['created_at']) ?></td>
                <td><a href="?slug=time-tracker&del=<?= $e['id'] ?>" class="btn btn-ghost btn-sm" style="color:#ef4444" onclick="return confirm('حذف؟')"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; ?>

<script>
let swInterval, swSeconds = 0, swRunning = false;
function startStopwatch() {
    if (swRunning) return;
    swRunning = true;
    document.getElementById('swStart').style.display = 'none';
    document.getElementById('swPause').style.display = '';
    document.getElementById('swStop').style.display = '';
    swInterval = setInterval(() => {
        swSeconds++;
        const h = String(Math.floor(swSeconds/3600)).padStart(2,'0');
        const m = String(Math.floor((swSeconds%3600)/60)).padStart(2,'0');
        const s = String(swSeconds%60).padStart(2,'0');
        document.getElementById('stopwatch').textContent = `${h}:${m}:${s}`;
    }, 1000);
}
function pauseStopwatch() {
    clearInterval(swInterval);
    swRunning = false;
    document.getElementById('swStart').style.display = '';
    document.getElementById('swStart').innerHTML = '<i class="fas fa-play"></i> استكمال';
    document.getElementById('swPause').style.display = 'none';
}
function stopStopwatch() {
    clearInterval(swInterval);
    swRunning = false;
    const minutes = Math.ceil(swSeconds / 60);
    document.getElementById('timerDuration').value = minutes;
    document.getElementById('swStart').style.display = '';
    document.getElementById('swStart').innerHTML = '<i class="fas fa-play"></i> بدء';
    document.getElementById('swPause').style.display = 'none';
    document.getElementById('swStop').style.display = 'none';
    swSeconds = 0;
    document.getElementById('stopwatch').textContent = '00:00:00';
    showToast(`تم تسجيل ${minutes} دقيقة. أدخل اسم المشروع واحفظ.`);
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
