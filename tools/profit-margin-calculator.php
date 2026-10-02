<?php
/**
 * أداة: حاسبة هامش الربح
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'profit-margin-calculator';
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
        <label class="form-label" for="costPrice">سعر التكلفة (Cost)</label>
        <input type="number" id="costPrice" class="form-control" value="70" min="0"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="sellingPrice">سعر البيع (Selling Price)</label>
        <input type="number" id="sellingPrice" class="form-control" value="100" min="0"  step="0.5" oninput="calculateTool()">
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
  0 => 'الربح الإجمالي = سعر البيع - سعر التكلفة.',
  1 => 'هامش الربح (Margin) = (الربح ÷ سعر البيع) × 100. (يعبر عن نسبة الربح من كل ريال/دولار مبيعات).',
  2 => 'نسبة الزيادة (Markup) = (الربح ÷ التكلفة) × 100. (يعبر عن كم أضفت فوق تكلفة الشراء).',
),
        'لا يشمل هذا الحساب المصاريف الإدارية والتشغيلية الإضافية (يعبر عن الهامش الإجمالي Gross Margin).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الفرق الجوهري بين Margin و Markup؟',
    'a' => 'الـ Margin يقيس كم ربحت كنسبة من سعر البيع، ولا يمكن أن يتجاوز 100%، بينما الـ Markup يقيس كم أضفت فوق التكلفة ويمكن أن يكون 150% أو 300% أو أكثر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'net-profit-margin-calculator',
  1 => 'product-selling-price-calculator',
  2 => 'break-even-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cost = Math.max(0, parseFloat(document.getElementById('costPrice').value) || 0);
            const sell = Math.max(0, parseFloat(document.getElementById('sellingPrice').value) || 0);
            const curr = getSelectedCurrency();

            const profit = sell - cost;
            const margin = sell > 0 ? ((profit / sell) * 100) : 0;
            const markup = cost > 0 ? ((profit / cost) * 100) : 0;

            setPrimaryResult(margin.toFixed(2) + '%', 'هامش الربح (Profit Margin)');
            showResultArea();

            setDetailStats([
                { label: 'مبلغ الربح لكل قطعة', value: formatMoney(profit, curr), color: profit >= 0 ? '#10b981' : '#ef4444' },
                { label: 'نسبة الزيادة على التكلفة (Markup)', value: markup.toFixed(2) + '%', color: '#3b82f6' },
                { label: 'سعر التكلفة', value: formatMoney(cost, curr), color: '#6b7280' },
                { label: 'سعر البيع النهائي', value: formatMoney(sell, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>عند بيع منتج تكلفته <strong>${formatMoney(cost, curr)}</strong> بسعر <strong>${formatMoney(sell, curr)}</strong>، تربح <strong>${formatMoney(profit, curr)}</strong>، بنسبة هامش ربح <strong>${margin.toFixed(2)}%</strong> من سعر البيع، وزيادة <strong>${markup.toFixed(2)}%</strong> فوق التكلفة.</p>
            `);
        
        saveLastInputs('profit-margin-calculator');
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
    restoreLastInputs('profit-margin-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>