<?php
/**
 * أداة: حاسبة استهلاك السخان الكهربائي للماء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'water-heater-consumption-calculator';
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
        <label class="form-label" for="heaterCapacity">سعة خزان السخان (باللتر)</label>
        <select id="heaterCapacity" class="form-control" onchange="calculateTool()">
            <option value="50" >سخان 50 لتر (1200 واط - لشخص إلى شخصين)</option>
            <option value="80" selected>سخان 80 لتر (1500 واط - لأسرة متوسطة)</option>
            <option value="100" >سخان 100 لتر (2000 واط - سعة كبيرة)</option>
            <option value="instant" >سخان فوري بدون خزان (Tankless Instant) ~ 6000 إلى 8000 واط</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="heaterOperatingHours">ساعات عمل الهيتر الفعلي يومياً (إعادة التسخين)</label>
        <input type="number" id="heaterOperatingHours" class="form-control" value="3.5" min="0.5" max="12" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="heaterKwhPrice">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="heaterKwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'سخان الخزان يفقد حرارة باستمرار من خلال الجدران ويعيد التسخين حتى لو لم يُستخدم الماء (Standby Heat Loss).',
  1 => 'ضبط ترموستات السخان عند 60 درجة مئوية يوفر الأمان ويمنع ترسب الأملاح الكلسية ويوفر الطاقة.',
),
        'استهلاك الشتاء يكون أعلى بسبب انخفاض درجة حرارة مياه الشبكة القادمة من الخزانات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل من الأفضل ترك السخان يعمل 24 ساعة أم تشغيله قبل الاستخدام فقط؟',
    'a' => 'إذا كان السخان معزولاً جيداً وتستخدمه الأسرة دورياً فالأفضل تركه على الترموستات 60°، أما إذا كان الاستخدام قليلاً فالأفضل تشغيله قبل الاستحمام بساعة عبر مؤقت ذكي (Smart Plug).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'electricity-consumption-calculator',
  1 => 'washing-machine-consumption-calculator',
  2 => 'solar-panels-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cap = document.getElementById('heaterCapacity').value;
            const hours = Math.max(0.5, Math.min(12, parseFloat(document.getElementById('heaterOperatingHours').value) || 3.5));
            const price = Math.max(0.01, parseFloat(document.getElementById('heaterKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let watts = 1500;
            if (cap === '50') watts = 1200;
            if (cap === '100') watts = 2000;
            if (cap === 'instant') watts = 6500;

            let actualHours = hours;
            if (cap === 'instant') actualHours = 0.5; // الفوري يعمل فقط وقت الاستحمام

            const dailyKwh = (watts * actualHours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة سخان الماء شهرياً في الشتاء');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري بالكيلوواط ساعة', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'قدرة عنصر التسخين (الهيتر)', value: watts + ' واط', color: '#f59e0b' },
                { label: 'تكلفة الاستهلاك لموسم الشتاء (4 أشهر)', value: formatMoney(monthlyCost * 4, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>سخان بقدرة <strong>${watts} واط</strong> يعمل بمعدل <strong>${actualHours} ساعات تسخين يومياً</strong> يستهلك <strong>${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة تقدر بـ <strong>${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('water-heater-consumption-calculator');
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
    restoreLastInputs('water-heater-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>