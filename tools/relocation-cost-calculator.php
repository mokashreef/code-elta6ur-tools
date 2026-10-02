<?php
/**
 * أداة: حاسبة تكلفة الانتقال لدولة أو مدينة أخرى
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'relocation-cost-calculator';
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
        <label class="form-label" for="targetCityRent">الإيجار الشهري المتوقع في المدينة الجديدة</label>
        <input type="number" id="targetCityRent" class="form-control" value="3200" min="500"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shippingLuggageCost">تكاليف شحن العفش أو الأمتعة الإضافية والسيارة</label>
        <input type="number" id="shippingLuggageCost" class="form-control" value="2500" min="0"  step="200"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="temporaryStayDays">أيام الإقامة المؤقتة في فندق/Airbnb للبحث عن سكن</label>
        <input type="number" id="temporaryStayDays" class="form-control" value="14" min="0" max="60" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tempStayNightRate">سعر الليلة في الإقامة المؤقتة</label>
        <input type="number" id="tempStayNightRate" class="form-control" value="250" min="50"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="newSetupAllowance">مخصص شراء مستلزمات وبداية المعيشة والتأمين</label>
        <input type="number" id="newSetupAllowance" class="form-control" value="4000" min="500"  step="500"  oninput="calculateTool()">
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
  0 => 'الانتقال دائماً يحمل مصاريف مفاجئة كشراء أدوات المطبخ والإنترنت ودفع اشتراكات جديدة.',
  1 => 'حجز سكن مؤقت لمدة أسبوعين يمنحك الفرصة لمعاينة الأحياء والمدارس بنفسك قبل توقيع عقد إيجار سنوي ملزم.',
),
        'يفترض الحصول على السكن الدائم خلال فترة الإقامة المؤقتة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الأفضل شحن الأثاث القديم أم بيعه وشراء أثاث جديد؟',
    'a' => 'إذا كانت مسافة الانتقال بعيدة أو بين دول مختلفة، فبيع الأثاث وشراء أثاث جديد من المدينة الجديدة يكون أوفر بنسبة كبيرة جداً ويوفر مخاطر كسر وتلف العفش أثناء النقل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'immigration-cost-calculator',
  1 => 'cost-of-living-calculator',
  2 => 'city-income-requirement-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rent = Math.max(500, parseFloat(document.getElementById('targetCityRent').value) || 3200);
            const shipping = Math.max(0, parseFloat(document.getElementById('shippingLuggageCost').value) || 2500);
            const stayDays = Math.max(0, parseInt(document.getElementById('temporaryStayDays').value) || 14);
            const nightRate = Math.max(50, parseFloat(document.getElementById('tempStayNightRate').value) || 250);
            const setup = Math.max(500, parseFloat(document.getElementById('newSetupAllowance').value) || 4000);
            const curr = getSelectedCurrency();

            const tempStayCost = stayDays * nightRate;
            const upfrontRentDeposit = rent * 2; // إيجار أول شهر + تأمين
            const totalRelocation = shipping + tempStayCost + setup + upfrontRentDeposit;

            setPrimaryResult(formatMoney(totalRelocation, curr), 'الميزانية التقديرية الإجمالية للانتقال');
            showResultArea();

            setDetailStats([
                { label: 'حجز الإقامة المؤقتة (' + stayDays + ' يوماً)', value: formatMoney(tempStayCost, curr), color: '#3b82f6' },
                { label: 'إيجار أول شهر وتأمين السكن الجديد', value: formatMoney(upfrontRentDeposit, curr), color: '#10b981' },
                { label: 'شحن الأمتعة والمقتنيات', value: formatMoney(shipping, curr), color: '#f59e0b' },
                { label: 'تجهيزات البداية والرسوم الإدارية', value: formatMoney(setup, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلف الانتقال وبدء الاستقرار في المدينة الجديدة حوالي <strong>${formatMoney(totalRelocation, curr)}</strong> تشمل الإقامة المؤقتة وشحن الأمتعة وتأمين أول شهرين للسكن الجديد.</p>
            `);
        
        saveLastInputs('relocation-cost-calculator');
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
    restoreLastInputs('relocation-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>