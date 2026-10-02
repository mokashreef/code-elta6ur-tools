<?php
/**
 * أداة: حاسبة تكلفة الطفل في السنة الأولى
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'baby-first-year-cost-calculator';
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
        <label class="form-label" for="deliveryCostHospital">تكاليف الولادة والمستشفى (بعد تغطية التأمين إن وجدت)</label>
        <input type="number" id="deliveryCostHospital" class="form-control" value="4000" min="0"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="diapersMonthlyCost">تكلفة الحفاضات والمناديل المبللة شهرياً</label>
        <input type="number" id="diapersMonthlyCost" class="form-control" value="250" min="50"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="milkFormulaMonthly">تكلفة الحليب الصناعي والمكملات (0 إن كانت رضاعة طبيعية كاملة)</label>
        <input type="number" id="milkFormulaMonthly" class="form-control" value="300" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gearCribStrollerCost">مستلزمات البداية (سرير الطفل، عربة الأطفال، مقعد السيارة، جهاز مراقبة)</label>
        <input type="number" id="gearCribStrollerCost" class="form-control" value="2500" min="500"  step="200"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="clothesDoctorMonthly">ملابس دورية (الطفل ينمو بسرعة) وزيارات طبيب أطفال وتطعيمات شهرياً</label>
        <input type="number" id="clothesDoctorMonthly" class="form-control" value="400" min="100"  step="50"  oninput="calculateTool()">
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
  0 => 'الرضاعة الطبيعية توفر على ميزانية الأسرة ما بين 3000 إلى 5000 ريال/دولار سنوياً من تكلفة الحليب الصناعي والزجاجات المعقمة.',
  1 => 'الأطفال الرضع يغيرون مقاس ملابسهم كل شهرين إلى 3 أشهر في السنة الأولى، لذلك تجنب المبالغة في شراء ملابس المواليد بكميات كبيرة.',
),
        'يفترض عدم وجود متطلبات علاجية خاصة أو عمليات جراحية غير متوقعة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف نوفر في مستلزمات المولود الأول؟',
    'a' => 'شراء عربة الأطفال والسرير من ماركات موثوقة مستعملة بحالة ممتازة يوفر أكثر من 50% من التكلفة، حيث يستخدمها الأطفال لفترات زمنية قصيرة جداً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'family-monthly-budget-calculator',
  1 => 'is-salary-enough-calculator',
  2 => 'savings-goal-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const delivery = Math.max(0, parseFloat(document.getElementById('deliveryCostHospital').value) || 4000);
            const diapers = Math.max(50, parseFloat(document.getElementById('diapersMonthlyCost').value) || 250);
            const milk = Math.max(0, parseFloat(document.getElementById('milkFormulaMonthly').value) || 300);
            const gear = Math.max(500, parseFloat(document.getElementById('gearCribStrollerCost').value) || 2500);
            const monthlyOther = Math.max(100, parseFloat(document.getElementById('clothesDoctorMonthly').value) || 400);
            const curr = getSelectedCurrency();

            const recurringMonthly = diapers + milk + monthlyOther;
            const oneTimeCosts = delivery + gear;
            const totalFirstYear = oneTimeCosts + (recurringMonthly * 12);
            const avgMonthlyEquiv = totalFirstYear / 12;

            setPrimaryResult(formatMoney(totalFirstYear, curr), 'إجمالي ميزانية الطفل للسنة الأولى كاملة');
            showResultArea();

            setDetailStats([
                { label: 'المصاريف الشهرية المستمرة للطفل', value: formatMoney(recurringMonthly, curr) + ' / شهرياً', color: '#3b82f6' },
                { label: 'تكاليف التأسيس المبدئية لمرة واحدة', value: formatMoney(oneTimeCosts, curr), color: '#10b981' },
                { label: 'إجمالي تكلفة الحفاضات والحليب سنوياً', value: formatMoney((diapers + milk) * 12, curr), color: '#f59e0b' },
                { label: 'متوسط الأثر الشهري على ميزانية الأسرة', value: formatMoney(avgMonthlyEquiv, curr) + ' / شهرياً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تصل تكلفة رعاية وتجهيز المولود الجديد في عامه الأول إلى حوالي <strong>${formatMoney(totalFirstYear, curr)}</strong>، بما يتطلب زيادة ميزانية الأسرة الشهرية بمقدار <strong>${formatMoney(recurringMonthly, curr)} شهرياً</strong> بخلاف تكاليف الولادة ومعدات السرير والعربة.</p>
            `);
        
        saveLastInputs('baby-first-year-cost-calculator');
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
    restoreLastInputs('baby-first-year-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>