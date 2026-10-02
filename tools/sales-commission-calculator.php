<?php
/**
 * أداة: حاسبة عمولة مندوب المبيعات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sales-commission-calculator';
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
        <label class="form-label" for="targetAmount">الهدف البيعي الشهري (Target)</label>
        <input type="number" id="targetAmount" class="form-control" value="100000" min="1"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="actualSales">المبيعات الفعلية المحققة</label>
        <input type="number" id="actualSales" class="form-control" value="120000" min="0"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="baseCommissionRate">نسبة العمولة الأساسية (%)</label>
        <input type="number" id="baseCommissionRate" class="form-control" value="3" min="0" max="100" step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="bonusOverTarget">نسبة بونص إضافية فوق الهدف (%)</label>
        <input type="number" id="bonusOverTarget" class="form-control" value="2" min="0" max="100" step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="baseSalary">الراتب الأساسي للمندوب</label>
        <input type="number" id="baseSalary" class="form-control" value="4000" min="0"  step="100" oninput="calculateTool()">
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
  0 => 'نسبة الإنجاز = (المبيعات الفعلية ÷ الهدف البيعي) × 100.',
  1 => 'عند تجاوز 100% من الهدف، تُحسب المبيعات الإضافية بنسبة بونص تحفيزية أعلى.',
  2 => 'إجمالي دخل المندوب = الراتب الثابت + العمولة الأساسية + بونص التميز.',
),
        'النظام التحفيزي المعتمد يكافئ المندوب بنسبة أعلى على المبيعات التي تتخطى التارجت المطلوب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يحدث إذا لم يحقق المندوب هدفه بالكامل؟',
    'a' => 'يعتمد على سياسة الشركة؛ بعض الشركات تصرف العمولة بنسبة الإنجاز، وأخرى تشترط تحقيق 80% كحد أدنى لبدء استحقاق العمولة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'commission-calculator',
  1 => 'profit-margin-calculator',
  2 => 'roas-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const target = Math.max(1, parseFloat(document.getElementById('targetAmount').value) || 1);
            const actual = Math.max(0, parseFloat(document.getElementById('actualSales').value) || 0);
            const baseRate = Math.max(0, parseFloat(document.getElementById('baseCommissionRate').value) || 0) / 100;
            const bonusRate = Math.max(0, parseFloat(document.getElementById('bonusOverTarget').value) || 0) / 100;
            const salary = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const curr = getSelectedCurrency();

            const achievementRate = (actual / target) * 100;
            let commission = 0;
            let bonus = 0;

            if (actual <= target) {
                commission = actual * baseRate;
            } else {
                commission = target * baseRate;
                const excess = actual - target;
                bonus = excess * (baseRate + bonusRate);
            }

            const totalCommission = commission + bonus;
            const totalIncome = salary + totalCommission;

            setPrimaryResult(formatMoney(totalCommission, curr), 'إجمالي العمولة والمكافأة');
            showResultArea();

            setDetailStats([
                { label: 'نسبة تحقيق الهدف (Target)', value: achievementRate.toFixed(1) + '%', color: achievementRate >= 100 ? '#10b981' : '#f59e0b' },
                { label: 'العمولة الأساسية', value: formatMoney(commission, curr), color: '#3b82f6' },
                { label: 'حافز التميز الإضافي (Bonus)', value: formatMoney(bonus, curr), color: '#8b5cf6' },
                { label: 'إجمالي دخل المندوب لهذا الشهر', value: formatMoney(totalIncome, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>حقق المندوب <strong>${achievementRate.toFixed(1)}%</strong> من هدفه البيعي. إجمالي المستحقات تشمل <strong>${formatMoney(salary, curr)}</strong> راتباً ثابتاً + <strong>${formatMoney(totalCommission, curr)}</strong> عمولات وحوافز.</p>
            `);
        
        saveLastInputs('sales-commission-calculator');
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
    restoreLastInputs('sales-commission-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>