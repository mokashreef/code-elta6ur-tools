<?php
/**
 * أداة: حاسبة تكلفة البنزين الشهرية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'monthly-gas-cost-calculator';
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
        <label class="form-label" for="dailyCommuteKm">المسافة المقطوعة يومياً (ذهاب وإياب للعمل والمشاوير بالكم)</label>
        <input type="number" id="dailyCommuteKm" class="form-control" value="45" min="1"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelConsumptionRate">معدل استهلاك سيارتك (لتر لكل 100 كم) - المتوسط 8 إلى 11 لتر</label>
        <input type="number" id="fuelConsumptionRate" class="form-control" value="9.0" min="3" max="25" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gasLiterPrice">سعر لتر الوقود (بنزين 91 أو 95 أو ديزل)</label>
        <input type="number" id="gasLiterPrice" class="form-control" value="2.18" min="0.1"  step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyWorkDays">عدد أيام القيادة في الشهر (المعتاد 30 يوماً شاملة عطلة الأسبوع)</label>
        <input type="number" id="monthlyWorkDays" class="form-control" value="30" min="1" max="31" step="1"  oninput="calculateTool()">
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
  0 => 'الاستهلاك الشهري باللتر = (المسافة الشهرية بالكم ÷ 100) × معدل استهلاك السيارة (لتر/100 كم).',
  1 => 'التكلفة الشهرية = اللترات المستهلكة شهرياً × سعر لتر البنزين.',
),
        'يفترض أسلوب قيادة متوازن واستخدام التكييف في الأجواء الحارة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أعرف استهلاك سيارتي الفعلي لكل 100 كم؟',
    'a' => 'املأ التانكي بالكامل وصفر عداد المسافات (Trip A)، وقُد حتى ينخفض التانكي ثم املأه مرة أخرى بالكامل؛ اقسم عدد اللترات المعبأة على عدد الكيلومترات المقطوعة واضرب الناتج في 100.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'fuel-by-distance-calculator',
  1 => 'monthly-car-cost-calculator',
  2 => 'car-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kmDay = Math.max(1, parseFloat(document.getElementById('dailyCommuteKm').value) || 45);
            const rate = Math.max(3, parseFloat(document.getElementById('fuelConsumptionRate').value) || 9.0);
            const price = Math.max(0.1, parseFloat(document.getElementById('gasLiterPrice').value) || 2.18);
            const days = Math.max(1, Math.min(31, parseInt(document.getElementById('monthlyWorkDays').value) || 30));
            const curr = getSelectedCurrency();

            const monthlyKm = kmDay * days;
            const litersPerKm = rate / 100;
            const monthlyLiters = monthlyKm * litersPerKm;
            const monthlyCost = monthlyLiters * price;
            const yearlyCost = monthlyCost * 12;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'فاتورة البنزين الشهرية');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المسافة المقطوعة شهرياً', value: monthlyKm.toLocaleString() + ' كم', color: '#3b82f6' },
                { label: 'كمية الوقود المستهلكة شهرياً', value: monthlyLiters.toFixed(1) + ' لتر', color: '#10b981' },
                { label: 'تكلفة الكيلومتر الواحد', value: formatMoney((monthlyCost / monthlyKm), curr) + ' / كم', color: '#f59e0b' },
                { label: 'التكلفة السنوية التقديرية', value: formatMoney(yearlyCost, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>${monthlyKm.toLocaleString()} كم شهرياً</strong> بمعدل استهلاك <strong>${rate} لتر/100 كم</strong>، تستهلك سيارتك <strong>${monthlyLiters.toFixed(1)} لتر بنزين</strong> بقيمة <strong>${formatMoney(monthlyCost, curr)}</strong> شهرياً.</p>
            `);
        
        saveLastInputs('monthly-gas-cost-calculator');
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
    restoreLastInputs('monthly-gas-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>