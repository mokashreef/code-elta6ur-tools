<?php
/**
 * أداة: حاسبة استهلاك التلفزيون للكهرباء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'tv-power-consumption-calculator';
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
        <label class="form-label" for="tvScreenSize">حجم شاشة التلفزيون وتقنيتها</label>
        <select id="tvScreenSize" class="form-control" onchange="calculateTool()">
            <option value="32_led" >شاشة 32 بوصة LED ~ 35 إلى 45 واط</option>
            <option value="43_led" >شاشة 43 بوصة 4K LED ~ 65 واط</option>
            <option value="55_led" selected>شاشة 55 بوصة 4K LED ~ 95 واط</option>
            <option value="65_led" >شاشة 65 بوصة 4K LED ~ 130 واط</option>
            <option value="65_oled" >شاشة 65 بوصة OLED / QLED فائقة السطوع ~ 170 واط</option>
            <option value="75_plus" >شاشة ضخمة 75-85 بوصة ~ 220 إلى 280 واط</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="tvDailyHours">ساعات المشاهدة والتشغيل يومياً</label>
        <input type="number" id="tvDailyHours" class="form-control" value="6" min="1" max="24" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tvKwhPrice">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="tvKwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'التلفزيونات الحديثة المعتمدة على إضاءة LED اقتصادية جداً في استهلاك الطاقة مقارنة بالمكيفات والسخانات.',
  1 => 'تفعيل خاصية السطوع التلقائي (Eco Sensor) يوفر حوالي 20% من استهلاك الشاشة في الغرف المظلمة ليلاً.',
),
        'يفترض مستوى سطوع قياسي متوسط وتفعيل وضع توفير الطاقة الذكي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل شاشات OLED تستهلك كهرباء أكثر من LED؟',
    'a' => 'شاشات OLED تستهلك طاقة أعلى قليلاً عند عرض مشاهد بيضاء وساطعة بالكامل، لكنها تصبح فائقة التوفير وتطفي البكسلات تماماً عند عرض المشاهد السوداء الداكنة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'pc-power-consumption-calculator',
  1 => 'electricity-consumption-calculator',
  2 => 'device-monthly-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const size = document.getElementById('tvScreenSize').value;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('tvDailyHours').value) || 6));
            const price = Math.max(0.01, parseFloat(document.getElementById('tvKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let watts = 95;
            if (size === '32_led') watts = 40;
            if (size === '43_led') watts = 65;
            if (size === '65_led') watts = 130;
            if (size === '65_oled') watts = 170;
            if (size === '75_plus') watts = 250;

            const dailyKwh = (watts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل التلفزيون شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'قدرة التلفزيون الفعلية', value: watts + ' واط', color: '#f59e0b' },
                { label: 'التكلفة السنوية الإجمالية', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشغيل الشاشة بقدرة <strong>${watts} واط</strong> لمدة <strong>${hours} ساعات يومياً</strong> يستهلك <strong>${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة زهيدة تقدر بـ <strong>${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('tv-power-consumption-calculator');
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
    restoreLastInputs('tv-power-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>