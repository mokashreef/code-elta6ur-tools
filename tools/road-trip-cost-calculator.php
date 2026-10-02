<?php
/**
 * أداة: حاسبة تكلفة الرحلة بالسيارة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'road-trip-cost-calculator';
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
        <label class="form-label" for="oneWayDistanceKm">المسافة باتجاه واحد (كم)</label>
        <input type="number" id="oneWayDistanceKm" class="form-control" value="450" min="10"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="isRoundTrip">نوع الرحلة</label>
        <select id="isRoundTrip" class="form-control" onchange="calculateTool()">
            <option value="round" selected>ذهاب وعودة (مضاعفة المسافة 2x)</option>
            <option value="oneway" >اتجاه واحد فقط</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="roadFuelRate">استهلاك السيارة على الخطوط السريعة (لتر/100 كم) - عادة 7 إلى 9</label>
        <input type="number" id="roadFuelRate" class="form-control" value="8.0" min="3" max="20" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roadFuelPrice">سعر لتر الوقود</label>
        <input type="number" id="roadFuelPrice" class="form-control" value="2.18" min="0.1"  step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tollsAndFees">رسوم الطرق وبوابات العبور (Tolls)</label>
        <input type="number" id="tollsAndFees" class="form-control" value="0" min="0"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="snacksAndMeals">وجبات واستراحات الطريق والمشروبات</label>
        <input type="number" id="snacksAndMeals" class="form-control" value="120" min="0"  step="20"  oninput="calculateTool()">
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
  0 => 'القيادة على الطرق السريعة بسرعة معتدلة (100 إلى 120 كم/س) توفر ما يصل إلى 20% من استهلاك الوقود مقارنة بالسرعات العالية (140+ كم/س).',
  1 => 'التأكد من ضغط الإطارات المناسب قبل السفر يوفر الوقود ويضمن أعلى درجات السلامة.',
),
        'يفترض طريقاً سريعاً مفتوحاً مع ثبات نسبي للسرعة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أوفر: السفر بالسيارة أم الطيران لأسرة من 4 أفراد؟',
    'a' => 'السفر بالسيارة لرحلات تقل عن 800 كم يكون أرخص بنسبة تتجاوز 60% إلى 70% للعائلات مقارنة بتذاكر الطيران، بالإضافة لتوفير تكلفة استئجار سيارة في وجهة الوصول.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'fuel-by-distance-calculator',
  1 => 'travel-cost-calculator',
  2 => 'monthly-gas-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const oneWay = Math.max(10, parseFloat(document.getElementById('oneWayDistanceKm').value) || 450);
            const isRound = document.getElementById('isRoundTrip').value === 'round';
            const rate = Math.max(3, parseFloat(document.getElementById('roadFuelRate').value) || 8.0);
            const price = Math.max(0.1, parseFloat(document.getElementById('roadFuelPrice').value) || 2.18);
            const tolls = Math.max(0, parseFloat(document.getElementById('tollsAndFees').value) || 0);
            const meals = Math.max(0, parseFloat(document.getElementById('snacksAndMeals').value) || 120);
            const curr = getSelectedCurrency();

            const totalKm = isRound ? oneWay * 2 : oneWay;
            const totalLiters = (totalKm / 100) * rate;
            const totalFuelCost = totalLiters * price;
            const grandTotalCost = totalFuelCost + tolls + meals;

            setPrimaryResult(formatMoney(grandTotalCost, curr), 'إجمالي التكلفة المقدرة للرحلة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المسافة المقطوعة', value: totalKm.toLocaleString() + ' كم', color: '#3b82f6' },
                { label: 'تكلفة الوقود والبنزين', value: formatMoney(totalFuelCost, curr), color: '#10b981' },
                { label: 'كمية البنزين المطلوبة', value: totalLiters.toFixed(1) + ' لتر', color: '#f59e0b' },
                { label: 'الوجبات ورسوم الطرق', value: formatMoney(tolls + meals, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للقيام برحلة مسافتها <strong>${totalKm.toLocaleString()} كم</strong> ${isRound ? '(ذهاباً وإياباً)' : '(اتجاه واحد)'}، تحتاج إلى حوالي <strong>${totalLiters.toFixed(1)} لتر بنزين</strong> بتكلفة وقود <strong>${formatMoney(totalFuelCost, curr)}</strong>، وإجمالي تكلفة شاملة الوجبات <strong>${formatMoney(grandTotalCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('road-trip-cost-calculator');
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
    restoreLastInputs('road-trip-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>