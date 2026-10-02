<?php
/**
 * أداة: تحويل وتشفير Base64
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card">
    <div class="tabs mb-2">
        <button type="button" class="tab active" id="tabEncode" onclick="setB64Mode('encode')">
            <i class="fas fa-lock"></i> تشفير إلى Base64
        </button>
        <button type="button" class="tab" id="tabDecode" onclick="setB64Mode('decode')">
            <i class="fas fa-lock-open"></i> فك تشفير Base64
        </button>
    </div>

    <div class="form-group">
        <label class="form-label" id="inputLabel">
            <i class="fas fa-align-right text-accent"></i> أدخل النص المراد تشفيره
        </label>
        <textarea id="b64Input" class="form-control" rows="6" placeholder="اكتب أو الصق النص هنا..." oninput="autoConvert()"></textarea>
    </div>

    <div class="d-flex justify-between align-center mb-2 flex-wrap gap-1">
        <div class="d-flex gap-1">
            <button type="button" class="btn btn-primary" onclick="convertBase64()">
                <i class="fas fa-exchange-alt"></i> <span id="btnConvertText">تشفير النص</span>
            </button>
            <button type="button" class="btn btn-ghost" onclick="clearB64()">
                <i class="fas fa-eraser"></i> مسح
            </button>
            <button type="button" class="btn btn-ghost" onclick="swapText()">
                <i class="fas fa-sync-alt"></i> عكس النص
            </button>
        </div>
        <div style="font-size:0.8rem;color:var(--text-muted)">
            <span id="charCount">0 حرف</span> | <span id="byteCount">0 بايت</span>
        </div>
    </div>
</div>

<?php renderResultArea('النتيجة المشفرة / المفكوكة', ['copy' => true, 'share' => true, 'download' => true]); ?>

<?php 
renderToolExplanation('ما هو ترميز Base64 وكيف يعمل؟', [
    'ترميز Base64 هو خوارزمية لتحويل البيانات الثنائية أو النصوص بمختلف اللغات إلى 64 رمزاً آمناً لنقلها عبر الشبكات والإنترنت.',
    'يدعم هذا المحول النصوص العربية والرموز التعبيرية (Emoji) باستخدام ترميز UTF-8 بدون أي تشويه.',
    'تشفير Base64 ليس تشفيراً أمنياً سرياً بل هو ترميز لنقل البيانات بدون أخطاء توافقية.'
], 'الترميز آمن ويتم بالكامل داخل متصفحك محلياً دون إرسال نصوصك لأي خادم خارجي.');

renderToolFAQ([
    ['q' => 'هل تدعم الأداة النصوص العربية والتشكيل؟', 'a' => 'نعم، تم تزويد الأداة بدعم كامل لـ UTF-8 لضمان تشفير وفك تشفير اللغة العربية والحروف الخاصة بدقة 100% دون ظهور رموز غير مفهومة.'],
    ['q' => 'هل Base64 وسيلة مناسبة لحفظ كلمات المرور؟', 'a' => 'لا، Base64 هو مجرد تمثيل للبيانات وليس تشفيراً سرياً، ويمكن لأي شخص فك تشفيره بسهولة. لكلمات المرور استخدم مولد الهاش (Hash Generator).']
]);

renderRelatedTools($tool['related'] ?? ['jwt-decoder', 'url-encode', 'hash-generator']);
renderToolScriptHelpers();
?>

<script>
let currentMode = 'encode';

function setB64Mode(mode) {
    currentMode = mode;
    document.getElementById('tabEncode').classList.toggle('active', mode === 'encode');
    document.getElementById('tabDecode').classList.toggle('active', mode === 'decode');
    document.getElementById('inputLabel').innerHTML = mode === 'encode' 
        ? '<i class="fas fa-align-right text-accent"></i> أدخل النص المراد تشفيره' 
        : '<i class="fas fa-code text-accent"></i> أدخل كود Base64 لفك تشفيره';
    document.getElementById('btnConvertText').textContent = mode === 'encode' ? 'تشفير النص' : 'فك التشفير';
    document.getElementById('resultTitleText').textContent = mode === 'encode' ? 'كود Base64 المشفر' : 'النص الأصلي المفكوك';
    convertBase64();
}

// تشفير آمن لـ UTF-8 في JavaScript الحديث
function utf8ToBase64(str) {
    const bytes = new TextEncoder().encode(str);
    const binString = Array.from(bytes, (byte) => String.fromCharCode(byte)).join('');
    return btoa(binString);
}

function base64ToUtf8(base64) {
    const binString = atob(base64.trim());
    const bytes = Uint8Array.from(binString, (m) => m.charCodeAt(0));
    return new TextDecoder().decode(bytes);
}

function convertBase64() {
    const input = document.getElementById('b64Input').value;
    updateCounts(input);
    if (!input.trim()) {
        document.getElementById('resultArea').style.display = 'none';
        return;
    }

    try {
        let output = '';
        if (currentMode === 'encode') {
            output = utf8ToBase64(input);
        } else {
            output = base64ToUtf8(input);
        }

        const len = output.length;
        const statCards = [
            { icon: 'fa-text-width', label: 'طول المخرج', value: len + ' حرف', color: 'purple' },
            { icon: 'fa-check', label: 'الحالة', value: 'ناجح 100%', color: 'green', textColor: '#10b981' }
        ];

        showToolResult(
            `<span style="word-break:break-all;font-family:monospace;font-size:1.1rem">${escapeHtml(output)}</span>`,
            currentMode === 'encode' ? 'كود Base64' : 'النص بعد فك التشفير',
            '',
            statCards
        );
    } catch (e) {
        showToolResult(
            '<span style="color:#ef4444"><i class="fas fa-exclamation-triangle"></i> خطأ: كود Base64 غير صالح أو يحتوي على رموز غير صحيحة</span>',
            'فشل التحويل'
        );
    }
}

function autoConvert() {
    const input = document.getElementById('b64Input').value;
    updateCounts(input);
    if (input.length < 5000) {
        convertBase64();
    }
}

function clearB64() {
    document.getElementById('b64Input').value = '';
    document.getElementById('resultArea').style.display = 'none';
    updateCounts('');
}

function swapText() {
    const resultBox = document.getElementById('resultPrimaryValue');
    if (resultBox && resultBox.innerText) {
        document.getElementById('b64Input').value = resultBox.innerText.trim();
        setB64Mode(currentMode === 'encode' ? 'decode' : 'encode');
    }
}

function updateCounts(text) {
    document.getElementById('charCount').textContent = text.length + ' حرف';
    document.getElementById('byteCount').textContent = new Blob([text]).size + ' بايت';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
