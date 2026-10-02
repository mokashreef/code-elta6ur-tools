<?php
/**
 * أداة: حاسبة تحويل الراتب الشهري إلى يومي وساعي ودقيق
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'salary-to-hourly-daily-converter';
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
        <label class="form-label" for="monthlySalaryInputConv">الراتب الشهري</label>
        <input type="number" id="monthlySalaryInputConv" class="form-control" value="6500" min="100"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="daysBasisConv">أساس احتساب الأيام بالشهر</label>
        <select id="daysBasisConv" class="form-control" onchange="calculateTool()">
            <option value="30" selected>30 يوماً (الأساس المعتمد في أنظمة العمل والخصومات)</option>
            <option value="22" >22 يوماً (أيام العمل الفعلية فقط - عطلة يومين أسبوعياً)</option>
            <option value="26" >26 يوماً (أيام العمل الفعلية - عطلة يوم واحد)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="hoursPerDayConv">ساعات العمل اليومية الرسمية</label>
        <input type="number" id="hoursPerDayConv" class="form-control" value="8" min="1" max="24" step="1"  oninput="calculateTool()">
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
  0 => 'القسمة على 30 يوماً هي المعيار المعتمد في قانون العمل لمعظم العقود الشهرية شاملة الإجازات الأسبوعية.',
  1 => 'حساب أجر الساعة بدقة يفيد في حساب مستحقات العمل الإضافي، وساعات التأخير والخصومات.',
),
        'يفترض دواماً منتظماً بعدد الساعات المدخلة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحسب قيمة خصم ساعة تأخير؟',
    'a' => 'اقسم راتبك الشهري على 30، ثم اقسم الناتج على ساعات دوامك اليومي (عادة 8 ساعات)، لتعرف قيمة ساعة التأخير الدقيقة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'hourly-wage-calculator',
  1 => 'daily-wage-calculator',
  2 => 'net-salary-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(100, parseFloat(document.getElementById('monthlySalaryInputConv').value) || 6500);
            const days = parseFloat(document.getElementById('daysBasisConv').value) || 30;
            const hours = Math.max(1, parseFloat(document.getElementById('hoursPerDayConv').value) || 8);
            const curr = getSelectedCurrency();

            const daily = salary / days;
            const hourly = daily / hours;
            const minute = hourly / 60;
            const weekly = hourly * hours * 5;

            setPrimaryResult(formatMoney(hourly, curr) + ' / ساعة عمل', 'أجرك في ساعة العمل الواحدة');
            showResultArea();

            setDetailStats([
                { label: 'أجر يوم العمل الكامل', value: formatMoney(daily, curr), color: '#3b82f6' },
                { label: 'أجر ساعة العمل', value: formatMoney(hourly, curr), color: '#10b981' },
                { label: 'أجر دقيقة العمل', value: formatMoney(minute, curr), color: '#f59e0b' },
                { label: 'الأجر الأسبوعي التقديري', value: formatMoney(weekly, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>براتب شهري <strong>${formatMoney(salary, curr)}</strong>:<br>
                - اليوم يعادل <strong>${formatMoney(daily, curr)}</strong>.<br>
                - الساعة تعادل <strong>${formatMoney(hourly, curr)}</strong>.<br>
                - كل دقيقة تقضيها في وظيفتك تدر عليك <strong>${formatMoney(minute, curr)}</strong>.</p>
            `);
        
        saveLastInputs('salary-to-hourly-daily-converter');
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
    restoreLastInputs('salary-to-hourly-daily-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>