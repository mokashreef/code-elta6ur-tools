<?php
/**
 * أداة: تنسيق النص
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="form-group">
        <label class="form-label">أدخل النص</label>
        <textarea id="tfInput" class="form-control" rows="5" placeholder="أدخل النص لتنسيقه..."></textarea>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
        <button class="btn btn-primary btn-sm" onclick="transformText('upper')"><i class="fas fa-arrow-up"></i> أحرف كبيرة</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('lower')"><i class="fas fa-arrow-down"></i> أحرف صغيرة</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('title')"><i class="fas fa-heading"></i> عناوين</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('sentence')"><i class="fas fa-paragraph"></i> جمل</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('reverse')"><i class="fas fa-exchange-alt"></i> عكس</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('remove_spaces')"><i class="fas fa-compress"></i> إزالة مسافات زائدة</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('remove_lines')"><i class="fas fa-align-justify"></i> إزالة أسطر فارغة</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('camelCase')">camelCase</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('snake_case')">snake_case</button>
        <button class="btn btn-primary btn-sm" onclick="transformText('kebab-case')">kebab-case</button>
    </div>
</div>
<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> النتيجة</span>
        <div class="result-actions"><button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button></div>
    </div>
    <div class="result-content" id="resultContent"></div>
</div>
<script>
function transformText(type) {
    const text = document.getElementById('tfInput').value;
    if (!text) return;
    let result = text;
    switch(type) {
        case 'upper': result = text.toUpperCase(); break;
        case 'lower': result = text.toLowerCase(); break;
        case 'title': result = text.replace(/\w\S*/g, t => t.charAt(0).toUpperCase() + t.substr(1).toLowerCase()); break;
        case 'sentence': result = text.toLowerCase().replace(/(^\s*\w|[.!?]\s*\w)/g, c => c.toUpperCase()); break;
        case 'reverse': result = text.split('').reverse().join(''); break;
        case 'remove_spaces': result = text.replace(/  +/g, ' ').trim(); break;
        case 'remove_lines': result = text.replace(/\n\s*\n/g, '\n').trim(); break;
        case 'camelCase': result = text.toLowerCase().replace(/[^a-zA-Z0-9]+(.)/g, (m, c) => c.toUpperCase()); break;
        case 'snake_case': result = text.toLowerCase().replace(/\s+/g, '_').replace(/[^a-zA-Z0-9_]/g, ''); break;
        case 'kebab-case': result = text.toLowerCase().replace(/\s+/g, '-').replace(/[^a-zA-Z0-9-]/g, ''); break;
    }
    document.getElementById('resultContent').textContent = result;
    document.getElementById('resultArea').classList.add('show');
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
