<?php
/**
 * أداة: حاسبة الربح من بيع منتج أونلاين
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ecommerce-profit-calculator';
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
        <label class="form-label" for="sellingPrice">سعر بيع المنتج للعميل</label>
        <input type="number" id="sellingPrice" class="form-control" value="180" min="1"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="productCost">تكلفة شراء المنتج مع التغليف</label>
        <input type="number" id="productCost" class="form-control" value="60" min="0"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="cpaAdCost">تكلفة الإعلان لكل طلب مؤكد (CPA)</label>
        <input type="number" id="cpaAdCost" class="form-control" value="35" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shippingFee">تكلفة شركة الشحن والتوصيل</label>
        <input type="number" id="shippingFee" class="form-control" value="25" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="paymentFeeRate">عمولة بوابة الدفع الإلكتروني (%)</label>
        <input type="number" id="paymentFeeRate" class="form-control" value="2.75" min="0" max="10" step="0.25" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fixedPaymentFee">رسم بوابة الدفع الثابت لكل عملية</label>
        <input type="number" id="fixedPaymentFee" class="form-control" value="1" min="0"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="returnRate">نسبة الإرجاع أو عدم الاستلام (%)</label>
        <input type="number" id="returnRate" class="form-control" value="8" min="0" max="50" step="1" oninput="calculateTool()">
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
  0 => 'صافي الربح = سعر البيع - (تكلفة المنتج + الإعلانات + الشحن + رسوم بوابة الدفع + أثر المرتجعات).',
  1 => 'عند إرجاع العميل للطلب، يتحمل المتجر عادة تكلفة شحن الذهاب والإياب دون تحقيق بيعة.',
),
        'نسبة المرتجعات تؤثر مباشرة على متوسط تكلفة الشحن لكل طلب مكتمل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أخفض تكلفة الاستحواذ على الطلب (CPA)؟',
    'a' => 'عبر تحسين متجرك لزيادة نسبة التحويل (CRO)، واستخدام عروض الحزم (Bundles) لرفع متوسط قيمة السلة الشرائية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'product-selling-price-calculator',
  1 => 'roas-calculator',
  2 => 'gateway-shipping-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const price = Math.max(1, parseFloat(document.getElementById('sellingPrice').value) || 1);
            const cost = Math.max(0, parseFloat(document.getElementById('productCost').value) || 0);
            const cpa = Math.max(0, parseFloat(document.getElementById('cpaAdCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingFee').value) || 0);
            const payRate = Math.max(0, parseFloat(document.getElementById('paymentFeeRate').value) || 0) / 100;
            const payFixed = Math.max(0, parseFloat(document.getElementById('fixedPaymentFee').value) || 0);
            const returnR = Math.max(0, parseFloat(document.getElementById('returnRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const paymentGatewayDeduction = (price * payRate) + payFixed;
            // خسارة الشحن عند رجوع الشحنة
            const returnLossPerOrder = returnR * ship;
            const totalDeductions = cost + cpa + ship + paymentGatewayDeduction + returnLossPerOrder;
            const netProfit = price - totalDeductions;
            const netMargin = (netProfit / price) * 100;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الربح الفعلي لكل طلب');
            showResultArea();

            setDetailStats([
                { label: 'هامش الربح الصافي', value: netMargin.toFixed(1) + '%', color: netMargin > 0 ? '#10b981' : '#ef4444' },
                { label: 'رسوم بوابة الدفع', value: formatMoney(paymentGatewayDeduction, curr), color: '#3b82f6' },
                { label: 'تكلفة المرتجعات المقدرة', value: formatMoney(returnLossPerOrder, curr), color: '#f59e0b' },
                { label: 'صافي ربح 100 طلب شهرياً', value: formatMoney(netProfit * 100, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>من سعر بيع <strong>${formatMoney(price, curr)}</strong>، يتبقى لك ربح حقيقي قدره <strong>${formatMoney(netProfit, curr)}</strong> بعد خصم المنتج، الإعلانات، الشحن، وبوابات الدفع ومخاطر الإرجاع.</p>
            `);
        
        saveLastInputs('ecommerce-profit-calculator');
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
    restoreLastInputs('ecommerce-profit-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>