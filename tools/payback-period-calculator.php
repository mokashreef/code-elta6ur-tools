<?php
/**
 * أداة: حاسبة فترة استرداد الاستثمار (Payback Period)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'payback-period-calculator';
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
        <label class="form-label" for="initialInvestment">رأس المال المبدئي المستثمر</label>
        <input type="number" id="initialInvestment" class="form-control" value="60000" min="1"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyCashflow">صافي التدفق النقدي الشهري المتوقع</label>
        <input type="number" id="monthlyCashflow" class="form-control" value="3500" min="1"  step="100" oninput="calculateTool()">
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
  0 => 'فترة الاسترداد (بالشهور) = رأس المال المستثمر ÷ التدفق النقدي الصافي شهرياً.',
  1 => 'فترة الاسترداد بالسنوات = فترة الاسترداد بالشهور ÷ 12.',
),
        'يفترض تدفقاً نقدياً شهرياً منتظماً وثابتاً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي فترة الاسترداد المثالية للمشاريع الصغيرة؟',
    'a' => 'تعتبر فترة الاسترداد بين سنة إلى 3 سنوات ممتازة لمعظم المشاريع الصغيرة والمتوسطة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'roi-calculator',
  1 => 'break-even-calculator',
  2 => 'online-store-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const invest = Math.max(1, parseFloat(document.getElementById('initialInvestment').value) || 1);
            const cashflow = Math.max(1, parseFloat(document.getElementById('monthlyCashflow').value) || 1);
            const curr = getSelectedCurrency();

            const paybackMonths = invest / cashflow;
            const years = Math.floor(paybackMonths / 12);
            const remainingMonths = Math.ceil(paybackMonths % 12);
            const annualCashflow = cashflow * 12;
            const annualReturnPercent = (annualCashflow / invest) * 100;

            let timeStr = '';
            if (years > 0) timeStr += years + ' سنة ';
            if (remainingMonths > 0) timeStr += 'و ' + remainingMonths + ' شهر';
            if (timeStr === '') timeStr = 'أقل من شهر';

            setPrimaryResult(timeStr + ' (' + paybackMonths.toFixed(1) + ' شهر)', 'فترة استرداد رأس المال');
            showResultArea();

            setDetailStats([
                { label: 'صافي التدفق النقدي السنوي', value: formatMoney(annualCashflow, curr), color: '#3b82f6' },
                { label: 'نسبة الاسترداد السنوي من رأس المال', value: annualReturnPercent.toFixed(1) + '%', color: '#10b981' },
                { label: 'المبلغ المستثمر بالكامل', value: formatMoney(invest, curr), color: '#6b7280' },
                { label: 'عدد الشهور الإجمالي', value: paybackMonths.toFixed(1) + ' شهر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>ستتمكن من استعادة رأس مالك البالغ <strong>${formatMoney(invest, curr)}</strong> بالكامل خلال <strong>${timeStr}</strong>، وبعد هذه النقطة تصبح التدفقات النقدية أرباحاً صافية متراكمة.</p>
            `);
        
        saveLastInputs('payback-period-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('payback-period-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>