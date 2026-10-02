<?php
/**
 * أداة: ضغط وتقليص حجم JSON (Minifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'json-minifier';
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
        <label class="form-label" for="jsonMinifyInput">أدخل كود الـ JSON المراد ضغطه</label>
        <textarea id="jsonMinifyInput" class="form-control" rows="8" placeholder="ضع كود JSON هنا..." oninput="calculateTool()">{
  "appName": "Code Elta6ur",
  "version": "2.0",
  "active": true,
  "features": [
    "calculators",
    "converters",
    "generators"
  ]
}</textarea>
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
  0 => 'يحذف كافة المسافات البيضاء والأسطر الفارغة وعلامات التبويب غير الضرورية.',
  1 => 'تسريع استجابة الـ API وخفض استهلاك الباندويث ونقل البيانات عبر الشبكة.',
),
        'يحافظ تماماً على سلامة البيانات والقيم النصية بداخل الكائنات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا نستخدم JSON Minifier في الإنتاج (Production)؟',
    'a' => 'لأن تقليص حجم الـ JSON يقلل من حجم حمولة الـ HTTP Request بنسبة 20% إلى 40% مما يسرع تحميل تطبيقات الويب والهواتف الذكية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'json-formatter',
  1 => 'json-validator',
  2 => 'sql-minifier',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('jsonMinifyInput').value.trim();
            try {
                const parsed = JSON.parse(text);
                const minified = JSON.stringify(parsed);
                const savedBytes = text.length - minified.length;
                const ratio = text.length > 0 ? ((savedBytes / text.length) * 100).toFixed(1) : 0;

                setPrimaryResult('تم تقليص الحجم بنسبة ' + ratio + '% (' + savedBytes + ' بايت وفر)', 'حالة الضغط');
                showResultArea();

                setDetailStats([
                    { label: 'الحجم بعد الضغط', value: minified.length + ' بايت', color: '#10b981' },
                    { label: 'الحجم الأصلي قبل الضغط', value: text.length + ' بايت', color: '#ef4444' },
                    { label: 'نسبة التوفير المئوية', value: ratio + '%', color: '#3b82f6' }
                ]);

                setResultContent(`
                    <div style="margin-top:1rem">
                        <label class="form-label">كود JSON المضغوط في سطر واحد (Minified):</label>
                        <textarea class="form-control" rows="4" style="font-family:monospace;direction:ltr" readonly>${minified}</textarea>
                    </div>
                `);
            } catch (e) {
                alert('خطأ: كود JSON غير صالح: ' + e.message);
            }
        
        saveLastInputs('json-minifier');
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
    restoreLastInputs('json-minifier');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>