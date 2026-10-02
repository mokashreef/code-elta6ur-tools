<?php
/**
 * أداة: مولد سيرة ذاتية (CV)
 */
$result = '';
$templates = getTemplates('cv-generator');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $templateType = sanitize($_POST['template'] ?? 'professional');
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'title' => sanitize($_POST['title'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'location' => sanitize($_POST['location'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
        'github' => sanitize($_POST['github'] ?? ''),
        'summary' => sanitize($_POST['summary'] ?? ''),
        'experience' => sanitize($_POST['experience'] ?? ''),
        'education' => sanitize($_POST['education'] ?? ''),
        'skills' => sanitize($_POST['skills'] ?? ''),
        'languages' => sanitize($_POST['languages'] ?? ''),
        'projects' => sanitize($_POST['projects'] ?? ''),
    ];
    
    $template = getTemplate('cv-generator', $templateType);
    if ($template) {
        $result = applyTemplate($template['content'], $data);
    }
    
    incrementToolUsage($toolId);
}

include __DIR__ . '/../includes/header.php';
?>

<div class="tool-page-header">
    <div class="tool-page-icon">
        <i class="fas <?= $tool['icon'] ?>"></i>
    </div>
    <div class="tool-page-info">
        <h1><?= sanitize($tool['name']) ?></h1>
        <p><?= sanitize($tool['description']) ?></p>
    </div>
</div>

<div class="card">
    <form method="POST" id="toolForm">
        <!-- اختيار القالب -->
        <div class="form-group">
            <label class="form-label">نوع القالب</label>
            <select name="template" class="form-control">
                <?php foreach ($templates as $t): ?>
                <option value="<?= $t['type'] ?>"><?= sanitize($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="مثال: أحمد محمد" required>
            </div>
            <div class="form-group">
                <label class="form-label">المسمى الوظيفي <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" placeholder="مثال: مطور ويب Full-Stack">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" placeholder="example@email.com">
            </div>
            <div class="form-group">
                <label class="form-label">الهاتف</label>
                <input type="text" name="phone" class="form-control" placeholder="+963 912 345 678">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الموقع</label>
                <input type="text" name="location" class="form-control" placeholder="دمشق، سوريا">
            </div>
            <div class="form-group">
                <label class="form-label">LinkedIn</label>
                <input type="text" name="linkedin" class="form-control" placeholder="linkedin.com/in/username">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">GitHub</label>
            <input type="text" name="github" class="form-control" placeholder="github.com/username">
        </div>

        <div class="form-group">
            <label class="form-label">الملخص المهني</label>
            <textarea name="summary" class="form-control" rows="3" placeholder="اكتب ملخصاً مختصراً عن خبرتك ومهاراتك..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">الخبرات العملية</label>
            <textarea name="experience" class="form-control" rows="4" placeholder="- مطور ويب في شركة X (2022-2024)&#10;- مطور حر على Upwork (2020-2022)"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">التعليم</label>
            <textarea name="education" class="form-control" rows="2" placeholder="بكالوريوس هندسة معلوماتية - جامعة دمشق"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">المهارات التقنية</label>
            <textarea name="skills" class="form-control" rows="2" placeholder="PHP, JavaScript, React, MySQL, Laravel, Node.js"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">اللغات</label>
            <input type="text" name="languages" class="form-control" placeholder="العربية (أم)، الإنجليزية (جيد جداً)">
        </div>

        <div class="form-group">
            <label class="form-label">المشاريع</label>
            <textarea name="projects" class="form-control" rows="3" placeholder="- منصة تعليمية: وصف مختصر&#10;- تطبيق إدارة مهام: وصف مختصر"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-magic"></i> توليد السيرة الذاتية
        </button>
    </form>
</div>

<!-- منطقة النتيجة -->
<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> السيرة الذاتية جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('cv.txt')"><i class="fas fa-download"></i> تحميل</button>
            <?php if (isLoggedIn()): ?>
            <button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'سيرة ذاتية', document.getElementById('resultContent').textContent)">
                <i class="fas fa-save"></i> حفظ
            </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
