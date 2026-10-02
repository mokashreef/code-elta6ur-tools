<?php
/**
 * أداة: توليد سلاسل نصية عشوائية للأكواد و API Keys
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'random-string-generator';
$tool = getToolBySlug($slug);
if (!$tool) {
    redirect('index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container tool-container">
    <?php renderToolHeader($tool); ?>

    <div class="tool-content-grid">
        <!-- قسم إدخال البيانات -->
        <div class="tool-card card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h text-accent"></i> أدخل البيانات المطلوبة</h3>
            </div>
            <div class="card-body">
    <div class="form-group">
        <label class="form-label" for="strLengthInput">طول السلسلة النصية (عدد الأحرف)</label>
        <input type="number" id="strLengthInput" class="form-control" value="32" min="4" max="256" step="4"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="strCountInput">عدد السلاسل المطلوب توليدها</label>
        <input type="number" id="strCountInput" class="form-control" value="3" min="1" max="50" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="strCharsetOption">مجموعة الأحرف والرموز المستخدمة</label>
        <select id="strCharsetOption" class="form-control" onchange="calculateTool()">
            <option value="alphanumeric" selected>أحرف وأرقام فقط (A-Z, a-z, 0-9) - مناسب للـ API Keys</option>
            <option value="hex" >أحرف ست عشرية (Hex: 0-9, a-f)</option>
            <option value="all_symbols" >شاملة الرموز الخاصة (!@#$%) لأقصى درجات الأمان</option>
        </select>
    </div>

                <div class="tool-actions-bar" style="display:flex;gap:0.75rem;margin-top:1.5rem;flex-wrap:wrap">
                    <button type="button" class="btn btn-primary btn-lg" style="flex:1" onclick="calculateTool()">
                        <i class="fas fa-calculator"></i> احسب الآن
                    </button>
                    <button type="button" class="btn btn-ghost" onclick="resetToolInputs()">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </button>
                </div>
            </div>
        </div>

        <!-- قسم عرض النتيجة -->
        <div class="tool-result-wrapper">
            <?php renderResultArea('ملخص الحساب والنتائج', ['copy' => true, 'share' => true, 'download' => true, 'print' => true]); ?>
        </div>
    </div>

    <!-- قسم الشرح والمعادلات -->
    <?php 
    renderToolExplanation(
        'طريقة الحساب والمعادلات المستخدمة',
        array (
  0 => 'يعتمد التوليد على دوال التشفير العشوائي الآمنة في المتصفح (CSPRNG - crypto.getRandomValues).',
  1 => 'مناسب لتوليد مفاتيح التوثيق السرية JWT Secrets، ورموز الجلسات Session Tokens، والـ API Keys.',
),
        'عشوائية مشفرة غير قابلة للتنبؤ رياضياً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل هذه السلاسل صالحة للاستخدام كرموز سرية في السيرفرات؟',
    'a' => 'نعم؛ بفضل استخدام crypto.getRandomValues تكون السلاسل آمنة تشفيرياً وصالحة لإنتاج مفاتيح بيئات الإنتاج الحساسة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'password-generator',
  1 => 'uuid-generator',
  2 => 'hash-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(4, Math.min(256, parseInt(document.getElementById('strLengthInput').value) || 32));
            const count = Math.max(1, Math.min(50, parseInt(document.getElementById('strCountInput').value) || 3));
            const charsetOpt = document.getElementById('strCharsetOption').value;

            let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            if (charsetOpt === 'hex') chars = '0123456789abcdef';
            if (charsetOpt === 'all_symbols') chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';

            const results = [];
            for (let i = 0; i < count; i++) {
                const arr = new Uint32Array(len);
                crypto.getRandomValues(arr);
                let str = '';
                for (let j = 0; j < len; j++) {
                    str += chars[arr[j] % chars.length];
                }
                results.push(str);
            }

            const output = results.join('\n');

            setPrimaryResult('تم توليد ' + count + ' سلسلة عشوائية بطول ' + len + ' حرف', 'السلاسل الناتجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد السلاسل المولدة', value: count + ' مفاتيح', color: '#10b981' },
                { label: 'طول المفتاح الواحد', value: len + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">السلاسل النصية المولدة (سطر لكل مفتاح):</label>
                    <textarea class="form-control" rows="5" style="font-family:monospace;direction:ltr" readonly>${output}</textarea>
                </div>
            `);
        
        saveLastInputs('random-string-generator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input, .tool-card textarea').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('random-string-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>