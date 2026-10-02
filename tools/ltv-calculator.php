<?php
/**
 * أداة: حاسبة القيمة الدائمة للعميل (LTV)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ltv-calculator';
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
        <label class="form-label" for="avgOrderValue">متوسط قيمة الطلب الواحد (AOV)</label>
        <input type="number" id="avgOrderValue" class="form-control" value="250" min="1"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="purchaseFrequency">متوسط عدد مرات الشراء للعميل سنوياً</label>
        <input type="number" id="purchaseFrequency" class="form-control" value="4" min="0.1"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="customerLifespan">متوسط مدة بقاء العميل مع المتجر (سنوات)</label>
        <input type="number" id="customerLifespan" class="form-control" value="3" min="0.1"  step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="grossMarginRate">هامش الربح الإجمالي (%)</label>
        <input type="number" id="grossMarginRate" class="form-control" value="35" min="1" max="100" step="1" oninput="calculateTool()">
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
  0 => 'قيمة العميل السنوية = متوسط قيمة الطلب × عدد مرات الشراء سنوياً.',
  1 => 'إجمالي إيراد العميل = القيمة السنوية × عدد سنوات بقاء العميل.',
  2 => 'القيمة الدائمة الصافية (LTV) = إجمالي الإيراد × هامش الربح.',
),
        'الحساب يفترض استقرار متوسط الإنفاق ومعدل التكرار خلال فترة بقاء العميل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أرفع القيمة الدائمة للعميل LTV؟',
    'a' => 'عبر برامج الولاء والمكافآت، وإعادة الاستهداف عبر البريد ورسائل الواتساب، وتقديم اشتراكات دورية وخدمة عملاء مميزة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cac-calculator',
  1 => 'roas-calculator',
  2 => 'ecommerce-profit-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const aov = Math.max(1, parseFloat(document.getElementById('avgOrderValue').value) || 1);
            const freq = Math.max(0.1, parseFloat(document.getElementById('purchaseFrequency').value) || 1);
            const lifespan = Math.max(0.1, parseFloat(document.getElementById('customerLifespan').value) || 1);
            const margin = Math.max(1, Math.min(100, parseFloat(document.getElementById('grossMarginRate').value) || 35)) / 100;
            const curr = getSelectedCurrency();

            const annualSpend = aov * freq;
            const lifetimeRevenue = annualSpend * lifespan;
            const ltvProfit = lifetimeRevenue * margin;

            setPrimaryResult(formatMoney(ltvProfit, curr), 'صافي القيمة الدائمة للعميل (LTV Profit)');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي إيراد العميل خلال دورة حياته', value: formatMoney(lifetimeRevenue, curr), color: '#3b82f6' },
                { label: 'إنفاق العميل السنوي', value: formatMoney(annualSpend, curr), color: '#10b981' },
                { label: 'إجمالي عدد الطلبات المتوقعة للعميل', value: (freq * lifespan).toFixed(1) + ' طلب', color: '#f59e0b' },
                { label: 'الحد الأقصى الموصى به للاستحواذ (CAC)', value: formatMoney(ltvProfit / 3, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>كل عميل جديد تكتسبه يحقق لمتجرك صافي أرباح تراكمية قدرها <strong>${formatMoney(ltvProfit, curr)}</strong> على مدار <strong>${lifespan} سنوات</strong> بمعدل <strong>${freq} طلبات سنوياً</strong>.</p>
            `);
        
        saveLastInputs('ltv-calculator');
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
    restoreLastInputs('ltv-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>