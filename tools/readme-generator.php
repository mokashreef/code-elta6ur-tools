<?php
/**
 * أداة: مولد README لـ GitHub
 */
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'project_name' => sanitize($_POST['project_name'] ?? ''),
        'description' => sanitize($_POST['description'] ?? ''),
        'features' => sanitize($_POST['features'] ?? ''),
        'requirements' => sanitize($_POST['requirements'] ?? ''),
        'installation' => sanitize($_POST['installation'] ?? ''),
        'usage' => sanitize($_POST['usage'] ?? ''),
        'screenshots' => sanitize($_POST['screenshots'] ?? ''),
        'contributing' => sanitize($_POST['contributing'] ?? 'المساهمات مرحب بها! افتح Issue أو Pull Request.'),
        'license' => sanitize($_POST['license'] ?? 'MIT'),
        'contact' => sanitize($_POST['contact'] ?? ''),
    ];
    
    $template = getTemplate('readme-generator', 'standard');
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
            <label class="form-label">اسم المشروع <span class="required">*</span></label>
            <input type="text" name="project_name" class="form-control" placeholder="My Awesome Project" required>
        </div>
        <div class="form-group">
            <label class="form-label">وصف المشروع</label>
            <textarea name="description" class="form-control" rows="2" placeholder="وصف مختصر لما يفعله المشروع"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">المميزات</label>
            <textarea name="features" class="form-control" rows="3" placeholder="- ميزة 1&#10;- ميزة 2&#10;- ميزة 3"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">المتطلبات</label>
            <textarea name="requirements" class="form-control" rows="2" placeholder="- PHP 7.4+&#10;- MySQL 5.7+&#10;- Node.js 16+"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">خطوات التثبيت</label>
            <textarea name="installation" class="form-control" rows="3" placeholder="git clone https://github.com/user/repo.git&#10;cd repo&#10;npm install"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">طريقة الاستخدام</label>
            <textarea name="usage" class="form-control" rows="2" placeholder="npm start"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الرخصة</label>
                <select name="license" class="form-control">
                    <option value="MIT">MIT</option>
                    <option value="Apache 2.0">Apache 2.0</option>
                    <option value="GPL v3">GPL v3</option>
                    <option value="BSD 3-Clause">BSD 3-Clause</option>
                    <option value="Unlicense">Unlicense</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">التواصل</label>
                <input type="text" name="contact" class="form-control" placeholder="email@example.com">
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد README</button>
    </form>
</div>

<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> README جاهز!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('README.md')"><i class="fas fa-download"></i> تحميل</button>
            <?php if (isLoggedIn()): ?>
            <button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'README', document.getElementById('resultContent').textContent)"><i class="fas fa-save"></i> حفظ</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
