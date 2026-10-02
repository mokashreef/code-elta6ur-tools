<?php
/**
 * أداة: التحقق من صحة JSON واكتشاف الأخطاء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'json-validator';
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
        <label class="form-label" for="jsonToValidateInput">أدخل كود الـ JSON للتحقق منه</label>
        <textarea id="jsonToValidateInput" class="form-control" rows="8" placeholder="ضع كود JSON هنا..." oninput="calculateTool()">{
  "status": "success",
  "code": 200,
  "message": "البيانات صحيحة",
  "items": [1, 2, 3]
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
  0 => 'يفحص الكود وفق معيار RFC 8259 الصارم للـ JSON.',
  1 => 'يكتشف الأخطاء الشائعة مثل: الفواصل الزائدة الختامية (Trailing Commas)، وعلامات الاقتباس الأحادية المفردة، والأقواس غير المغلقة.',
),
        'مفاتيح الكائنات في JSON يجب أن تكون محاطة باقتباس مزدوج "key".'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يقبل JSON علامات الاقتباس المفردة \' \'؟',
    'a' => 'لا؛ المعيار القياسي لـ JSON يفرض استخدام الاقتباس المزدوج \\" \\" حصراً لكل من المفاتيح والنصوص، واستخدام الاقتباس المفرد يعتبر خطأ نحوياً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'json-formatter',
  1 => 'json-minifier',
  2 => 'text-to-json-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('jsonToValidateInput').value.trim();
            if (!text) {
                setPrimaryResult('أدخل كود JSON أولاً', 'الحالة');
                showResultArea();
                return;
            }

            try {
                const parsed = JSON.parse(text);
                const keysCount = typeof parsed === 'object' && parsed !== null ? (Array.isArray(parsed) ? parsed.length : Object.keys(parsed).length) : 1;
                
                setPrimaryResult('كود JSON صالح وسليم بنسبة 100% ✅ (Valid JSON)', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'سليم وخالٍ من الأخطاء ✅', color: '#10b981' },
                    { label: 'النوع الجذري للكائن', value: Array.isArray(parsed) ? 'مصفوفة (Array)' : typeof parsed, color: '#3b82f6' },
                    { label: 'عدد العناصر / المفاتيح في المستوى الأول', value: keysCount + ' عنصر', color: '#f59e0b' },
                    { label: 'حجم النص بالبايت', value: text.length + ' بايت', color: '#8b5cf6' }
                ]);

                setResultContent(`
                    <div class="alert alert-success" style="margin-top:1rem">
                        <i class="fas fa-check-circle"></i> كود JSON متوافق تماماً مع المعيار الدولي RFC 8259، وجاهز للاستخدام في الـ APIs وقواعد البيانات.
                    </div>
                `);
            } catch (e) {
                setPrimaryResult('كود JSON غير صالح ❌ (Invalid JSON)', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'غير صالح ويحتوي على خطأ ❌', color: '#ef4444' },
                    { label: 'نوع الخطأ المكتشف', value: e.name, color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class="alert alert-danger" style="margin-top:1rem;background:rgba(239,68,68,0.1);border:1px solid #ef4444;color:#ef4444">
                        <strong>تفاصيل الخطأ:</strong> ${e.message}
                    </div>
                `);
            }
        
        saveLastInputs('json-validator');
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
    restoreLastInputs('json-validator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>