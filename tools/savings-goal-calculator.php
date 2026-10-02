<?php
/**
 * أداة: حاسبة الادخار للوصول إلى هدف مالي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'savings-goal-calculator';
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
        <label class="form-label" for="targetGoalAmount">المبلغ المستهدف الذي ترغب في جمعه</label>
        <input type="number" id="targetGoalAmount" class="form-control" value="30000" min="500"  step="1000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthsToReachGoal">المدة الزمنية المحددة للوصول للهدف (بالأشهر)</label>
        <input type="number" id="monthsToReachGoal" class="form-control" value="12" min="1" max="120" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="initialSavingsAvailable">المبلغ المتوفر لديك بالفعل كدفعة بداية</label>
        <input type="number" id="initialSavingsAvailable" class="form-control" value="3000" min="0"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="expectedAnnualReturn">العائد السنوي المتوقع إذا وُضعت الأموال في صندوق استثماري (%) - 0 للادخار النقدي</label>
        <input type="number" id="expectedAnnualReturn" class="form-control" value="0" min="0" max="20" step="0.5"  oninput="calculateTool()">
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
  0 => 'الادخار الشهري المطلوب = (المبلغ المستهدف - الرصيد المبدئي) ÷ عدد الشهور.',
  1 => 'تحويل الأهداف المالية الكبيرة إلى أرقام يومية صغيرة يجعل تحقيقها نفسياً أسهل وأكثر قابلية للتنفيذ.',
),
        'يفترض التزاماً ثابتاً بالإيداع الشهري دون سحب من الرصيد المتراكم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أين أضع أموال الادخار أثناء تجميع الهدف؟',
    'a' => 'إذا كان الهدف لأقل من سنة فضعه في حساب ادخاري عالي العائد أو صكوك وسندات حكومية قصيرة الأجل منخفضة المخاطر، ولا تضعه في أسهم متقلبة تجنباً للهبوط المفاجئ قبل موعد حاجتك للمال.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'salary-division-calculator',
  1 => 'when-can-i-buy-car-calculator',
  2 => 'when-can-i-buy-house-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const goal = Math.max(500, parseFloat(document.getElementById('targetGoalAmount').value) || 30000);
            const months = Math.max(1, parseInt(document.getElementById('monthsToReachGoal').value) || 12);
            const initial = Math.max(0, parseFloat(document.getElementById('initialSavingsAvailable').value) || 3000);
            const annualReturn = Math.max(0, parseFloat(document.getElementById('expectedAnnualReturn').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const netGoalToSave = Math.max(0, goal - initial);
            let monthlyContribution = netGoalToSave / months;

            // حساب أثر العائد الاستثماري المركب إن وجد
            if (annualReturn > 0) {
                const r = annualReturn / 12;
                // PMT formula: P = FV * r / ((1 + r)^n - 1)
                const futureValueOfInitial = initial * Math.pow(1 + r, months);
                const remainingNeeded = Math.max(0, goal - futureValueOfInitial);
                monthlyContribution = (remainingNeeded * r) / (Math.pow(1 + r, months) - 1);
            }

            const dailySavings = monthlyContribution / 30;

            setPrimaryResult(formatMoney(monthlyContribution, curr) + ' شهرياً', 'المبلغ المطلوب ادخاره كل شهر');
            showResultArea();

            setDetailStats([
                { label: 'الادخار المطلوب يومياً', value: formatMoney(dailySavings, curr) + ' / يوم', color: '#3b82f6' },
                { label: 'المدة الزمنية المحددة', value: months + ' شهراً (' + (months/12).toFixed(1) + ' سنة)', color: '#10b981' },
                { label: 'المبلغ المتبقي للهدف بعد الرصيد الحالي', value: formatMoney(netGoalToSave, curr), color: '#f59e0b' },
                { label: 'الرصيد الابتدائي المتوفر', value: formatMoney(initial, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للوصول إلى هدفك المالي البالغ <strong>${formatMoney(goal, curr)}</strong> خلال <strong>${months} شهراً</strong>، تحتاج لادخار <strong>${formatMoney(monthlyContribution, curr)} شهرياً</strong> (أي ما يعادل <strong>${formatMoney(dailySavings, curr)} يومياً</strong>).</p>
            `);
        
        saveLastInputs('savings-goal-calculator');
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
    restoreLastInputs('savings-goal-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>