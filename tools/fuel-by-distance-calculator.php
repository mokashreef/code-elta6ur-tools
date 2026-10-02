<?php
/**
 * أداة: حاسبة الوقود حسب المسافة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'fuel-by-distance-calculator';
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
        <label class="form-label" for="tripDistanceKm">المسافة المراد قطعها (بالكيلومتر)</label>
        <input type="number" id="tripDistanceKm" class="form-control" value="320" min="1"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelConsumptionLiters100">معدل استهلاك سيارتك (لتر لكل 100 كم)</label>
        <input type="number" id="fuelConsumptionLiters100" class="form-control" value="8.5" min="3" max="25" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelUnitPrice">سعر لتر الوقود</label>
        <input type="number" id="fuelUnitPrice" class="form-control" value="2.18" min="0.1"  step="0.05"  oninput="calculateTool()">
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
  0 => 'كمية الوقود (لتر) = (المسافة بالكم ÷ 100) × معدل الاستهلاك.',
  1 => 'التكلفة = كمية الوقود × سعر لتر البنزين.',
),
        'الاستهلاك محسوب على متوسط كفاءة محرك السيارة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحسن كفاءة استهلاك الوقود في الرحلات الطويلة؟',
    'a' => 'استخدم مثبت السرعة (Cruise Control) على الطرق السريعة المستوية، وتجنب التسارع والفرملة المفاجئة، وتأكد من صيانة البواجي وفلتر الهواء.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'distance-fuel-calculator',
  1 => 'monthly-gas-cost-calculator',
  2 => 'car-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const km = Math.max(1, parseFloat(document.getElementById('tripDistanceKm').value) || 320);
            const rate = Math.max(3, parseFloat(document.getElementById('fuelConsumptionLiters100').value) || 8.5);
            const price = Math.max(0.1, parseFloat(document.getElementById('fuelUnitPrice').value) || 2.18);
            const curr = getSelectedCurrency();

            const litersNeeded = (km / 100) * rate;
            const tripCost = litersNeeded * price;
            const kmCost = tripCost / km;

            setPrimaryResult(litersNeeded.toFixed(1) + ' لتر بنزين (' + formatMoney(tripCost, curr) + ')', 'كمية وتكلفة الوقود المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الرحلة الإجمالية بالوقود', value: formatMoney(tripCost, curr), color: '#3b82f6' },
                { label: 'كمية الوقود المطلوبة', value: litersNeeded.toFixed(1) + ' لتر', color: '#10b981' },
                { label: 'تكلفة الكيلومتر الواحد', value: formatMoney(kmCost, curr), color: '#f59e0b' },
                { label: 'عدد الكيلومترات المقطوعة باللتر الواحد', value: (100 / rate).toFixed(1) + ' كم / لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>${km} كم</strong>، تحتاج سيارتك إلى <strong>${litersNeeded.toFixed(1)} لتر</strong> من الوقود بتكلفة <strong>${formatMoney(tripCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('fuel-by-distance-calculator');
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
    restoreLastInputs('fuel-by-distance-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>