<?php
/**
 * أداة: حاسبة المسافة والوقود (كم تقطع السيارة بالتانكي؟)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'distance-fuel-calculator';
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
    <div class="form-group">
        <label class="form-label" for="fuelTankLiters">كمية الوقود المتوفرة أو سعة التانكي (باللتر)</label>
        <input type="number" id="fuelTankLiters" class="form-control" value="55" min="5" max="200" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="consumptionRateL100">معدل استهلاك الوقود (لتر لكل 100 كم)</label>
        <input type="number" id="consumptionRateL100" class="form-control" value="8.0" min="3" max="25" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="reserveSafeMargin">ترك وقود احتياطي في التانكي للطوارئ (لتر)</label>
        <input type="number" id="reserveSafeMargin" class="form-control" value="5" min="0" max="20" step="1"  oninput="calculateTool()">
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
  0 => 'المسافة المقطوعة (كم) = (كمية الوقود المتاحة باللتر ÷ معدل الاستهلاك) × 100.',
  1 => 'القيادة حتى جفاف التانكي تماماً يتلف طلمبة الوقود (طرمبة البنزين) لأنها تعتمد على غمرها بالبنزين لتبريدها.',
),
        'يفترض عدم وجود تسريب وقود وضغط إطارات سليم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم كيلومتر تمشي السيارة بعد إضاءة لمبة البنزين؟',
    'a' => 'في معظم السيارات الحديثة، تحتوي لمبة البنزين على احتياطي يتراوح بين 7 إلى 10 لترات، وهو ما يكفي لقطع مسافة 50 إلى 80 كم تقريباً للوصول لأقرب محطة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'fuel-by-distance-calculator',
  1 => 'car-consumption-calculator',
  2 => 'monthly-gas-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const tank = Math.max(5, parseFloat(document.getElementById('fuelTankLiters').value) || 55);
            const rate = Math.max(3, parseFloat(document.getElementById('consumptionRateL100').value) || 8.0);
            const reserve = Math.max(0, parseFloat(document.getElementById('reserveSafeMargin').value) || 5);

            const usableFuel = Math.max(1, tank - reserve);
            const totalDistance = (usableFuel / rate) * 100;
            const fullTankDistance = (tank / rate) * 100;
            const kmPerLiter = 100 / rate;

            setPrimaryResult(Math.round(totalDistance).toLocaleString() + ' كم', 'المسافة الآمنة التي تقطعها السيارة');
            showResultArea();

            setDetailStats([
                { label: 'المدى الآمن قبل إضاءة لمبة البنزين', value: Math.round(totalDistance) + ' كم', color: '#10b981' },
                { label: 'المدى الأقصى النظري حتى جفاف التانكي', value: Math.round(fullTankDistance) + ' كم', color: '#3b82f6' },
                { label: 'كفاءة اللتر الواحد', value: kmPerLiter.toFixed(1) + ' كم / لتر', color: '#f59e0b' },
                { label: 'كمية الوقود القابلة للاستخدام', value: usableFuel + ' لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بكمية وقود <strong>${tank} لتر</strong> واحتياطي أمان <strong>${reserve} لتر</strong>، تستطيع سيارتك قطع <strong>${Math.round(totalDistance)} كم</strong> بأمان قبل الحاجة للوقوف عند محطة وقود.</p>
            `);
        
        saveLastInputs('distance-fuel-calculator');
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
    restoreLastInputs('distance-fuel-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>