<?php
/**
 * أداة: مولد أكواد جاهزة (Code Templates)
 */
$templates = getTemplates('code-templates');

include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>

<div class="card">
    <div class="form-group">
        <label class="form-label">اختر القالب</label>
        <select id="templateSelect" class="form-control" onchange="showTemplate()">
            <option value="">-- اختر قالب --</option>
            <?php foreach ($templates as $t): ?>
            <option value="<?= $t['id'] ?>" data-content="<?= htmlspecialchars($t['content'], ENT_QUOTES) ?>" data-name="<?= sanitize($t['name']) ?>">
                <?= sanitize($t['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title" id="templateTitle"><i class="fas fa-file-code"></i> القالب</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-ghost btn-sm" onclick="downloadAsText('template.txt')"><i class="fas fa-download"></i> تحميل</button>
        </div>
    </div>
    <div class="result-content" id="resultContent" style="direction:ltr;text-align:left"></div>
</div>

<script>
function showTemplate() {
    const select = document.getElementById('templateSelect');
    const selected = select.options[select.selectedIndex];
    if (!selected.value) { document.getElementById('resultArea').classList.remove('show'); return; }
    
    const content = selected.getAttribute('data-content');
    const name = selected.getAttribute('data-name');
    document.getElementById('resultContent').textContent = content;
    document.getElementById('templateTitle').innerHTML = '<i class="fas fa-file-code"></i> ' + name;
    document.getElementById('resultArea').classList.add('show');
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
