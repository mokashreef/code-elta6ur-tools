<?php
/**
 * أداة: تحويل Base64
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="tabs">
        <button class="tab active" onclick="setMode('encode',this)">تشفير</button>
        <button class="tab" onclick="setMode('decode',this)">فك تشفير</button>
    </div>
    <div class="form-group">
        <label class="form-label" id="inputLabel">النص للتشفير</label>
        <textarea id="b64Input" class="form-control" rows="5" placeholder="أدخل النص هنا..."></textarea>
    </div>
    <button class="btn btn-primary btn-lg" onclick="convertBase64()"><i class="fas fa-exchange-alt"></i> تحويل</button>
</div>
<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> النتيجة</span>
        <div class="result-actions"><button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button></div>
    </div>
    <div class="result-content" id="resultContent" style="direction:ltr;text-align:left"></div>
</div>
<script>
let b64Mode = 'encode';
function setMode(mode, btn) {
    b64Mode = mode;
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('inputLabel').textContent = mode === 'encode' ? 'النص للتشفير' : 'النص المشفر لفك تشفيره';
}
function convertBase64() {
    const input = document.getElementById('b64Input').value;
    if (!input) return;
    try {
        let result;
        if (b64Mode === 'encode') {
            result = btoa(unescape(encodeURIComponent(input)));
        } else {
            result = decodeURIComponent(escape(atob(input)));
        }
        document.getElementById('resultContent').textContent = result;
        document.getElementById('resultArea').classList.add('show');
    } catch(e) {
        document.getElementById('resultContent').textContent = '❌ خطأ: نص غير صالح';
        document.getElementById('resultArea').classList.add('show');
    }
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
