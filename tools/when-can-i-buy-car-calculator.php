<?php
/**
 * أداة: حاسبة متى أستطيع شراء سيارة؟
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'when-can-i-buy-car-calculator';
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
        <label class="form-label" for="targetCarPrice">سعر السيارة المستهدفة</label>
        <input type="number" id="targetCarPrice" class="form-control" value="65000" min="5000"  step="2500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentCarSavings">المدخرات المتوفرة لديك حالياً لشراء السيارة</label>
        <input type="number" id="currentCarSavings" class="form-control" value="10000" min="0"  step="1000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyCarSavingsPace">المبلغ الذي تستطيع ادخاره شهرياً لشراء السيارة</label>
        <input type="number" id="monthlyCarSavingsPace" class="form-control" value="2000" min="100"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="purchaseStrategy">طريقة الشراء المخططة</label>
        <select id="purchaseStrategy" class="form-control" onchange="calculateTool()">
            <option value="cash" >شراء كاش كامل بنسبة 100% (بدون أي فوائد أو أقساط)</option>
            <option value="downpayment_20" selected>تجميع دفعة أولى 20% فقط وتقسيط الباقي</option>
            <option value="downpayment_50" >تجميع دفعة أولى 50% لتقليل القسط الشهري</option>
        </select>
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
  0 => 'دفع دفعة أولى لا تقل عن 20% يخفض قيمة القسط الشهري ويوفر آلاف الريالات/الدولارات في الفوائد التمويلية.',
  1 => 'الشراء كاش يمنحك قوة تفاوضية في المعارض للحصول على خصومات إضافية.',
),
        'يفترض ثبات سعر السيارة وعدم حدوث تضخم مفاجئ في الموديلات القادمة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل من الأفضل شراء سيارة كاش أم بالتقسيط؟',
    'a' => 'الشراء كاش يوفر فوائد التمويل والتأمين الشامل الإجباري المرهق، ولكن إذا كان لديك فرصة استثمارية تحقق عائداً أعلى من فائدة قرض السيارة فالتقسيط قد يكون خياراً منطقياً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'monthly-car-cost-calculator',
  1 => 'savings-goal-calculator',
  2 => 'real-car-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const price = Math.max(5000, parseFloat(document.getElementById('targetCarPrice').value) || 65000);
            const current = Math.max(0, parseFloat(document.getElementById('currentCarSavings').value) || 10000);
            const pace = Math.max(100, parseFloat(document.getElementById('monthlyCarSavingsPace').value) || 2000);
            const strategy = document.getElementById('purchaseStrategy').value;
            const curr = getSelectedCurrency();

            let targetAmountNeeded = price;
            if (strategy === 'downpayment_20') targetAmountNeeded = price * 0.20;
            if (strategy === 'downpayment_50') targetAmountNeeded = price * 0.50;

            const remainingToSave = Math.max(0, targetAmountNeeded - current);
            const monthsNeeded = Math.ceil(remainingToSave / pace);

            const targetDate = new Date();
            targetDate.setMonth(targetDate.getMonth() + monthsNeeded);
            const dateStr = targetDate.toLocaleDateString('ar-EG', { year: 'numeric', month: 'long' });

            setPrimaryResult(monthsNeeded + ' شهراً (' + dateStr + ')', 'الموعد المتوقع لشراء السيارة');
            showResultArea();

            setDetailStats([
                { label: 'المبلغ المطلوب تجميعه', value: formatMoney(targetAmountNeeded, curr), color: '#3b82f6' },
                { label: 'المبلغ المتبقي للادخار', value: formatMoney(remainingToSave, curr), color: '#ef4444' },
                { label: 'الادخار الشهري المعتمد', value: formatMoney(pace, curr) + ' / شهر', color: '#10b981' },
                { label: 'طريقة الشراء المحددة', value: strategy === 'cash' ? 'كاش بالكامل' : (strategy === 'downpayment_20' ? 'دفعة أولى 20%' : 'دفعة أولى 50%'), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتوفير مبلغ <strong>${formatMoney(targetAmountNeeded, curr)}</strong> لشراء السيارة بمعدل ادخار <strong>${formatMoney(pace, curr)} شهرياً</strong>، ستكون جاهزاً للشراء خلال <strong>${monthsNeeded} شهراً</strong> بحلول <strong>${dateStr}</strong>.</p>
            `);
        
        saveLastInputs('when-can-i-buy-car-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input, .tool-card textarea').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('when-can-i-buy-car-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>