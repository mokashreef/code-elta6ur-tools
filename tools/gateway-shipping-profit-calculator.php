<?php
/**
 * أداة: حاسبة الأرباح بعد بوابة الدفع والشحن والإعلانات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'gateway-shipping-profit-calculator';
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
        <label class="form-label" for="orderTotal">قيمة الطلب الإجمالية المدفوعة من العميل</label>
        <input type="number" id="orderTotal" class="form-control" value="250" min="1"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="productBaseCost">تكلفة البضاعة المباعة في الطلب</label>
        <input type="number" id="productBaseCost" class="form-control" value="85" min="0"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shippingCostPaid">تكلفة الشحن الفعلية على المتجر</label>
        <input type="number" id="shippingCostPaid" class="form-control" value="28" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gatewayPercentage">نسبة عمولة بوابة الدفع (%)</label>
        <input type="number" id="gatewayPercentage" class="form-control" value="2.5" min="0" max="10" step="0.1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gatewayFixedFee">رسم بوابة الدفع الثابت لكل طلب</label>
        <input type="number" id="gatewayFixedFee" class="form-control" value="1" min="0"  step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="adSpendPerOrder">تكلفة الإعلان المخصصة لكل طلب</label>
        <input type="number" id="adSpendPerOrder" class="form-control" value="40" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="vatOnFees">ضريبة القيمة المضافة على رسوم الدفع والشحن (%)</label>
        <input type="number" id="vatOnFees" class="form-control" value="15" min="0" max="25" step="1" oninput="calculateTool()">
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
  0 => 'رسوم بوابات الدفع الإلكترونية تخضع غالباً لضريبة القيمة المضافة ويجب احتسابها.',
  1 => 'حساب الصافي بعد الشحن والإعلانات يضمن عدم وجود خسائر خفية في الطلبات الصغيرة.',
),
        'الرسوم تحتسب بدقة مع الرسوم الثابتة والنسبية المطبقة لدى بوابات الدفع الشهيرة مثل ميسر وتاب وهايبرباي وسترايب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تختلف رسوم بوابة الدفع حسب نوع البطاقة؟',
    'a' => 'نعم؛ بطاقات مدى أو بطاقات الخصم المباشر المحلية تكون عمولتها أقل عادة (حوالي 1% إلى 1.75%) مقارنة بالبطاقات الائتمانية الدولية مثل Visa و Mastercard (حوالي 2.5% إلى 3%).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ecommerce-profit-calculator',
  1 => 'online-store-profit-calculator',
  2 => 'product-selling-price-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const total = Math.max(1, parseFloat(document.getElementById('orderTotal').value) || 1);
            const cogs = Math.max(0, parseFloat(document.getElementById('productBaseCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingCostPaid').value) || 0);
            const gRate = Math.max(0, parseFloat(document.getElementById('gatewayPercentage').value) || 0) / 100;
            const gFixed = Math.max(0, parseFloat(document.getElementById('gatewayFixedFee').value) || 0);
            const ads = Math.max(0, parseFloat(document.getElementById('adSpendPerOrder').value) || 0);
            const feeVat = Math.max(0, parseFloat(document.getElementById('vatOnFees').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const gatewayFee = (total * gRate) + gFixed;
            const gatewayFeeWithVat = gatewayFee * (1 + feeVat);
            const totalDeductions = cogs + ship + gatewayFeeWithVat + ads;
            const netProfit = total - totalDeductions;
            const profitMargin = (netProfit / total) * 100;

            setPrimaryResult(formatMoney(netProfit, curr), 'الصافي المتبقي في جيبك من الطلب');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي للطلب', value: profitMargin.toFixed(1) + '%', color: profitMargin > 0 ? '#10b981' : '#ef4444' },
                { label: 'عمولة بوابة الدفع شاملة ضريبتها', value: formatMoney(gatewayFeeWithVat, curr), color: '#f59e0b' },
                { label: 'إجمالي التكاليف والخصومات', value: formatMoney(totalDeductions, curr), color: '#ef4444' },
                { label: 'المبلغ الإجمالي المحصل', value: formatMoney(total, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>من أصل <strong>${formatMoney(total, curr)}</strong> يدفعها العميل، تستقطع البوابة <strong>${formatMoney(gatewayFeeWithVat, curr)}</strong>، ويكلف الشحن <strong>${formatMoney(ship, curr)}</strong>، والإعلانات <strong>${formatMoney(ads, curr)}</strong>، ويتبقى لك <strong>${formatMoney(netProfit, curr)}</strong> صافياً.</p>
            `);
        
        saveLastInputs('gateway-shipping-profit-calculator');
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
    restoreLastInputs('gateway-shipping-profit-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>