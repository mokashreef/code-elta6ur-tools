<?php
/**
 * أداة: حاسبة تكلفة المنتج الحقيقية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'real-product-cost-calculator';
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
        <label class="form-label" for="purchasePrice">سعر شراء المنتج من المورد</label>
        <input type="number" id="purchasePrice" class="form-control" value="50" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="customsDuty">رسوم الجمارك والشحن الدولي للقطعة</label>
        <input type="number" id="customsDuty" class="form-control" value="8" min="0"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="packagingCost">تكلفة التغليف والعلب والملصقات</label>
        <input type="number" id="packagingCost" class="form-control" value="5" min="0"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="storageCost">تكلفة التخزين والمناولة لكل قطعة</label>
        <input type="number" id="storageCost" class="form-control" value="3" min="0"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="defectReserveRate">نسبة مخصص التوالف والمرتجعات (%)</label>
        <input type="number" id="defectReserveRate" class="form-control" value="5" min="0" max="30" step="0.5" oninput="calculateTool()">
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
  0 => 'تكلفة الهبوط (Landed Cost) تشمل سعر الشراء + الشحن الدولي + الجمارك + التغليف والتخزين.',
  1 => 'تجاهل نسبة التوالف والمرتجعات يؤدي إلى تآكل أرباح المتجر دون معرفة السبب الحقيقي.',
),
        'مخصص التوالف يحمي رأس المال من المنتجات التالفة أثناء الشحن أو التي يسترجعها العميل متضررة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما أهمية حساب Landed Cost؟',
    'a' => 'تسعير المنتجات بناءً على سعر الشراء من المصنع فقط هو الخطأ الأول الذي يتسبب في إفلاس المتاجر الإلكترونية الناشئة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'product-selling-price-calculator',
  1 => 'shipping-cost-share-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const buy = Math.max(0, parseFloat(document.getElementById('purchasePrice').value) || 0);
            const customs = Math.max(0, parseFloat(document.getElementById('customsDuty').value) || 0);
            const pack = Math.max(0, parseFloat(document.getElementById('packagingCost').value) || 0);
            const store = Math.max(0, parseFloat(document.getElementById('storageCost').value) || 0);
            const defectRate = Math.max(0, parseFloat(document.getElementById('defectReserveRate').value) || 0) / 100;
            const curr = getSelectedCurrency();

            const directCost = buy + customs + pack + store;
            const defectCost = directCost * defectRate;
            const realLandedCost = directCost + defectCost;

            setPrimaryResult(formatMoney(realLandedCost, curr), 'التكلفة الحقيقية الكاملة (Landed Cost)');
            showResultArea();

            setDetailStats([
                { label: 'سعر الشراء الأساسي', value: formatMoney(buy, curr), color: '#3b82f6' },
                { label: 'مصاريف جمارك وتغليف وتخزين', value: formatMoney(customs + pack + store, curr), color: '#f59e0b' },
                { label: 'مخصص المرتجعات والتوالف', value: formatMoney(defectCost, curr), color: '#ef4444' },
                { label: 'نسبة الزيادة عن سعر الشراء', value: buy > 0 ? (((realLandedCost - buy) / buy) * 100).toFixed(1) + '%' : '0%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بينما تشتري القطعة بـ <strong>${formatMoney(buy, curr)}</strong>، فإن تكلفتها الحقيقية بعد الشحن والجمارك والتغليف والتوالف تصل إلى <strong>${formatMoney(realLandedCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('real-product-cost-calculator');
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
    restoreLastInputs('real-product-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>