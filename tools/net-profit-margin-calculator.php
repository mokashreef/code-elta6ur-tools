<?php
/**
 * أداة: حاسبة هامش الربح الحقيقي بعد المصاريف
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'net-profit-margin-calculator';
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
        <label class="form-label" for="totalRevenue">إجمالي الإيرادات / المبيعات</label>
        <input type="number" id="totalRevenue" class="form-control" value="100000" min="0"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="cogs">تكلفة البضاعة المباعة (COGS)</label>
        <input type="number" id="cogs" class="form-control" value="45000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="operatingExpenses">المصاريف التشغيلية (رواتب، إيجار، برامج)</label>
        <input type="number" id="operatingExpenses" class="form-control" value="25000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="marketingExpenses">مصاريف التسويق والإعلانات</label>
        <input type="number" id="marketingExpenses" class="form-control" value="10000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="taxesAndFees">الضرائب ورسوم الدفع البنكية</label>
        <input type="number" id="taxesAndFees" class="form-control" value="3000" min="0"  step="100" oninput="calculateTool()">
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
  0 => 'الربح الإجمالي (Gross Profit) = الإيرادات - تكلفة البضاعة المباعة.',
  1 => 'صافي الربح (Net Profit) = الإيرادات - (تكلفة البضاعة + المصاريف التشغيلية + الإعلانات + الضرائب والرسوم).',
  2 => 'هامش صافي الربح = (صافي الربح ÷ إجمالي الإيرادات) × 100.',
),
        'هامش الربح الصافي الممتاز يتراوح عموماً بين 15% إلى 25% حسب قطاع العمل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الهامش الصافي الجيد للمتاجر الإلكترونية؟',
    'a' => 'في التجارة الإلكترونية، يُعتبر هامش الربح الصافي بين 10% إلى 20% أداءً صحياً ومستداماً بعد احتساب الإعلانات والشحن وبوابات الدفع.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'profit-margin-calculator',
  1 => 'break-even-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rev = Math.max(0, parseFloat(document.getElementById('totalRevenue').value) || 0);
            const cogs = Math.max(0, parseFloat(document.getElementById('cogs').value) || 0);
            const opex = Math.max(0, parseFloat(document.getElementById('operatingExpenses').value) || 0);
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingExpenses').value) || 0);
            const tax = Math.max(0, parseFloat(document.getElementById('taxesAndFees').value) || 0);
            const curr = getSelectedCurrency();

            const grossProfit = rev - cogs;
            const grossMargin = rev > 0 ? (grossProfit / rev) * 100 : 0;
            const totalExpenses = cogs + opex + mkt + tax;
            const netProfit = rev - totalExpenses;
            const netMargin = rev > 0 ? (netProfit / rev) * 100 : 0;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الربح الفعلي');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي (Net Margin)', value: netMargin.toFixed(2) + '%', color: netMargin >= 10 ? '#10b981' : '#f59e0b' },
                { label: 'إجمالي الربح الأولي (Gross Profit)', value: formatMoney(grossProfit, curr), color: '#3b82f6' },
                { label: 'هامش الربح الأولي (Gross Margin)', value: grossMargin.toFixed(2) + '%', color: '#8b5cf6' },
                { label: 'إجمالي المصاريف الشاملة', value: formatMoney(totalExpenses, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>من إجمالي إيرادات <strong>${formatMoney(rev, curr)}</strong>، يتبقى للنشاط <strong>${formatMoney(netProfit, curr)}</strong> كربح صافٍ، أي بهامش <strong>${netMargin.toFixed(2)}%</strong> بعد دفع جميع الالتزامات.</p>
            `);
        
        saveLastInputs('net-profit-margin-calculator');
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
    restoreLastInputs('net-profit-margin-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>