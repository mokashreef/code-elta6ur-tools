<?php
/**
 * أداة: مولد Cover Letter
 */
$result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'date' => date('Y/m/d'),
        'company' => sanitize($_POST['company'] ?? ''),
        'position' => sanitize($_POST['position'] ?? ''),
        'intro_paragraph' => sanitize($_POST['intro_paragraph'] ?? ''),
        'qualifications' => sanitize($_POST['qualifications'] ?? ''),
        'why_company' => sanitize($_POST['why_company'] ?? ''),
    ];
    $template = getTemplate('cover-letter', 'professional');
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
        <div class="form-row">
            <div class="form-group"><label class="form-label">اسمك <span class="required">*</span></label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label class="form-label">البريد</label><input type="email" name="email" class="form-control"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">الهاتف</label><input type="text" name="phone" class="form-control"></div>
            <div class="form-group"><label class="form-label">اسم الشركة <span class="required">*</span></label><input type="text" name="company" class="form-control" required></div>
        </div>
        <div class="form-group"><label class="form-label">الوظيفة المطلوبة <span class="required">*</span></label><input type="text" name="position" class="form-control" required></div>
        <div class="form-group"><label class="form-label">مقدمة عن نفسك</label><textarea name="intro_paragraph" class="form-control" rows="3" placeholder="اكتب فقرة تعريفية..."></textarea></div>
        <div class="form-group"><label class="form-label">مؤهلاتك وخبراتك</label><textarea name="qualifications" class="form-control" rows="3" placeholder="- 3 سنوات خبرة في...&#10;- شهادة في..."></textarea></div>
        <div class="form-group"><label class="form-label">لماذا هذه الشركة؟</label><textarea name="why_company" class="form-control" rows="2" placeholder="لماذا تريد العمل في هذه الشركة تحديداً؟"></textarea></div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد الرسالة</button>
    </form>
</div>
<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> الرسالة جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('cover-letter.txt')"><i class="fas fa-download"></i> تحميل</button>
            <?php if (isLoggedIn()): ?><button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'Cover Letter', document.getElementById('resultContent').textContent)"><i class="fas fa-save"></i> حفظ</button><?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
