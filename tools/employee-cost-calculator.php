<?php
/**
 * أداة: حاسبة تكلفة الموظف الحقيقية على الشركة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'employee-cost-calculator';
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
        <label class="form-label" for="baseSalary">الراتب الأساسي للموظف</label>
        <input type="number" id="baseSalary" class="form-control" value="7000" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="housingAllow">بدل السكن الشهري</label>
        <input type="number" id="housingAllow" class="form-control" value="1500" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="transAllow">بدل المواصلات الشهري</label>
        <input type="number" id="transAllow" class="form-control" value="500" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="employerGosi">حصة الشركة في التأمينات الاجتماعية (%)</label>
        <input type="number" id="employerGosi" class="form-control" value="11.75" min="0" max="50" step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="healthInsurance">التأمين الطبي السنوي للموظف</label>
        <input type="number" id="healthInsurance" class="form-control" value="3000" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="workPermitFees">رسوم حكومية ورخص عمل سنوية (إن وجدت)</label>
        <input type="number" id="workPermitFees" class="form-control" value="0" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="equipmentCosts">تكاليف المعدات والبرامج والمكتب شهرياً</label>
        <input type="number" id="equipmentCosts" class="form-control" value="400" min="0"  step="50" oninput="calculateTool()">
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
  0 => 'الراتب المباشر = الراتب الأساسي + البدلات النقدية.',
  1 => 'حصة الشركة في التأمينات تحسب غالباً على الأساسي + بدل السكن.',
  2 => 'مخصص نهاية الخدمة والإجازة السنوية يتم حسابهما كالتزامات شهرية مستحقة على الشركة.',
  3 => 'تضاف التكاليف غير المباشرة: الرعاية الصحية، رخص العمل، والأجهزة والمساحة المكتبية.',
),
        'مكافأة نهاية الخدمة محتسبة على أساس نصف راتب شهري لكل سنة من السنوات الخمس الأولى، مع شهر إجازة سنوية مدفوعة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تزيد تكلفة الموظف على راتبه بنسبة 25% إلى 50%؟',
    'a' => 'لأن صاحب العمل يتحمل قانونياً اشتراكات التأمينات، التأمين الصحي، مخصصات مكافأة نهاية الخدمة، أيام الإجازات المدفوعة، بالإضافة إلى تراخيص العمل وتجهيزات بيئة العمل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'employee-hourly-cost-calculator',
  1 => 'net-salary-calculator',
  2 => 'freelancer-hourly-rate-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const base = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const housing = Math.max(0, parseFloat(document.getElementById('housingAllow').value) || 0);
            const trans = Math.max(0, parseFloat(document.getElementById('transAllow').value) || 0);
            const gosiRate = Math.max(0, parseFloat(document.getElementById('employerGosi').value) || 0) / 100;
            const healthAnnual = Math.max(0, parseFloat(document.getElementById('healthInsurance').value) || 0);
            const feesAnnual = Math.max(0, parseFloat(document.getElementById('workPermitFees').value) || 0);
            const officeMonthly = Math.max(0, parseFloat(document.getElementById('equipmentCosts').value) || 0);
            const curr = getSelectedCurrency();

            const directSalary = base + housing + trans;
            const monthlyGosi = (base + housing) * gosiRate;
            const monthlyHealth = healthAnnual / 12;
            const monthlyFees = feesAnnual / 12;
            const eosProvision = (base + housing) / 24; // مخصص نهاية خدمة تقديري نصف شهر سنوياً
            const annualLeaveProvision = directSalary / 12; // مخصص شهر إجازة سنوية

            const totalMonthlyCost = directSalary + monthlyGosi + monthlyHealth + monthlyFees + officeMonthly + eosProvision + annualLeaveProvision;
            const totalAnnualCost = totalMonthlyCost * 12;
            const costMultiplier = directSalary > 0 ? (totalMonthlyCost / directSalary) : 1;

            setPrimaryResult(formatMoney(totalMonthlyCost, curr), 'التكلفة الحقيقية للموظف شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الراتب المباشر للموظف', value: formatMoney(directSalary, curr), color: '#3b82f6' },
                { label: 'التكلفة الإجمالية سنوياً', value: formatMoney(totalAnnualCost, curr), color: '#10b981' },
                { label: 'مضاعف التكلفة مقارنة بالراتب', value: costMultiplier.toFixed(2) + 'x', color: '#f59e0b' },
                { label: 'مخصصات سنوية وإجازات ونهاية خدمة', value: formatMoney((eosProvision + annualLeaveProvision) * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلف الموظف الشركة فعلياً <strong>${formatMoney(totalMonthlyCost, curr)}</strong> شهرياً، أي ما يعادل <strong>${costMultiplier.toFixed(2)} ضعف</strong> راتبه الإجمالي المعلن، بسبب التأمينات ومخصصات الإجازات ومكافأة نهاية الخدمة والتأمين الطبي.</p>
            `);
        
        saveLastInputs('employee-cost-calculator');
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
    restoreLastInputs('employee-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>