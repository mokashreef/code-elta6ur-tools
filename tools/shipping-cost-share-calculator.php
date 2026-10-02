<?php
/**
 * أداة: حاسبة تكلفة الشحن ضمن سعر المنتج
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'shipping-cost-share-calculator';
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
        <label class="form-label" for="totalShipInvoice">إجمالي فاتورة الشحن للشحنة / الحاوية</label>
        <input type="number" id="totalShipInvoice" class="form-control" value="3000" min="0"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="totalUnits">إجمالي عدد القطع في الشحنة</label>
        <input type="number" id="totalUnits" class="form-control" value="500" min="1"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="allocationMethod">طريقة التوزيع</label>
        <select id="allocationMethod" class="form-control" onchange="calculateTool()">
            <option value="equal" selected>بالتساوي على جميع القطع</option>
            <option value="weight" >حسب الوزن النسبي للقطعة (كجم)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="itemWeight">وزن القطعة الواحدة (كجم) - إذا اخترت الوزن</label>
        <input type="number" id="itemWeight" class="form-control" value="0.8" min="0.01"  step="0.05" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="totalShipWeight">إجمالي وزن الشحنة (كجم) - إذا اخترت الوزن</label>
        <input type="number" id="totalShipWeight" class="form-control" value="400" min="1"  step="5" oninput="calculateTool()">
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
  0 => 'التوزيع بالتساوي يصلح للبضائع المتشابهة في الحجم والوزن.',
  1 => 'التوزيع بالوزن هو الأدق عندما تحتوي الشحنة على منتجات متفاوتة الثقل والحجم.',
),
        'التكاليف تشمل الشحن مع أي مصاريف تفريغ ومناولة مرتبطة به.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى يجب توزيع الشحن بالحجم CBM بدلاً من الوزن؟',
    'a' => 'عندما تكون المنتجات خفيفة ولكنها تأخذ حيزاً كبيراً في الشحن الجوي أو البحري (الوزن الحجمي Volumetric Weight).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'real-product-cost-calculator',
  1 => 'product-selling-price-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const totalShip = Math.max(0, parseFloat(document.getElementById('totalShipInvoice').value) || 0);
            const units = Math.max(1, parseFloat(document.getElementById('totalUnits').value) || 1);
            const method = document.getElementById('allocationMethod').value;
            const curr = getSelectedCurrency();

            let unitShipCost = 0;
            if (method === 'equal') {
                unitShipCost = totalShip / units;
            } else {
                const itemW = Math.max(0.01, parseFloat(document.getElementById('itemWeight').value) || 1);
                const totalW = Math.max(itemW, parseFloat(document.getElementById('totalShipWeight').value) || itemW);
                unitShipCost = totalShip * (itemW / totalW);
            }

            setPrimaryResult(formatMoney(unitShipCost, curr), 'نصيب القطعة الواحدة من الشحن');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي قيمة الشحن للشحنة', value: formatMoney(totalShip, curr), color: '#3b82f6' },
                { label: 'إجمالي عدد المنتجات', value: units + ' قطعة', color: '#10b981' },
                { label: 'طريقة التوزيع المطبقة', value: method === 'equal' ? 'بالتساوي' : 'بحسب الوزن', color: '#8b5cf6' },
                { label: 'تكلفة شحن 100 قطعة', value: formatMoney(unitShipCost * 100, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>يجب تحميل سعر بيع كل قطعة بمقدار <strong>${formatMoney(unitShipCost, curr)}</strong> لتغطية فاتورة الشحن الإجمالية.</p>
            `);
        
        saveLastInputs('shipping-cost-share-calculator');
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
    restoreLastInputs('shipping-cost-share-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>