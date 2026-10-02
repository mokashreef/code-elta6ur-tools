<?php
/**
 * أداة: حاسبة الراتب اليومي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'daily-wage-calculator';
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
        <label class="form-label" for="monthlySalary">الراتب الشهري</label>
        <input type="number" id="monthlySalary" class="form-control" value="6000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="workDaysType">طريقة حساب الأيام</label>
        <select id="workDaysType" class="form-control" onchange="calculateTool()">
            <option value="calendar" selected>أيام الشهر التقويمي (30 يوماً - نظام العمل)</option>
            <option value="actual22" >أيام العمل الفعلية فقط (22 يوماً - إجازة يومين أسبوعياً)</option>
            <option value="actual26" >أيام العمل الفعلية (26 يوماً - إجازة يوم واحد أسبوعياً)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyHours">ساعات العمل اليومية</label>
        <input type="number" id="dailyHours" class="form-control" value="8" min="1" max="24" step="1" oninput="calculateTool()">
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
  0 => 'في معظم قوانين العمل (مثل قانون العمل السعودي والمصري)، يُقسم الراتب الشهري على 30 يوماً لحساب أجر اليوم لأغراض الخصومات والإجازات.',
  1 => 'إذا كنت تعمل بنظام اليومية أو الفريلانس يُفضل القسمة على أيام العمل الفعلية (22 أو 26 يوماً).',
),
        'القسمة على 30 هي المعيار الرسمي لمعظم عقود العمل المدفوعة شهرياً شاملة العطلات الأسبوعية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحسب يوم الغياب بقسمة الراتب على 30 أم على 22؟',
    'a' => 'في أنظمة العمل للشركات الرسمية يتم حساب قيمة اليوم بقسمة الراتب على 30 يوماً لأن الإجازات الأسبوعية مدفوعة الأجر ضمن الراتب الشهري.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'hourly-wage-calculator',
  1 => 'net-salary-calculator',
  2 => 'overtime-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const daysType = document.getElementById('workDaysType').value;
            const hours = Math.max(1, parseFloat(document.getElementById('dailyHours').value) || 8);
            const curr = getSelectedCurrency();

            let divisor = 30;
            if (daysType === 'actual22') divisor = 22;
            if (daysType === 'actual26') divisor = 26;

            const dailyWage = salary / divisor;
            const hourlyWage = dailyWage / hours;

            setPrimaryResult(formatMoney(dailyWage, curr), 'الأجر اليومي');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأيام المعتمدة بالحساب', value: divisor + ' يوم', color: '#3b82f6' },
                { label: 'أجر ساعة العمل', value: formatMoney(hourlyWage, curr), color: '#10b981' },
                { label: 'تكلفة خصم غياب يوم', value: formatMoney(dailyWage, curr), color: '#ef4444' },
                { label: 'قيمة نصف يوم عمل', value: formatMoney(dailyWage / 2, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الأجر اليومي المستحق هو <strong>${formatMoney(dailyWage, curr)}</strong>، وكل ساعة عمل تعادل <strong>${formatMoney(hourlyWage, curr)}</strong>.</p>
            `);
        
        saveLastInputs('daily-wage-calculator');
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
    restoreLastInputs('daily-wage-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>