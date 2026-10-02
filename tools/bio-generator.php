<?php
/**
 * أداة: مولد نبذة احترافية (Bio)
 */
$result = '';
$templates = getTemplates('bio-generator');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = sanitize($_POST['template'] ?? 'short');
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'title' => sanitize($_POST['title'] ?? ''),
        'specialization' => sanitize($_POST['specialization'] ?? ''),
        'years' => sanitize($_POST['years'] ?? ''),
        'field' => sanitize($_POST['field'] ?? ''),
        'achievement' => sanitize($_POST['achievement'] ?? ''),
        'current_work' => sanitize($_POST['current_work'] ?? ''),
        'interests' => sanitize($_POST['interests'] ?? ''),
        'skills' => sanitize($_POST['skills'] ?? ''),
        'projects_count' => sanitize($_POST['projects_count'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
    ];
    $template = getTemplate('bio-generator', $type);
    if ($template) $result = applyTemplate($template['content'], $data);
    incrementToolUsage($toolId);
}
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <form method="POST">
        <div class="form-group">
            <label class="form-label">طول النبذة</label>
            <select name="template" class="form-control">
                <?php foreach ($templates as $t): ?><option value="<?= $t['type'] ?>"><?= sanitize($t['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">الاسم <span class="required">*</span></label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">المسمى</label><input type="text" name="title" class="form-control" placeholder="مطور ويب"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">التخصص</label><input type="text" name="specialization" class="form-control" placeholder="تطوير تطبيقات الويب"></div>
            <div class="form-group"><label class="form-label">سنوات الخبرة</label><input type="number" name="years" class="form-control" min="0" max="50"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">المجال</label><input type="text" name="field" class="form-control" placeholder="تطوير البرمجيات"></div>
            <div class="form-group"><label class="form-label">عدد المشاريع</label><input type="number" name="projects_count" class="form-control" min="0"></div>
        </div>
        <div class="form-group"><label class="form-label">أبرز إنجاز</label><input type="text" name="achievement" class="form-control" placeholder="قمت ببناء منصة حصلت على 10K مستخدم"></div>
        <div class="form-group"><label class="form-label">العمل الحالي</label><input type="text" name="current_work" class="form-control" placeholder="أعمل حالياً على مشروع..."></div>
        <div class="form-group"><label class="form-label">المهارات</label><input type="text" name="skills" class="form-control" placeholder="PHP, JavaScript, React"></div>
        <div class="form-group"><label class="form-label">الاهتمامات</label><input type="text" name="interests" class="form-control" placeholder="الذكاء الاصطناعي، الأمن السيبراني"></div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">البريد</label><input type="email" name="email" class="form-control"></div>
            <div class="form-group"><label class="form-label">LinkedIn</label><input type="text" name="linkedin" class="form-control"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد النبذة</button>
    </form>
</div>
<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> النبذة جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <?php if (isLoggedIn()): ?><button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'نبذة احترافية', document.getElementById('resultContent').textContent)"><i class="fas fa-save"></i> حفظ</button><?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
