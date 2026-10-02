<?php
/**
 * أداة: حاسبة تكلفة الساعة للموظف
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'employee-hourly-cost-calculator';
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
        <label class="form-label" for="totalMonthlyCost">التكلفة الحقيقية الشهرية للموظف على الشركة</label>
        <input type="number" id="totalMonthlyCost" class="form-control" value="11500" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="workingDaysMonth">أيام العمل الفعلية في الشهر (بدون الإجازات)</label>
        <input type="number" id="workingDaysMonth" class="form-control" value="21" min="1" max="31" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyHours">ساعات العمل اليومية الرسمية</label>
        <input type="number" id="dailyHours" class="form-control" value="8" min="1" max="24" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="productiveRatio">نسبة الإنتاجية الفعلية من ساعات الدوام (%)</label>
        <input type="number" id="productiveRatio" class="form-control" value="75" min="10" max="100" step="5" oninput="calculateTool()">
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
  0 => 'ساعات الدوام الشهرية = أيام العمل الفعلية × ساعات العمل اليومية.',
  1 => 'تكلفة الساعة الاسمية = إجمالي التكلفة الشهرية للموظف ÷ ساعات الدوام الشهرية.',
  2 => 'تكلفة الساعة الإنتاجية = إجمالي التكلفة الشهرية ÷ (ساعات الدوام × نسبة الإنتاجية).',
),
        'تشير الدراسات الإدارية إلى أن الموظف المكتبي ينتج فعلياً بين 60% إلى 80% من ساعات حضوره الرسمية بسبب الاجتماعات وفترات الراحة والتواصل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف تفيد هذه الحاسبة في تسعير مشاريع الشركات؟',
    'a' => 'عند تسعير مشروع لعميل، يجب احتساب تكلفة ساعة الموظف الحقيقية المنتجة وليس فقط راتبه بالساعة، لضمان عدم تكبد الشركة خسائر خفية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'employee-cost-calculator',
  1 => 'freelancer-hourly-rate-calculator',
  2 => 'project-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const monthlyCost = Math.max(0, parseFloat(document.getElementById('totalMonthlyCost').value) || 0);
            const days = Math.max(1, parseFloat(document.getElementById('workingDaysMonth').value) || 21);
            const hours = Math.max(1, parseFloat(document.getElementById('dailyHours').value) || 8);
            const prod = Math.max(10, Math.min(100, parseFloat(document.getElementById('productiveRatio').value) || 75)) / 100;
            const curr = getSelectedCurrency();

            const totalHours = days * hours;
            const nominalHourlyCost = totalHours > 0 ? (monthlyCost / totalHours) : 0;
            const productiveHours = totalHours * prod;
            const realProductiveHourlyCost = productiveHours > 0 ? (monthlyCost / productiveHours) : 0;

            setPrimaryResult(formatMoney(realProductiveHourlyCost, curr), 'تكلفة ساعة العمل المنتجة الحقيقية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الساعة الاسمية (ساعات الدوام)', value: formatMoney(nominalHourlyCost, curr), color: '#3b82f6' },
                { label: 'ساعات العمل الاسمية شهرياً', value: totalHours + ' ساعة', color: '#10b981' },
                { label: 'ساعات الإنتاجية الصافية شهرياً', value: productiveHours.toFixed(1) + ' ساعة', color: '#f59e0b' },
                { label: 'فارق التكلفة بسبب وقت الضياع/الاستراحات', value: formatMoney(realProductiveHourlyCost - nominalHourlyCost, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>بينما تكلف ساعة الحضور الرسمية للموظف <strong>${formatMoney(nominalHourlyCost, curr)}</strong>، فإن التكلفة الفعلية لكل ساعة إنجاز حقيقية تبلغ <strong>${formatMoney(realProductiveHourlyCost, curr)}</strong> بافتراض إنتاجية ${(prod*100).toFixed(0)}%.</p>
            `);
        
        saveLastInputs('employee-hourly-cost-calculator');
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
    restoreLastInputs('employee-hourly-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>