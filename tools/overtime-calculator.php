<?php
/**
 * أداة: حاسبة العمل الإضافي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'overtime-calculator';
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
        <label class="form-label" for="monthlySalary">الراتب الإجمالي المعتمد للحساب</label>
        <input type="number" id="monthlySalary" class="form-control" value="6000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyStandardHours">ساعات العمل القياسية شهرياً</label>
        <input type="number" id="monthlyStandardHours" class="form-control" value="240" min="100" max="300" step="8" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="regularOvertimeHours">ساعات العمل الإضافي في الأيام العادية</label>
        <input type="number" id="regularOvertimeHours" class="form-control" value="15" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="holidayOvertimeHours">ساعات العمل الإضافي في العطلات الرسمية والأعياد</label>
        <input type="number" id="holidayOvertimeHours" class="form-control" value="0" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="regularMultiplier">معامل الإضافي العادي (غالباً 1.5)</label>
        <input type="number" id="regularMultiplier" class="form-control" value="1.5" min="1" max="3" step="0.1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="holidayMultiplier">معامل إضافي العطلات (غالباً 2.0)</label>
        <input type="number" id="holidayMultiplier" class="form-control" value="2.0" min="1" max="3" step="0.1" oninput="calculateTool()">
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
  0 => 'أجر الساعة الأساسي = الراتب المعتمد ÷ عدد الساعات الشهرية النظامية (عادة 240 ساعة لنظام 8 ساعات/يوم × 30 يوماً).',
  1 => 'ساعة العمل الإضافي العادية = أجر الساعة الأساسي + 50% إضافية (أجر الساعة × 1.5) وفقاً لأغلب قوانين العمل العربية.',
  2 => 'ساعة العمل في أيام العطلات الأسبوعية والأعياد الرسمية = أجر الساعة × 2.0 (أو 100% زيادة).',
),
        'ينص نظام العمل السعودي والمصري والإماراتي على أن ساعة الإضافي تعادل أجر الساعة مضافاً إليه 50% من أجره الأساسي على الأقل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف يُحسب الحد الأقصى لساعات الإضافي؟',
    'a' => 'تنص قوانين العمل على حد أقصى لساعات الإضافي لا يتجاوز عادة ساعتين إلى 3 ساعات يومياً، أو 720 ساعة سنوياً لحماية صحة العامل وسلامته.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'hourly-wage-calculator',
  1 => 'net-salary-calculator',
  2 => 'daily-wage-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const standardHours = Math.max(1, parseFloat(document.getElementById('monthlyStandardHours').value) || 240);
            const regHours = Math.max(0, parseFloat(document.getElementById('regularOvertimeHours').value) || 0);
            const holHours = Math.max(0, parseFloat(document.getElementById('holidayOvertimeHours').value) || 0);
            const regMult = Math.max(1, parseFloat(document.getElementById('regularMultiplier').value) || 1.5);
            const holMult = Math.max(1, parseFloat(document.getElementById('holidayMultiplier').value) || 2.0);
            const curr = getSelectedCurrency();

            const baseHourlyRate = salary / standardHours;
            const regOvertimePay = regHours * (baseHourlyRate * regMult);
            const holOvertimePay = holHours * (baseHourlyRate * holMult);
            const totalOvertimePay = regOvertimePay + holOvertimePay;
            const grandTotalSalary = salary + totalOvertimePay;

            setPrimaryResult(formatMoney(totalOvertimePay, curr), 'مستحقات العمل الإضافي');
            showResultArea();

            setDetailStats([
                { label: 'أجر ساعة العمل الأساسية', value: formatMoney(baseHourlyRate, curr), color: '#3b82f6' },
                { label: 'أجر ساعة الإضافي العادي (' + regMult + 'x)', value: formatMoney(baseHourlyRate * regMult, curr), color: '#10b981' },
                { label: 'أجر ساعة إضافي العطلات (' + holMult + 'x)', value: formatMoney(baseHourlyRate * holMult, curr), color: '#f59e0b' },
                { label: 'إجمالي الراتب مع الإضافي', value: formatMoney(grandTotalSalary, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي ساعات العمل الإضافي: <strong>${regHours + holHours} ساعة</strong>. تضاف <strong>${formatMoney(totalOvertimePay, curr)}</strong> إلى راتبك الشهري ليصبح المجموع <strong>${formatMoney(grandTotalSalary, curr)}</strong>.</p>
            `);
        
        saveLastInputs('overtime-calculator');
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
    restoreLastInputs('overtime-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>