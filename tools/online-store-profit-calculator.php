<?php
/**
 * أداة: حاسبة أرباح المتجر الإلكتروني
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'online-store-profit-calculator';
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
        <label class="form-label" for="monthlyOrders">عدد الطلبات الشهرية</label>
        <input type="number" id="monthlyOrders" class="form-control" value="300" min="1"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="avgBasketValue">متوسط قيمة السلة (AOV)</label>
        <input type="number" id="avgBasketValue" class="form-control" value="220" min="10"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="cogsPercent">نسبة تكلفة البضاعة من الإيرادات (%)</label>
        <input type="number" id="cogsPercent" class="form-control" value="40" min="5" max="90" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="adSpendMonthly">ميزانية الإعلانات الشهرية</label>
        <input type="number" id="adSpendMonthly" class="form-control" value="12000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="platformAndAppFees">اشتراكات المنصة والتطبيقات والمستودع</label>
        <input type="number" id="platformAndAppFees" class="form-control" value="3500" min="0"  step="200" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="salariesAndOther">رواتب ومصاريف إدارية أخرى</label>
        <input type="number" id="salariesAndOther" class="form-control" value="6000" min="0"  step="500" oninput="calculateTool()">
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
  0 => 'إجمالي المبيعات = عدد الطلبات × متوسط قيمة السلة.',
  1 => 'الربح الإجمالي = المبيعات - تكلفة المنتجات.',
  2 => 'صافي الربح = الربح الإجمالي - (الإعلانات + اشتراكات المنصة والتطبيقات + الرواتب والتكاليف الثابتة).',
),
        'الحساب يقدر المصاريف الشهرية الثابتة والمتغيرة لتحديد سلامة نموذج عمل المتجر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أزيد أرباح متجري الإلكتروني بدون زيادة ميزانية الإعلانات؟',
    'a' => 'ركز على رفع متوسط قيمة السلة الشرائية (AOV) عبر عروض البيع التكميلي (Upselling & Cross-selling)، وتحسين نسبة الاحتفاظ بالعملاء لتكرار الشراء.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ecommerce-profit-calculator',
  1 => 'gateway-shipping-profit-calculator',
  2 => 'break-even-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const orders = Math.max(1, parseFloat(document.getElementById('monthlyOrders').value) || 1);
            const aov = Math.max(10, parseFloat(document.getElementById('avgBasketValue').value) || 10);
            const cogsRate = Math.max(5, Math.min(90, parseFloat(document.getElementById('cogsPercent').value) || 40)) / 100;
            const ads = Math.max(0, parseFloat(document.getElementById('adSpendMonthly').value) || 0);
            const software = Math.max(0, parseFloat(document.getElementById('platformAndAppFees').value) || 0);
            const salaries = Math.max(0, parseFloat(document.getElementById('salariesAndOther').value) || 0);
            const curr = getSelectedCurrency();

            const totalRevenue = orders * aov;
            const totalCogs = totalRevenue * cogsRate;
            const grossProfit = totalRevenue - totalCogs;
            const totalExpenses = totalCogs + ads + software + salaries;
            const netProfit = totalRevenue - totalExpenses;
            const netMargin = (netProfit / totalRevenue) * 100;
            const profitPerOrder = netProfit / orders;

            setPrimaryResult(formatMoney(netProfit, curr), 'صافي الأرباح الشهرية للمتجر');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المبيعات الشهرية', value: formatMoney(totalRevenue, curr), color: '#3b82f6' },
                { label: 'هامش الربح الصافي للمتجر', value: netMargin.toFixed(1) + '%', color: netMargin >= 10 ? '#10b981' : '#ef4444' },
                { label: 'صافي الربح لكل طلب', value: formatMoney(profitPerOrder, curr), color: '#8b5cf6' },
                { label: 'إجمالي المصاريف الشهرية', value: formatMoney(totalExpenses, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>عند تحقيق <strong>${orders} طلب</strong> بمتوسط سلة <strong>${formatMoney(aov, curr)}</strong>، يحقق متجرك مبيعات <strong>${formatMoney(totalRevenue, curr)}</strong> وصافي ربح <strong>${formatMoney(netProfit, curr)}</strong> بعد خصم البضاعة والإعلانات والتشغيل.</p>
            `);
        
        saveLastInputs('online-store-profit-calculator');
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
    restoreLastInputs('online-store-profit-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>