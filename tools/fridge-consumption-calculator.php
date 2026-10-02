<?php
/**
 * أداة: حاسبة استهلاك الثلاجة للكهرباء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'fridge-consumption-calculator';
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
    <?php renderCurrencySelector('calcCurrency', 'SAR', 'العملة المفضلة للنتائج'); ?>
    <div class="form-group">
        <label class="form-label" for="fridgeSizeCategory">حجم الثلاجة</label>
        <select id="fridgeSizeCategory" class="form-control" onchange="calculateTool()">
            <option value="small" >ثلاجة ميني بار صغيرة (4 إلى 6 قدم) ~ 150 kWh سنوياً</option>
            <option value="medium" selected>ثلاجة عائلية متوسطة (12 إلى 16 قدم) ~ 350 kWh سنوياً</option>
            <option value="large" >ثلاجة كبيرة بابين / دولابي Side-by-Side (18 إلى 24 قدم) ~ 550 kWh سنوياً</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="efficiencyStars">مستوى كفاءة الطاقة للثلاجة (النجوم)</label>
        <select id="efficiencyStars" class="form-control" onchange="calculateTool()">
            <option value="inverter_top" selected>إنفرتر حديث موفر جداً (أعلى تصنيف A / 5-6 نجوم)</option>
            <option value="standard" >تصنيف متوسط عادي (3-4 نجوم)</option>
            <option value="old" >ثلاجة قديمة عادية (نجمة إلى نجمتين / موديل قديم +40% استهلاك)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="kwhCostFridge">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="kwhCostFridge" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'الثلاجة تعمل متصلة بالكهرباء 24 ساعة ولكن الضاغط (الكمبروسر) يعمل فعلياً بين 6 إلى 10 ساعات فقط يومياً حسب حرارة الجو وتكرار فتح الباب.',
  1 => 'الثلاجات بتقنية الإنفرتر توفر ما بين 30% إلى 45% من استهلاك الكهرباء مقارنة بالموديلات القديمة.',
),
        'يفترض ضبط الترموستات على درجة حرارة معتدلة (4 درجات للثلاجة و -18 للفريزر).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أقلل استهلاك الثلاجة للكهرباء؟',
    'a' => 'اترك مسافة 10 سم خلف الثلاجة لتهوية المكثف، وتأكد من سلامة الجوان المطاطي للباب لمنع تسرب البرودة، وتجنب وضع الأطعمة الساخنة مباشرة بداخلها.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'electricity-consumption-calculator',
  1 => 'ac-consumption-calculator',
  2 => 'washing-machine-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const size = document.getElementById('fridgeSizeCategory').value;
            const stars = document.getElementById('efficiencyStars').value;
            const price = Math.max(0.01, parseFloat(document.getElementById('kwhCostFridge').value) || 0.18);
            const curr = getSelectedCurrency();

            let baseAnnualKwh = 350;
            if (size === 'small') baseAnnualKwh = 150;
            if (size === 'large') baseAnnualKwh = 550;

            let mult = 0.8; // موفر انفرتر
            if (stars === 'standard') mult = 1.0;
            if (stars === 'old') mult = 1.45;

            const annualKwh = baseAnnualKwh * mult;
            const monthlyKwh = annualKwh / 12;
            const dailyKwh = annualKwh / 365;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الثلاجة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك السنوي الإجمالي', value: annualKwh.toFixed(0) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري التقديري', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'الاستهلاك اليومي المتوسط', value: dailyKwh.toFixed(2) + ' kWh', color: '#f59e0b' },
                { label: 'التكلفة السنوية الكاملة', value: formatMoney(annualKwh * price, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك هذه الثلاجة حوالي <strong>${annualKwh.toFixed(0)} كيلوواط ساعة سنوياً</strong>، وتكلفك تقريباً <strong>${formatMoney(monthlyCost, curr)} شهرياً</strong>. كمبروسر الثلاجة لا يعمل طوال الـ 24 ساعة، بل يفصل ويعمل دورياً للحفاظ على البرودة.</p>
            `);
        
        saveLastInputs('fridge-consumption-calculator');
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
    restoreLastInputs('fridge-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>