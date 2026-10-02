<?php
/**
 * أداة: حاسبة صافي الراتب بعد الخصومات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'net-salary-calculator';
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
        <label class="form-label" for="grossSalary">الراتب الأساسي / الإجمالي</label>
        <input type="number" id="grossSalary" class="form-control" value="6000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="allowances">البدلات والمكافآت الشهرية</label>
        <input type="number" id="allowances" class="form-control" value="1000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="socialRate">نسبة التأمينات / التقاعد (%)</label>
        <input type="number" id="socialRate" class="form-control" value="9.75" min="0" max="100" step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="taxRate">نسبة ضريبة الدخل (%) إن وجدت</label>
        <input type="number" id="taxRate" class="form-control" value="0" min="0" max="100" step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="otherDeductions">خصومات أو أقساط أخرى ثابتة</label>
        <input type="number" id="otherDeductions" class="form-control" value="0" min="0"  step="10" oninput="calculateTool()">
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
  0 => 'إجمالي الدخل = الراتب الأساسي + البدلات والمكافآت.',
  1 => 'خصم التأمينات = الراتب الأساسي × (نسبة التأمينات ÷ 100).',
  2 => 'صافي الراتب = إجمالي الدخل - (خصم التأمينات + ضريبة الدخل + الاستقطاعات الأخرى).',
  3 => 'صافي الدخل السنوي = صافي الراتب الشهري × 12 شهر.',
),
        'نسبة التأمينات الشائعة في السعودية للمواطن هي 9.75%، وفي مصر 11%، وفي الإمارات 5%. يمكنك تعديل النسبة بحسب نظام عملك وبلدك.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفرق بين الراتب الأساسي وإجمالي الراتب وصافي الراتب؟',
    'a' => 'الراتب الأساسي هو الراتب المتفق عليه قبل أي إضافات، وإجمالي الراتب يضاف إليه البدلات كالسكن والمواصلات، بينما صافي الراتب هو المبلغ الفعلي الذي يدخل حسابك البنكي بعد استقطاع التأمينات والضرائب.',
  ),
  1 => 
  array (
    'q' => 'هل تحسب التأمينات على البدلات أيضاً؟',
    'a' => 'في بعض الدول مثل نظام التأمينات السعودي تشمل التأمينات الراتب الأساسي وبدل السكن بحد أقصى، بينما في دول أخرى تخصم من الأساسي فقط. يمكنك إدخال المبلغ الخاضع للتأمين في خانة الراتب الأساسي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'hourly-wage-calculator',
  1 => 'daily-wage-calculator',
  2 => 'employee-cost-calculator',
  3 => 'overtime-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const gross = Math.max(0, parseFloat(document.getElementById('grossSalary').value) || 0);
            const allow = Math.max(0, parseFloat(document.getElementById('allowances').value) || 0);
            const socialR = Math.max(0, Math.min(100, parseFloat(document.getElementById('socialRate').value) || 0));
            const taxR = Math.max(0, Math.min(100, parseFloat(document.getElementById('taxRate').value) || 0));
            const other = Math.max(0, parseFloat(document.getElementById('otherDeductions').value) || 0);
            const curr = getSelectedCurrency();

            const totalGross = gross + allow;
            const socialDeduction = (gross * (socialR / 100));
            const taxableIncome = Math.max(0, totalGross - socialDeduction);
            const taxDeduction = (taxableIncome * (taxR / 100));
            const totalDeductions = socialDeduction + taxDeduction + other;
            const netSalary = Math.max(0, totalGross - totalDeductions);
            const yearlyNet = netSalary * 12;

            setPrimaryResult(formatMoney(netSalary, curr), 'صافي الراتب الشهري المستلم');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الدخل قبل الخصم', value: formatMoney(totalGross, curr), color: '#3b82f6' },
                { label: 'خصم التأمينات الاجتماعية', value: formatMoney(socialDeduction, curr), color: '#f59e0b' },
                { label: 'إجمالي الخصومات الشهرية', value: formatMoney(totalDeductions, curr), color: '#ef4444' },
                { label: 'صافي الدخل السنوي المتوقع', value: formatMoney(yearlyNet, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <div class="alert alert-info" style="margin-top:1rem">
                    <strong>نسبة ما تستلمه من الراتب:</strong> ${totalGross > 0 ? ((netSalary / totalGross) * 100).toFixed(1) : 0}% من إجمالي المستحقات.
                    الاستقطاعات تمثل ${totalGross > 0 ? ((totalDeductions / totalGross) * 100).toFixed(1) : 0}% من راتبك.
                </div>
            `);
        
        saveLastInputs('net-salary-calculator');
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
    restoreLastInputs('net-salary-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>