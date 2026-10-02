<?php
/**
 * أداة: حاسبة سعر الساعة للفريلانسر
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'freelancer-hourly-rate-calculator';
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
        <label class="form-label" for="targetAnnualIncome">الدخل السنوي الصافي المستهدف</label>
        <input type="number" id="targetAnnualIncome" class="form-control" value="60000" min="1000"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="annualBusinessCosts">المصاريف السنوية (أجهزة، برامج، إنترنت، ضرائب)</label>
        <input type="number" id="annualBusinessCosts" class="form-control" value="8000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="vacationWeeks">أسابيع الإجازات السنوية والمرضية غير المدفوعة</label>
        <input type="number" id="vacationWeeks" class="form-control" value="4" min="0" max="20" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="billableHoursPerWeek">ساعات العمل المدفوعة أسبوعياً (غير شاملة التسويق)</label>
        <input type="number" id="billableHoursPerWeek" class="form-control" value="25" min="5" max="60" step="1" oninput="calculateTool()">
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
  0 => 'أسابيع العمل الفعلية = 52 - أسابيع الإجازات.',
  1 => 'الساعات القابلة للفوترة سنوياً = أسابيع العمل × الساعات المدفوعة أسبوعياً.',
  2 => 'سعر الساعة الأدنى = (الدخل المطلوب + المصاريف السنوية) ÷ إجمالي الساعات القابلة للفوترة.',
),
        'الفريلانسر لا يقضي 40 ساعة أسبوعياً في العمل المدفوع؛ فهناك ما بين 30% إلى 40% من وقته يضيع في التفاوض والمراسلات والتسويق غير المدفوع.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُنصح باحتساب 25 ساعة فقط أسبوعياً للفوترة؟',
    'a' => 'لأن المستقل يقضي ساعات إضافية في إدارة الفواتير، التواصل مع العملاء، وتحديث مهاراته، وهذه الساعات غير مدفوعة ويجب تحميل قيمتها على ساعات العمل الفعلي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'freelancer-project-price-calculator',
  1 => 'hourly-wage-calculator',
  2 => 'project-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const income = Math.max(1000, parseFloat(document.getElementById('targetAnnualIncome').value) || 1000);
            const costs = Math.max(0, parseFloat(document.getElementById('annualBusinessCosts').value) || 0);
            const vacation = Math.max(0, Math.min(20, parseFloat(document.getElementById('vacationWeeks').value) || 4));
            const billableHours = Math.max(5, parseFloat(document.getElementById('billableHoursPerWeek').value) || 25);
            const curr = getSelectedCurrency();

            const workingWeeks = 52 - vacation;
            const annualBillableHours = workingWeeks * billableHours;
            const totalRequiredRevenue = income + costs;
            const minHourlyRate = totalRequiredRevenue / annualBillableHours;
            const recommendedHourlyRate = minHourlyRate * 1.25; // مع هامش أمان 25%

            setPrimaryResult(formatMoney(recommendedHourlyRate, curr), 'سعر الساعة الموصى به (مع هامش أمان)');
            showResultArea();

            setDetailStats([
                { label: 'الحد الأدنى لسعر الساعة', value: formatMoney(minHourlyRate, curr), color: '#f59e0b' },
                { label: 'إجمالي الساعات القابلة للفوترة سنوياً', value: annualBillableHours + ' ساعة', color: '#3b82f6' },
                { label: 'إجمالي الدخل الإجمالي المطلوب سنوياً', value: formatMoney(totalRequiredRevenue, curr), color: '#10b981' },
                { label: 'أسابيع العمل الفعلية', value: workingWeeks + ' أسبوع', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتحقيق صافي دخل سنوي <strong>${formatMoney(income, curr)}</strong> مع تغطية مصاريفك وإجازاتك، يجب ألا يقل سعر ساعتك عن <strong>${formatMoney(minHourlyRate, curr)}</strong>، ويُوصى بطلب <strong>${formatMoney(recommendedHourlyRate, curr)}</strong> لكل ساعة عمل.</p>
            `);
        
        saveLastInputs('freelancer-hourly-rate-calculator');
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
    restoreLastInputs('freelancer-hourly-rate-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>