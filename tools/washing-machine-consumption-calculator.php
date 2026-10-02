<?php
/**
 * أداة: حاسبة استهلاك الغسالة للكهرباء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'washing-machine-consumption-calculator';
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
        <label class="form-label" for="washTempMode">درجة حرارة مياه الغسيل</label>
        <select id="washTempMode" class="form-control" onchange="calculateTool()">
            <option value="cold" >غسيل بماء بارد (بدون سخان كهربائي) ~ 0.25 kWh للدورة</option>
            <option value="warm40" selected>غسيل بماء دافئ 40 درجة مئوية ~ 0.70 kWh للدورة</option>
            <option value="hot60" >غسيل بماء ساخن 60-90 درجة (تعقيم كامل) ~ 1.80 kWh للدورة</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="washesPerWeek">عدد دورات الغسيل في الأسبوع</label>
        <input type="number" id="washesPerWeek" class="form-control" value="5" min="1" max="30" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="washKwhPrice">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="washKwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'أكثر من 85% إلى 90% من استهلاك الغسالة للكهرباء يذهب لتسخين المياه عبر السخان المدمج (Heater).',
  1 => 'الغسيل بماء بارد (30 درجة أو أقل) يوفر حتى 70% من كهرباء الغسالة مع الحفاظ على نظافة الملابس باستخدام مساحيق حديثة.',
),
        'القيم لغسالة أوتوماتيك أمامية قياسية سعة 7 إلى 9 كجم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الغسالة ذات الفتحة العلوية تستهلك كهرباء أقل؟',
    'a' => 'الغسالات العلوية لا تحتوي غالباً على سخان مدمج وتسحب ماء ساخناً من سخان المنزل، لذلك تستهلك كهرباء مباشرة أقل ولكنها تستهلك كمية أكبر من المياه.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'water-heater-consumption-calculator',
  1 => 'electricity-consumption-calculator',
  2 => 'fridge-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const temp = document.getElementById('washTempMode').value;
            const washes = Math.max(1, parseInt(document.getElementById('washesPerWeek').value) || 5);
            const price = Math.max(0.01, parseFloat(document.getElementById('washKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let kwhPerCycle = 0.70;
            if (temp === 'cold') kwhPerCycle = 0.25;
            if (temp === 'hot60') kwhPerCycle = 1.80;

            const monthlyCycles = washes * 4.33;
            const monthlyKwh = monthlyCycles * kwhPerCycle;
            const monthlyCost = monthlyKwh * price;
            const costPerCycle = kwhPerCycle * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الغسالة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة دورة الغسيل الواحدة', value: formatMoney(costPerCycle, curr), color: '#3b82f6' },
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'عدد دورات الغسيل شهرياً', value: Math.round(monthlyCycles) + ' غسلة', color: '#f59e0b' },
                { label: 'استهلاك الدورة الواحدة', value: kwhPerCycle.toFixed(2) + ' kWh', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشغيل الغسالة بمعدل <strong>${washes} غسلات أسبوعياً</strong> يستهلك حوالي <strong>${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة <strong>${formatMoney(monthlyCost, curr)}</strong>. معظم استهلاك الغسالة يذهب لتسخين المياه وليس لتحريك الحلة.']</p>
            `);
        
        saveLastInputs('washing-machine-consumption-calculator');
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
    restoreLastInputs('washing-machine-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>