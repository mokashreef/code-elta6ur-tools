<?php
/**
 * أداة: حاسبة تكلفة الاستقلال عن الأهل وبدء السكن المنفرد
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'independence-cost-calculator';
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
        <label class="form-label" for="monthlyRentExpected">الإيجار الشهري المتوقع للشقة أو الاستديو</label>
        <input type="number" id="monthlyRentExpected" class="form-control" value="1600" min="300"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="depositMonths">تأمين وعربون الإيجار (المعتاد شهر إلى شهرين)</label>
        <input type="number" id="depositMonths" class="form-control" value="1600" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="basicSetupFurnish">الأثاث والأجهزة الأساسية للبداية (سرير، ثلاجة، غسالة، مكتب)</label>
        <input type="number" id="basicSetupFurnish" class="form-control" value="5000" min="1000"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyBillsAndFood">مصاريف المعيشة والفواتير التقديرية شهرياً</label>
        <input type="number" id="monthlyBillsAndFood" class="form-control" value="1500" min="500"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="emergencyFundMonths">صندوق طوارئ موصى به قبل الاستقلال (عدد الأشهر)</label>
        <input type="number" id="emergencyFundMonths" class="form-control" value="3" min="1" max="6" step="1"  oninput="calculateTool()">
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
  0 => 'الاستقلال السكني الناجح يتطلب رأس مال انطلاق أولي (Upfront Costs) + دخلاً شهرياً مستقراً يغطي المصاريف المستمرة.',
  1 => 'تأسيس صندوق طوارئ يغطي 3 أشهر على الأقل من الإيجار والمعيشة يحميك من الإفلاس أو العودة الإجبارية عند حدوث أي ظرف طارئ في العمل.',
),
        'يفترض شقة غير مفروشة تتطلب تجهيزات أساسية عملية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الشقة المفروشة أو السكن المشترك خيار أفضل في البداية؟',
    'a' => 'نعم؛ السكن المشترك مع زملاء أو استئجار استوديو مفروش بالكامل يقلل تكاليف التأسيس المبدئية بنسبة تتجاوز 70% ويعتبر خطوة انتقالية ذكية لتجربة الاستقلال.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'is-salary-enough-calculator',
  1 => 'rent-split-calculator',
  2 => 'salary-division-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rent = Math.max(300, parseFloat(document.getElementById('monthlyRentExpected').value) || 1600);
            const deposit = Math.max(0, parseFloat(document.getElementById('depositMonths').value) || 1600);
            const setup = Math.max(1000, parseFloat(document.getElementById('basicSetupFurnish').value) || 5000);
            const living = Math.max(500, parseFloat(document.getElementById('monthlyBillsAndFood').value) || 1500);
            const emergencyMonths = Math.max(1, parseInt(document.getElementById('emergencyFundMonths').value) || 3);
            const curr = getSelectedCurrency();

            const recurringMonthlyCost = rent + living;
            const emergencyFundNeeded = recurringMonthlyCost * emergencyMonths;
            const upfrontStartCapital = deposit + setup + emergencyFundNeeded + rent; // تكاليف الانطلاق

            setPrimaryResult(formatMoney(upfrontStartCapital, curr), 'المبلغ المالي الموصى بادخاره قبل اتخاذ خطوة الاستقلال');
            showResultArea();

            setDetailStats([
                { label: 'المصاريف الشهرية المستمرة للاستقلال', value: formatMoney(recurringMonthlyCost, curr) + ' / شهرياً', color: '#ef4444' },
                { label: 'قيمة صندوق الطوارئ (' + emergencyMonths + ' أشهر)', value: formatMoney(emergencyFundNeeded, curr), color: '#10b981' },
                { label: 'أثاث وتجهيزات بداية السكن', value: formatMoney(setup, curr), color: '#3b82f6' },
                { label: 'الراتب الصافي المطلوب للاستقرار', value: formatMoney(recurringMonthlyCost * 1.4, curr) + ' / شهرياً', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لتستقل بسكن منفرد بأمان مالي تام، تحتاج لجمع <strong>${formatMoney(upfrontStartCapital, curr)}</strong> كرأس مال مبدئي (يشمل التأمين والأثاث وصندوق طوارئ ${emergencyMonths} أشهر)، وأن يكون دخلك الشهري لا يقل عن <strong>${formatMoney(recurringMonthlyCost * 1.4, curr)}</strong> لتغطية مصاريفك الشهرية البالغة <strong>${formatMoney(recurringMonthlyCost, curr)}</strong> مع هامش أمان.</p>
            `);
        
        saveLastInputs('independence-cost-calculator');
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
    restoreLastInputs('independence-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>