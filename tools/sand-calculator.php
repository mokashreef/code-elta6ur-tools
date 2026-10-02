<?php
/**
 * أداة: حاسبة كمية الرمل للبناء والخرسانة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sand-calculator';
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
        <label class="form-label" for="concreteVolume">حجم الخرسانة أو المونة المطلوب (متر مكعب)</label>
        <input type="number" id="concreteVolume" class="form-control" value="20" min="0.5"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="sandRatio">نسبة الرمل في المزيج (عادة 0.40 إلى 0.45 م³ لكل م³ خرسانة)</label>
        <input type="number" id="sandRatio" class="form-control" value="0.42" min="0.3" max="1.0" step="0.02"  oninput="calculateTool()">
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
  0 => 'الخلطة الخرسانية القياسية تتكون من: 0.8 م³ سن + 0.4 م³ رمل + 350 كجم إسمنت + ماء.',
  1 => 'كثافة الرمل الجاف تتراوح بين 1.5 إلى 1.6 طن لكل متر مكعب.',
),
        'يفترض رمل سيليكا حرش مغسول مناسب للأعمال الخرسانية الإنشائية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُشترط غسل الرمل قبل استخدامه في الخرسانة؟',
    'a' => 'للتخلص من الأملاح والطمي التي تضعف تماسك الإسمنت وتتسبب في تآكل وصدأ حديد التسليح لاحقاً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'gravel-calculator',
  1 => 'cement-calculator',
  2 => 'house-building-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const volume = Math.max(0.5, parseFloat(document.getElementById('concreteVolume').value) || 20);
            const ratio = Math.max(0.3, parseFloat(document.getElementById('sandRatio').value) || 0.42);

            const sandM3 = volume * ratio;
            // كثافة الرمل المتوسطة حوالي 1.5 إلى 1.6 طن / م³
            const sandTons = sandM3 * 1.55;
            const trucksCount = Math.ceil(sandM3 / 16); // لوري سعة 16 م³

            setPrimaryResult(sandM3.toFixed(1) + ' م³ رمل (' + sandTons.toFixed(1) + ' طن)', 'كمية الرمل المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'حجم الرمل بالمتر المكعب', value: sandM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن التقريبي بالطن', value: sandTons.toFixed(2) + ' طن', color: '#10b981' },
                { label: 'عدد سيارات النقل الكبيرة (تريلا 16م³)', value: trucksCount + ' سيارة', color: '#f59e0b' },
                { label: 'حجم الخرسانة الإجمالي المغطى', value: volume + ' م³', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لصب <strong>${volume} م³</strong> خرسانة، تحتاج إلى <strong>${sandM3.toFixed(1)} متر مكعب رمل</strong> نظيف خالي من الشوائب والأملاح، بوزن يعادل تقريباً <strong>${sandTons.toFixed(1)} طن</strong>.</p>
            `);
        
        saveLastInputs('sand-calculator');
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
    restoreLastInputs('sand-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>