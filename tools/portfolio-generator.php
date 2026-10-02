<?php
/**
 * أداة: مولد بورتفوليو نصي
 */
$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => sanitize($_POST['name'] ?? ''),
        'title' => sanitize($_POST['title'] ?? ''),
        'bio' => sanitize($_POST['bio'] ?? ''),
        'skills' => sanitize($_POST['skills'] ?? ''),
        'experience' => sanitize($_POST['experience'] ?? ''),
        'projects' => sanitize($_POST['projects'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'github' => sanitize($_POST['github'] ?? ''),
        'linkedin' => sanitize($_POST['linkedin'] ?? ''),
    ];
    
    $template = getTemplate('portfolio-generator', 'default');
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
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الاسم <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="اسمك الكامل" required>
            </div>
            <div class="form-group">
                <label class="form-label">التخصص</label>
                <input type="text" name="title" class="form-control" placeholder="مطور Full-Stack">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">نبذة عنك</label>
            <textarea name="bio" class="form-control" rows="3" placeholder="اكتب نبذة مختصرة عن نفسك وشغفك..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">المهارات</label>
            <textarea name="skills" class="form-control" rows="2" placeholder="JavaScript, PHP, React, Laravel..."></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">الخبرات</label>
            <textarea name="experience" class="form-control" rows="3" placeholder="- مطور ويب في شركة X&#10;- عامل حر لمدة سنتين"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label">المشاريع</label>
            <textarea name="projects" class="form-control" rows="4" placeholder="1. اسم المشروع - وصف مختصر&#10;2. اسم المشروع - وصف مختصر"></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">البريد</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com">
            </div>
            <div class="form-group">
                <label class="form-label">GitHub</label>
                <input type="text" name="github" class="form-control" placeholder="github.com/username">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">LinkedIn</label>
            <input type="text" name="linkedin" class="form-control" placeholder="linkedin.com/in/username">
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد البورتفوليو</button>
    </form>
</div>

<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> البورتفوليو جاهز!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('portfolio.md')"><i class="fas fa-download"></i> تحميل</button>
            <?php if (isLoggedIn()): ?>
            <button class="btn btn-primary btn-sm" onclick="saveOutputAjax(<?= $toolId ?>, 'بورتفوليو', document.getElementById('resultContent').textContent)"><i class="fas fa-save"></i> حفظ</button>
            <?php endif; ?>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
