<?php
/**
 * أداة: منسق JSON
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="form-group">
        <label class="form-label">أدخل كود JSON</label>
        <textarea id="jsonInput" class="form-control" rows="8" placeholder='{"name":"أحمد","age":25,"skills":["PHP","JS"]}'></textarea>
    </div>
    <div class="d-flex gap-1">
        <button class="btn btn-primary" onclick="formatJSON()"><i class="fas fa-magic"></i> تنسيق</button>
        <button class="btn btn-ghost" onclick="minifyJSON()"><i class="fas fa-compress"></i> ضغط</button>
        <button class="btn btn-ghost" onclick="validateJSON()"><i class="fas fa-check"></i> تحقق</button>
        <button class="btn btn-ghost" onclick="document.getElementById('jsonInput').value=''"><i class="fas fa-eraser"></i> مسح</button>
    </div>
</div>

<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title" id="jsonStatus"><i class="fas fa-code"></i> النتيجة</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
        </div>
    </div>
    <div class="result-content" id="resultContent" style="direction:ltr;text-align:left"></div>
</div>

<script>
function formatJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) return;
    try {
        const parsed = JSON.parse(input);
        const formatted = JSON.stringify(parsed, null, 4);
        document.getElementById('resultContent').textContent = formatted;
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-check-circle" style="color:#10b981"></i> JSON صالح - تم التنسيق';
        document.getElementById('resultArea').classList.add('show');
    } catch (e) {
        document.getElementById('resultContent').textContent = '❌ خطأ: ' + e.message;
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-exclamation-circle" style="color:#ef4444"></i> JSON غير صالح';
        document.getElementById('resultArea').classList.add('show');
    }
}

function minifyJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) return;
    try {
        const parsed = JSON.parse(input);
        const minified = JSON.stringify(parsed);
        document.getElementById('resultContent').textContent = minified;
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-check-circle" style="color:#10b981"></i> تم الضغط';
        document.getElementById('resultArea').classList.add('show');
    } catch (e) {
        document.getElementById('resultContent').textContent = '❌ خطأ: ' + e.message;
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-exclamation-circle" style="color:#ef4444"></i> JSON غير صالح';
        document.getElementById('resultArea').classList.add('show');
    }
}

function validateJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) return;
    try {
        JSON.parse(input);
        document.getElementById('resultContent').textContent = '✅ JSON صالح!';
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-check-circle" style="color:#10b981"></i> صالح';
        document.getElementById('resultArea').classList.add('show');
    } catch (e) {
        document.getElementById('resultContent').textContent = '❌ غير صالح: ' + e.message;
        document.getElementById('jsonStatus').innerHTML = '<i class="fas fa-exclamation-circle" style="color:#ef4444"></i> غير صالح';
        document.getElementById('resultArea').classList.add('show');
    }
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
