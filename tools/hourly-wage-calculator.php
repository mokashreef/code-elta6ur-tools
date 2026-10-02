<?php
/**
 * أداة: حاسبة الراتب بالساعة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'hourly-wage-calculator';
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
        <label class="form-label" for="monthlySalary">الراتب الشهري (صافي أو إجمالي)</label>
        <input type="number" id="monthlySalary" class="form-control" value="5000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hoursPerWeek">ساعات العمل في الأسبوع</label>
        <input type="number" id="hoursPerWeek" class="form-control" value="40" min="1" max="100" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="weeksPerMonth">متوسط الأسابيع في الشهر</label>
        <input type="number" id="weeksPerMonth" class="form-control" value="4.33" min="4" max="5" step="0.01" oninput="calculateTool()">
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
  0 => 'ساعات العمل الشهرية = ساعات العمل الأسبوعية × 4.33 (متوسط عدد أسابيع الشهر).',
  1 => 'أجر الساعة = الراتب الشهري ÷ ساعات العمل الشهرية.',
  2 => 'أجر الدقيقة = أجر الساعة ÷ 60.',
),
        'يحسب الشهر عالمياً على أنه 4.33 أسبوعاً (52 أسبوعاً في السنة ÷ 12 شهراً).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا نستخدم 4.33 أسبوع وليس 4 أسابيع بالضبط؟',
    'a' => 'لأن الشهر الميلادي يحتوي على 30 أو 31 يوماً وليس 28 يوماً، وبذلك يحتوي العام على 52 أسبوعاً، وعند قسمة 52 على 12 شهراً يكون الناتج 4.33 أسبوع لكل شهر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'daily-wage-calculator',
  1 => 'net-salary-calculator',
  2 => 'overtime-calculator',
  3 => 'freelancer-hourly-rate-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(0, parseFloat(document.getElementById('monthlySalary').value) || 0);
            const hpw = Math.max(1, parseFloat(document.getElementById('hoursPerWeek').value) || 40);
            const wpm = Math.max(1, parseFloat(document.getElementById('weeksPerMonth').value) || 4.33);
            const curr = getSelectedCurrency();

            const monthlyHours = hpw * wpm;
            const hourlyRate = monthlyHours > 0 ? (salary / monthlyHours) : 0;
            const dailyRate = hourlyRate * (hpw / 5);
            const minuteRate = hourlyRate / 60;

            setPrimaryResult(formatMoney(hourlyRate, curr), 'سعر ساعة العمل الواحدة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ساعات العمل شهرياً', value: formatNumber(monthlyHours, 1) + ' ساعة', color: '#3b82f6' },
                { label: 'أجر يوم العمل (على أساس 5 أيام)', value: formatMoney(dailyRate, curr), color: '#10b981' },
                { label: 'أجر دقيقة العمل', value: formatMoney(minuteRate, curr), color: '#8b5cf6' },
                { label: 'الأجر الأسبوعي', value: formatMoney(hourlyRate * hpw, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>كل ساعة تقضيها في وظيفتك تدر عليك <strong>${formatMoney(hourlyRate, curr)}</strong> بناءً على دوام أسبوعي ${hpw} ساعة.</p>
            `);
        
        saveLastInputs('hourly-wage-calculator');
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
    restoreLastInputs('hourly-wage-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>