<?php
/**
 * أداة: مولد رسالة تقديم (Freelance Proposal)
 */
$result = '';
$templates = getTemplates('proposal-generator');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $templateType = sanitize($_POST['template'] ?? 'freelance');
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'client_name' => sanitize($_POST['client_name'] ?? ''),
        'project_title' => sanitize($_POST['project_title'] ?? ''),
        'why_me' => sanitize($_POST['why_me'] ?? ''),
        'work_plan' => sanitize($_POST['work_plan'] ?? ''),
        'duration' => sanitize($_POST['duration'] ?? ''),
        'budget' => sanitize($_POST['budget'] ?? ''),
        'portfolio_links' => sanitize($_POST['portfolio_links'] ?? ''),
    ];
    
    $template = getTemplate('proposal-generator', $templateType);
    if ($template) {
        $result = applyTemplate($template['content'], $data);
    }
    incrementToolUsage($toolId);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info">
        <h1><?= sanitize($tool['name']) ?></h1>
        <p><?= sanitize($tool['description']) ?></p>
    </div>
</div>

<div class="card">
    <form method="POST">
        <div class="form-group">
            <label class="form-label">نوع الرسالة</label>
            <select name="template" class="form-control">
                <?php foreach ($templates as $t): ?>
                <option value="<?= $t['type'] ?>"><?= sanitize($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">اسمك <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">اسم العميل</label>
                <input type="text" name="client_name" class="form-control" placeholder="اسم العميل أو الشركة">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">عنوان المشروع <span class="required">*</span></label>
            <input type="text" name="project_title" class="form-control" placeholder="وصف مختصر للمشروع" required>
        </div>
        <div class="form-group">
            <label class="form-label">لماذا أنت مناسب؟</label>
            <textarea name="why_me" class="form-control" rows="3" placeholder="اذكر خبراتك ومهاراتك المتعلقة بالمشروع..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">خطة العمل</label>
            <textarea name="work_plan" class="form-control" rows="3" placeholder="1. تحليل المتطلبات&#10;2. التصميم&#10;3. التطوير&#10;4. الاختبار"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">المدة المتوقعة</label>
                <input type="text" name="duration" class="form-control" placeholder="أسبوعين">
            </div>
            <div class="form-group">
                <label class="form-label">الميزانية</label>
                <input type="text" name="budget" class="form-control" placeholder="$500">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">أعمال سابقة (روابط)</label>
            <textarea name="portfolio_links" class="form-control" rows="2" placeholder="رابط 1&#10;رابط 2"></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد الرسالة</button>
    </form>
</div>

<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> الرسالة جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('proposal.txt')"><i class="fas fa-download"></i> تحميل</button>
            <?php if (isLoggedIn()): ?>
            <button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'رسالة تقديم', document.getElementById('resultContent').textContent)"><i class="fas fa-save"></i> حفظ</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
