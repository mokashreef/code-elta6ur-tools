<?php
/**
 * أداة: منسق ومحقق JSON
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card">
    <div class="form-group">
        <div class="d-flex justify-between align-center mb-1 flex-wrap gap-1">
            <label class="form-label mb-0"><i class="fas fa-code text-accent"></i> أدخل كود أو بيانات JSON</label>
            <div class="d-flex gap-1 align-center">
                <button type="button" class="btn btn-ghost btn-sm" onclick="loadSampleJSON()"><i class="fas fa-magic"></i> نموذج تجريبي</button>
                <button type="button" class="btn btn-ghost btn-sm" onclick="clearJSON()"><i class="fas fa-eraser"></i> مسح</button>
            </div>
        </div>
        <textarea id="jsonInput" class="form-control" rows="10" placeholder='{"name": "محمد", "role": "مطور", "skills": ["PHP", "JavaScript"]}' style="font-family:monospace;direction:ltr;text-align:left" oninput="liveValidate()"></textarea>
    </div>

    <!-- شريط الإجراءات والخيارات -->
    <div class="d-flex justify-between align-center flex-wrap gap-1 mb-2">
        <div class="d-flex gap-1 flex-wrap">
            <button type="button" class="btn btn-primary" onclick="formatJSON(4)">
                <i class="fas fa-align-left"></i> تنسيق (4 مسافات)
            </button>
            <button type="button" class="btn btn-primary" onclick="formatJSON(2)">
                <i class="fas fa-indent"></i> تنسيق (2 مسافة)
            </button>
            <button type="button" class="btn btn-ghost" onclick="minifyJSON()">
                <i class="fas fa-compress-arrows-alt"></i> ضغط وتقليص (Minify)
            </button>
            <button type="button" class="btn btn-ghost" onclick="validateJSONOnly()">
                <i class="fas fa-check-circle"></i> التحقق من الصحة
            </button>
        </div>
        <div id="jsonLiveStatus" style="font-size:0.85rem;font-weight:600;color:var(--text-muted)">
            جاهز
        </div>
    </div>
</div>

<div class="result-area" id="resultArea" style="display:none">
    <div class="result-header">
        <span class="result-title" id="jsonStatusTitle">
            <i class="fas fa-check-circle text-success"></i> تم التنسيق بنجاح
        </span>
        <div class="result-actions">
            <button type="button" class="btn btn-ghost btn-sm" onclick="copyJSONResult(this)"><i class="fas fa-copy"></i> نسخ</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="downloadJSONFile()"><i class="fas fa-download"></i> تحميل .json</button>
        </div>
    </div>
    
    <div class="result-content" id="resultContent" style="direction:ltr;text-align:left;font-family:monospace;white-space:pre-wrap;max-height:500px;overflow-y:auto"></div>
</div>

<?php 
renderToolExplanation('ما هو تنسيق JSON وكيف تستفيد من هذه الأداة؟', [
    'تنسيق JSON (JavaScript Object Notation) هو المعيار العالمي لنقل وتبادل البيانات بين التطبيقات والخوادم.',
    'التجميل (Beautify): يرتب المفاتيح والقيم بمسافات بادئة واضحة تسهل قراءتها وتتبع أخطائها.',
    'الضغط (Minify): يحذف كافة المسافات والأسطر الفارغة لتقليل حجم الحزمة وتسريع زمن استجابة الـ API.'
], 'تتم جميع العمليات محلياً على جهازك في المتصفح بسرعة فائقة وبأعلى معايير الخصوصية والأمان.');

renderToolFAQ([
    ['q' => 'ما هي أكثر الأخطاء شيوعاً في كتابة JSON؟', 'a' => 'الفواصل الزائدة في نهاية المصفوفات أو الكائنات (Trailing Commas)، استخدام علامات اقتباس فردية بدلاً من المزدوجة، أو نسيان إغلاق الأقواس المعقوفة.'],
    ['q' => 'هل تدعم الأداة اللغة العربية والـ Unicode؟', 'a' => 'نعم، الأداة تدعم النصوص العربية واليونيكود بشكل طبيعي تماماً وبدون تشفير غير ضروري للحروف العربية.']
]);

renderRelatedTools($tool['related'] ?? ['json-validator', 'json-minifier', 'text-to-json-converter']);
renderToolScriptHelpers();
?>

<script>
function formatJSON(indent = 4) {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) {
        showToast('يرجى إدخال كود JSON أولاً', 'error');
        return;
    }

    try {
        const parsed = JSON.parse(input);
        const formatted = JSON.stringify(parsed, null, indent);
        
        displaySuccess('JSON صالح - تم التنسيق بنجاح (' + indent + ' مسافات)', formatted);
    } catch (e) {
        displayError(e);
    }
}

function minifyJSON() {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) return;

    try {
        const parsed = JSON.parse(input);
        const minified = JSON.stringify(parsed);
        displaySuccess('JSON صالح - تم الضغط وإزالة المسافات', minified);
    } catch (e) {
        displayError(e);
    }
}

function validateJSONOnly() {
    const input = document.getElementById('jsonInput').value.trim();
    if (!input) return;

    try {
        JSON.parse(input);
        displaySuccess('كود JSON صالح 100% وخالي من الأخطاء النحوية!', input);
        showToast('كود JSON صالح ومطابق للمعايير!');
    } catch (e) {
        displayError(e);
    }
}

function liveValidate() {
    const input = document.getElementById('jsonInput').value.trim();
    const statusEl = document.getElementById('jsonLiveStatus');
    if (!input) {
        statusEl.textContent = 'جاهز';
        statusEl.style.color = 'var(--text-muted)';
        return;
    }

    try {
        JSON.parse(input);
        statusEl.innerHTML = '<i class="fas fa-check-circle" style="color:#10b981"></i> JSON صالح';
        statusEl.style.color = '#10b981';
    } catch(e) {
        statusEl.innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444"></i> خطأ في الصياغة';
        statusEl.style.color = '#ef4444';
    }
}

function displaySuccess(title, content) {
    const area = document.getElementById('resultArea');
    const titleEl = document.getElementById('jsonStatusTitle');
    const contentEl = document.getElementById('resultContent');

    titleEl.innerHTML = `<i class="fas fa-check-circle" style="color:#10b981"></i> ${escapeHtml(title)}`;
    contentEl.textContent = content;
    contentEl.style.color = 'var(--text-primary)';
    
    area.style.display = 'block';
    area.classList.add('show');
    area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function displayError(err) {
    const area = document.getElementById('resultArea');
    const titleEl = document.getElementById('jsonStatusTitle');
    const contentEl = document.getElementById('resultContent');

    titleEl.innerHTML = `<i class="fas fa-exclamation-triangle" style="color:#ef4444"></i> خطأ في بنية كود JSON`;
    contentEl.textContent = `❌ ${err.message}\n\nنصيحة: تأكد من:\n1. إغلاق جميع الأقواس {} و [].\n2. استخدام علامات اقتباس مزدوجة "" لجميع المفاتيح والنصوص.\n3. عدم ترك فاصلة (comma) بعد العنصر الأخير.`;
    contentEl.style.color = '#ef4444';

    area.style.display = 'block';
    area.classList.add('show');
    area.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    showToast('كود JSON غير صالح: ' + err.message, 'error');
}

function loadSampleJSON() {
    const sample = {
        "platform": "Code Elta6ur Tools",
        "description": "منصة أدوات عملية شاملة",
        "features": [
            "200+ أداة",
            "سريع ومتجاوب",
            "دعم كامل للغة العربية"
        ],
        "active": true,
        "version": 2.0
    };
    document.getElementById('jsonInput').value = JSON.stringify(sample, null, 4);
    liveValidate();
    formatJSON(4);
}

function clearJSON() {
    document.getElementById('jsonInput').value = '';
    document.getElementById('resultArea').style.display = 'none';
    liveValidate();
}

function copyJSONResult(btn) {
    copyToClipboard(document.getElementById('resultContent').textContent, btn);
}

function downloadJSONFile() {
    const content = document.getElementById('resultContent').textContent;
    const blob = new Blob([content], { type: 'application/json;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'data.json';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تحميل ملف JSON بنجاح!');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
