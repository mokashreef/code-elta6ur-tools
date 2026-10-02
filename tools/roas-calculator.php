<?php
/**
 * أداة: حاسبة العائد على الإنفاق الإعلاني (ROAS)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'roas-calculator';
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
        <label class="form-label" for="adSpend">إجمالي الميزانية الإعلانية المنفقة</label>
        <input type="number" id="adSpend" class="form-control" value="2000" min="1"  step="50" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="adRevenue">المبيعات أو الإيرادات المحققة من الإعلانات</label>
        <input type="number" id="adRevenue" class="form-control" value="8000" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="profitMargin">متوسط هامش ربح منتجاتك (%) بدون إعلانات</label>
        <input type="number" id="profitMargin" class="form-control" value="40" min="1" max="100" step="1" oninput="calculateTool()">
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
  0 => 'ROAS = الإيرادات الناتجة عن الإعلانات ÷ تكلفة الإعلانات.',
  1 => 'نقطة تعادل ROAS = 1 ÷ هامش ربح المنتج.',
  2 => 'إذا كان الـ ROAS أقل من نقطة التعادل فأنت تخسر مالاً حتى وإن كانت الإيرادات تبدو مرتفعة.',
),
        'تحقيق مبيعات بإعلانات ذات عائد مرتفع لا يعني بالضرورة ربحاً صافياً إذا كان هامش ربح المنتج منخفضاً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الـ ROAS الجيد للمتاجر؟',
    'a' => 'لا يوجد رقم ثابت، فالمنتجات بهامش ربح 20% تحتاج ROAS أعلى من 5x للتعادل، بينما المنتجات الرقمية بهامش 80% تربح حتى عند ROAS يعادل 2x.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cac-calculator',
  1 => 'roi-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const spend = Math.max(1, parseFloat(document.getElementById('adSpend').value) || 1);
            const rev = Math.max(0, parseFloat(document.getElementById('adRevenue').value) || 0);
            const margin = Math.max(1, Math.min(100, parseFloat(document.getElementById('profitMargin').value) || 40)) / 100;
            const curr = getSelectedCurrency();

            const roas = rev / spend;
            const breakEvenRoas = 1 / margin;
            const isProfitable = roas >= breakEvenRoas;
            const estimatedGrossProfit = rev * margin;
            const estimatedNetAdProfit = estimatedGrossProfit - spend;

            setPrimaryResult(roas.toFixed(2) + 'x (' + (roas * 100).toFixed(0) + '%)', 'العائد على الإعلانات (ROAS)');
            showResultArea();

            setDetailStats([
                { label: 'نقطة تعادل الإعلانات (Break-even ROAS)', value: breakEvenRoas.toFixed(2) + 'x', color: '#f59e0b' },
                { label: 'صافي الربح من الحملة الإعلانية', value: formatMoney(estimatedNetAdProfit, curr), color: isProfitable ? '#10b981' : '#ef4444' },
                { label: 'حالة الحملة', value: isProfitable ? 'حملة رابحة ✅' : 'حملة خاسرة ❌', color: isProfitable ? '#10b981' : '#ef4444' },
                { label: 'كل دولار إعلاني جلب مبيعات بـ', value: formatMoney(roas, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>حملتك حققت <strong>${roas.toFixed(2)}x</strong> عائد إعلاني. بما أن هامش ربح منتجاتك <strong>${(margin * 100).toFixed(0)}%</strong>، فإن نقطة التعادل لإعلاناتك هي <strong>${breakEvenRoas.toFixed(2)}x</strong>. صافي ربح الحملة بعد تكلفة المنتج والإعلان: <strong>${formatMoney(estimatedNetAdProfit, curr)}</strong>.</p>
            `);
        
        saveLastInputs('roas-calculator');
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
    restoreLastInputs('roas-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>