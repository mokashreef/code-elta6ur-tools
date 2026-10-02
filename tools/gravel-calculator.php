<?php
/**
 * أداة: حاسبة كمية الحصى والسن للخرسانة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'gravel-calculator';
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
        <label class="form-label" for="concreteVol">حجم الخرسانة المطلوب صبها (متر مكعب)</label>
        <input type="number" id="concreteVol" class="form-control" value="20" min="0.5"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gravelRatio">نسبة الحصى في المزيج (عادة 0.80 إلى 0.85 م³ لكل م³ خرسانة)</label>
        <input type="number" id="gravelRatio" class="form-control" value="0.82" min="0.6" max="1.0" step="0.02"  oninput="calculateTool()">
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
  0 => 'الركام الخشن (الحصى أو السن) يمثل الهيكل العظمي للخرسانة ويشكل حوالي 70% إلى 80% من حجمها.',
  1 => 'حجم الحصى المطلوب يعادل ضعف حجم الرمل تقريباً (نسبة 2 : 1).',
),
        'يفترض استخدام حصى متدرج مقاس 1 و 2 خالي من الأتربة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو المقاس الأفضل للسن في الأسقف والأعمدة؟',
    'a' => 'يُفضل خليط متوازن بين سن 1 وسن 2 (مقاس من 10 مم إلى 20 مم) لضمان سهولة الانسياب بين أسياخ الحديد ومنع التعشيش.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sand-calculator',
  1 => 'cement-calculator',
  2 => 'house-building-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const vol = Math.max(0.5, parseFloat(document.getElementById('concreteVol').value) || 20);
            const ratio = Math.max(0.6, parseFloat(document.getElementById('gravelRatio').value) || 0.82);

            const gravelM3 = vol * ratio;
            // كثافة السن/الحصى حوالي 1.6 إلى 1.7 طن / م³
            const gravelTons = gravelM3 * 1.65;
            const trucks = Math.ceil(gravelM3 / 16);

            setPrimaryResult(gravelM3.toFixed(1) + ' م³ سن وحصى (' + gravelTons.toFixed(1) + ' طن)', 'كمية الحصى (الركام الخشن)');
            showResultArea();

            setDetailStats([
                { label: 'حجم الحصى بالمتر المكعب', value: gravelM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن التقريبي بالطن', value: gravelTons.toFixed(2) + ' طن', color: '#10b981' },
                { label: 'عدد شاحنات النقل الكبيرة (16م³)', value: trucks + ' نقلة', color: '#f59e0b' },
                { label: 'نسبة الحصى من الخلطة', value: (ratio * 100).toFixed(0) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لخلط <strong>${vol} م³</strong> خرسانة، تحتاج إلى <strong>${gravelM3.toFixed(1)} متر مكعب من الحصى المتدرج (السن)</strong>، ما يزن حوالي <strong>${gravelTons.toFixed(1)} طن</strong>.</p>
            `);
        
        saveLastInputs('gravel-calculator');
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
    restoreLastInputs('gravel-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>