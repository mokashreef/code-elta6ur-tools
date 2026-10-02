<?php
/**
 * أداة: حاسبة متى أستطيع شراء منزل؟ (الدفعة الأولى للعقار)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'when-can-i-buy-house-calculator';
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
        <label class="form-label" for="targetPropertyPrice">سعر العقار المستهدف (فيلا أو شقة تمليك)</label>
        <input type="number" id="targetPropertyPrice" class="form-control" value="800000" min="100000"  step="25000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="downpaymentRatioRequired">نسبة الدفعة الأولى المطلوبة للتمويل العقاري (%) - الشائع 10% إلى 15%</label>
        <input type="number" id="downpaymentRatioRequired" class="form-control" value="10" min="5" max="50" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentHomeSavings">المدخرات المتاحة حالياً للمنزل</label>
        <input type="number" id="currentHomeSavings" class="form-control" value="25000" min="0"  step="5000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyHomeSavingsRate">قدرتك على الادخار الشهري لشراء المنزل</label>
        <input type="number" id="monthlyHomeSavingsRate" class="form-control" value="3500" min="500"  step="250"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="purchaseFeesBuffer">رسوم التصرفات العقارية وأتعاب السعي (%)- عادة 5% إلى 7.5%</label>
        <input type="number" id="purchaseFeesBuffer" class="form-control" value="5" min="0" max="10" step="0.5"  oninput="calculateTool()">
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
  0 => 'الدفعة الأولى للعقار ليست المصروف النقدي الوحيد؛ يجب احتساب ضريبة التصرفات العقارية (5%) وأتعاب الوسيط العقاري (السعي 2.5%) ورسوم التقييم.',
  1 => 'استثمار مدخرات المنزل في أصول آمنة مدرة للعائد (مثل الصكوك) يسرع موعد تملك المنزل بنسبة 15% إلى 20%.',
),
        'يفترض استحقاق المشتري لبرامج الدعم السكني أو الحصول على تمويل عقاري بنكي معتمد.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الحد الأقصى للاستقطاع الشهري للقسط العقاري من الراتب؟',
    'a' => 'تحدد البنوك المركزية غالباً حداً أقصى للاستقطاع العقاري لا يتجاوز 50% إلى 65% من صافي الدخل الشهري لحماية المواطن من التعثر المالي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'savings-goal-calculator',
  1 => 'house-building-cost-calculator',
  2 => 'is-salary-enough-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const propPrice = Math.max(100000, parseFloat(document.getElementById('targetPropertyPrice').value) || 800000);
            const downRatio = Math.max(5, parseFloat(document.getElementById('downpaymentRatioRequired').value) || 10) / 100;
            const current = Math.max(0, parseFloat(document.getElementById('currentHomeSavings').value) || 25000);
            const pace = Math.max(500, parseFloat(document.getElementById('monthlyHomeSavingsRate').value) || 3500);
            const feeRatio = Math.max(0, parseFloat(document.getElementById('purchaseFeesBuffer').value) || 5) / 100;
            const curr = getSelectedCurrency();

            const downPayment = propPrice * downRatio;
            const extraFees = propPrice * feeRatio;
            const totalCashNeeded = downPayment + extraFees;
            const remainingToSave = Math.max(0, totalCashNeeded - current);
            const monthsNeeded = Math.ceil(remainingToSave / pace);
            const yearsNeeded = (monthsNeeded / 12).toFixed(1);

            const targetDate = new Date();
            targetDate.setMonth(targetDate.getMonth() + monthsNeeded);
            const dateStr = targetDate.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long' });

            setPrimaryResult(yearsNeeded + ' سنة (' + monthsNeeded + ' شهراً)', 'المدة المقدرة لتملك المنزل');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي السيولة النقدية المطلوبة', value: formatMoney(totalCashNeeded, curr), color: '#3b82f6' },
                { label: 'قيمة الدفعة الأولى للعقار (' + (downRatio*100) + '%)', value: formatMoney(downPayment, curr), color: '#10b981' },
                { label: 'الرسوم العقارية والضرائب والسعي', value: formatMoney(extraFees, curr), color: '#f59e0b' },
                { label: 'التاريخ المستهدف لامتلاك العقار', value: dateStr, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لشراء عقار بقيمة <strong>${formatMoney(propPrice, curr)}</strong>، تحتاج لتجهيز سيولة نقدية قدرها <strong>${formatMoney(totalCashNeeded, curr)}</strong> (دفعة أولى ورسوم إدارية وضريبية). بادخار <strong>${formatMoney(pace, curr)} شهرياً</strong>، ستصل لهدفك بحلول <strong>${dateStr}</strong> (خلال <strong>${yearsNeeded} سنة</strong>).</p>
            `);
        
        saveLastInputs('when-can-i-buy-house-calculator');
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
    restoreLastInputs('when-can-i-buy-house-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>