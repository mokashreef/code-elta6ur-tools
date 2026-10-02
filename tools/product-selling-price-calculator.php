<?php
/**
 * أداة: حاسبة سعر بيع المنتج
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'product-selling-price-calculator';
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
        <label class="form-label" for="productCost">تكلفة شراء أو تصنيع المنتج</label>
        <input type="number" id="productCost" class="form-control" value="80" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shippingCost">تكلفة الشحن والتغليف للقطعة</label>
        <input type="number" id="shippingCost" class="form-control" value="20" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="marketingPerUnit">تكلفة الإعلان المتوقعة لكل بيعة (CPA)</label>
        <input type="number" id="marketingPerUnit" class="form-control" value="25" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="targetProfitMargin">هامش الربح الصافي المستهدف (%)</label>
        <input type="number" id="targetProfitMargin" class="form-control" value="25" min="1" max="90" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="vatRate">نسبة ضريبة القيمة المضافة (%) إن وجدت</label>
        <input type="number" id="vatRate" class="form-control" value="15" min="0" max="50" step="1" oninput="calculateTool()">
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
  0 => 'التكلفة الإجمالية = تكلفة المنتج + الشحن والتغليف + تكلفة الاستحواذ الإعلاني.',
  1 => 'سعر البيع قبل الضريبة = التكلفة الإجمالية ÷ (1 - هامش الربح المستهدف).',
  2 => 'سعر البيع النهائي = السعر قبل الضريبة × (1 + نسبة الضريبة).',
),
        'التسعير يعتمد على تحقيق نسبة الربح المستهدفة من إجمالي سعر البيع، وليس مجرد إضافة النسبة فوق التكلفة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا نقسم على (1 - الهامش) بدلاً من ضرب التكلفة في النسبة؟',
    'a' => 'لأن هامش الربح يُحسب كنسبة من سعر البيع النهائي؛ فإذا أردت هامش 20% وتكلفتك 80، فإن 80 ÷ 0.8 = 100، وبهذا يكون ربحك 20 من 100 وهو ما يمثل 20% فعلاً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'real-product-cost-calculator',
  1 => 'profit-margin-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cost = Math.max(0, parseFloat(document.getElementById('productCost').value) || 0);
            const ship = Math.max(0, parseFloat(document.getElementById('shippingCost').value) || 0);
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingPerUnit').value) || 0);
            const margin = Math.max(1, Math.min(90, parseFloat(document.getElementById('targetProfitMargin').value) || 25)) / 100;
            const vat = Math.max(0, parseFloat(document.getElementById('vatRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const totalUnitCost = cost + ship + mkt;
            // Selling price before VAT to achieve target margin: Price = Cost / (1 - margin)
            const priceBeforeVat = totalUnitCost / (1 - margin);
            const profitPerUnit = priceBeforeVat - totalUnitCost;
            const vatAmount = priceBeforeVat * vat;
            const finalSellingPrice = priceBeforeVat + vatAmount;

            setPrimaryResult(formatMoney(finalSellingPrice, curr), 'سعر البيع المقترح للمستهلك شامل الضريبة');
            showResultArea();

            setDetailStats([
                { label: 'سعر البيع قبل الضريبة', value: formatMoney(priceBeforeVat, curr), color: '#3b82f6' },
                { label: 'صافي الربح في كل قطعة', value: formatMoney(profitPerUnit, curr), color: '#10b981' },
                { label: 'قيمة ضريبة القيمة المضافة', value: formatMoney(vatAmount, curr), color: '#f59e0b' },
                { label: 'إجمالي التكلفة الشاملة للقطعة', value: formatMoney(totalUnitCost, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>لتحقيق هامش ربح <strong>${(margin * 100).toFixed(0)}%</strong> بعد مصاريف الإعلانات والشحن، يُوصى بتسعير المنتج عند <strong>${formatMoney(finalSellingPrice, curr)}</strong> شامل الضريبة، لتربح <strong>${formatMoney(profitPerUnit, curr)}</strong> صافياً في كل مبيعة.</p>
            `);
        
        saveLastInputs('product-selling-price-calculator');
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
    restoreLastInputs('product-selling-price-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>