<?php
/**
 * أداة: حاسبة العمولة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'commission-calculator';
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
        <label class="form-label" for="salesAmount">قيمة المبيعات أو الصفقة</label>
        <input type="number" id="salesAmount" class="form-control" value="50000" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="commissionRate">نسبة العمولة (%)</label>
        <input type="number" id="commissionRate" class="form-control" value="5" min="0.1" max="100" step="0.1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="baseSalary">الراتب الأساسي الثابت (إن وجد)</label>
        <input type="number" id="baseSalary" class="form-control" value="0" min="0"  step="50" oninput="calculateTool()">
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
  0 => 'العمولة = قيمة المبيعات × (نسبة العمولة ÷ 100).',
  1 => 'إجمالي المستحق = الراتب الأساسي + مبلغ العمولة.',
),
        'الحساب مبني على عمولة ذات نسبة ثابتة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تحسب العمولة قبل الضريبة أم بعدها؟',
    'a' => 'المتعارف عليه تجارياً هو احتساب العمولة على صافي قيمة المبيعات بعد استبعاد ضريبة القيمة المضافة ومصاريف الشحن المستردة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sales-commission-calculator',
  1 => 'profit-margin-calculator',
  2 => 'roas-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const sales = Math.max(0, parseFloat(document.getElementById('salesAmount').value) || 0);
            const rate = Math.max(0, parseFloat(document.getElementById('commissionRate').value) || 0) / 100;
            const base = Math.max(0, parseFloat(document.getElementById('baseSalary').value) || 0);
            const curr = getSelectedCurrency();

            const commission = sales * rate;
            const totalEarnings = base + commission;

            setPrimaryResult(formatMoney(commission, curr), 'مبلغ العمولة المستحق');
            showResultArea();

            setDetailStats([
                { label: 'نسبة العمولة المطبقة', value: (rate * 100).toFixed(2) + '%', color: '#3b82f6' },
                { label: 'إجمالي المبيعات المحققة', value: formatMoney(sales, curr), color: '#10b981' },
                { label: 'الراتب الثابت', value: formatMoney(base, curr), color: '#f59e0b' },
                { label: 'إجمالي الدخل (الراتب + العمولة)', value: formatMoney(totalEarnings, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>على مبيعات قدرها <strong>${formatMoney(sales, curr)}</strong> بنسبة عمولة <strong>${(rate * 100).toFixed(1)}%</strong>، تبلغ عمولتك <strong>${formatMoney(commission, curr)}</strong>.</p>
            `);
        
        saveLastInputs('commission-calculator');
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
    restoreLastInputs('commission-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>