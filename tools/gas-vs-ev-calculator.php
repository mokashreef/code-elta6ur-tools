<?php
/**
 * أداة: حاسبة سيارة بنزين مقابل سيارة كهربائية (EV)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'gas-vs-ev-calculator';
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
        <label class="form-label" for="yearlyKmCompare">المسافة السنوية المقطوعة (كم)</label>
        <input type="number" id="yearlyKmCompare" class="form-control" value="25000" min="5000"  step="1000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gasCarL100">استهلاك سيارة البنزين (لتر/100 كم)</label>
        <input type="number" id="gasCarL100" class="form-control" value="8.5" min="4" max="20" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gasLiterCostComp">سعر لتر البنزين</label>
        <input type="number" id="gasLiterCostComp" class="form-control" value="2.18" min="0.1"  step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="evKwhPer100">استهلاك السيارة الكهربائية (kWh لكل 100 كم) - المعتاد 16 إلى 20</label>
        <input type="number" id="evKwhPer100" class="form-control" value="17.5" min="10" max="30" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="evHomeKwhCost">سعر كيلوواط الكهرباء للشحن المنزلي (kWh)</label>
        <input type="number" id="evHomeKwhCost" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'السيارات الكهربائية توفر ما بين 60% إلى 80% من تكلفة الوقود عند الشحن المنزلي الرخيص.',
  1 => 'محرك السيارة الكهربائية يحتوي على حوالي 20 قطعة متحركة فقط مقابل أكثر من 2000 قطعة في محرك الاحتراق الداخلي، مما يلغي تماماً مصاريف غيار الزيوت، الفلاتر، البواجي، وسيور التيمن.',
),
        'يفترض شحن منزلي بمعظم الوقت (الشواحن السريعة العامة تكون تكلفتها أعلى قليلاً).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا عن تكلفة تغيير بطارية السيارة الكهربائية؟',
    'a' => 'معظم بطاريات السيارات الكهربائية الحديثة تضمنها الشركات المصنعة لمدة 8 سنوات أو 160,000 كم، وتشير الدراسات الواقعية إلى أن البطاريات تفقد أقل من 10% إلى 15% من سعتها بعد قطع أكثر من 250,000 كم.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'real-car-cost-calculator',
  1 => 'car-consumption-calculator',
  2 => 'monthly-gas-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const km = Math.max(5000, parseFloat(document.getElementById('yearlyKmCompare').value) || 25000);
            const gasRate = Math.max(4, parseFloat(document.getElementById('gasCarL100').value) || 8.5);
            const gasPrice = Math.max(0.1, parseFloat(document.getElementById('gasLiterCostComp').value) || 2.18);
            const evRate = Math.max(10, parseFloat(document.getElementById('evKwhPer100').value) || 17.5);
            const evPrice = Math.max(0.01, parseFloat(document.getElementById('evHomeKwhCost').value) || 0.18);
            const curr = getSelectedCurrency();

            // 1. تكلفة وقود البنزين سنوياً
            const annualGasLiters = (km / 100) * gasRate;
            const annualGasCost = annualGasLiters * gasPrice;
            const gasMaintAnnual = 2500; // غيار زيوت وبواجي وفلاتر

            // 2. تكلفة كهرباء شحن EV سنوياً
            const annualEvKwh = (km / 100) * evRate;
            const annualEvCost = annualEvKwh * evPrice;
            const evMaintAnnual = 800; // صيانة منخفضة جداً بدون زيوت ومكابس

            const totalGasYear = annualGasCost + gasMaintAnnual;
            const totalEvYear = annualEvCost + evMaintAnnual;
            const annualSavings = totalGasYear - totalEvYear;

            setPrimaryResult(formatMoney(annualSavings, curr) + ' وفر سنوي', 'الوفر السنوي لصالح السيارة الكهربائية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة وقود البنزين والصيانة سنوياً', value: formatMoney(totalGasYear, curr), color: '#ef4444' },
                { label: 'تكلفة شحن الكهرباء والصيانة سنوياً', value: formatMoney(totalEvYear, curr), color: '#10b981' },
                { label: 'نسبة توفير الطاقة مع السيارة الكهربائية', value: (((totalGasYear - totalEvYear) / totalGasYear) * 100).toFixed(0) + '% وفر', color: '#3b82f6' },
                { label: 'الوفر التراكمي على مدى 5 سنوات', value: formatMoney(annualSavings * 5, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لقطع مسافة <strong>${km.toLocaleString()} كم سنوياً</strong>، تدفع لسيارة البنزين <strong>${formatMoney(totalGasYear, curr)}</strong> سنوياً مقابل <strong>${formatMoney(totalEvYear, curr)}</strong> للسيارة الكهربائية، محققاً وفراً مذهلاً قدره <strong>${formatMoney(annualSavings, curr)} سنوياً</strong> في مصاريف التشغيل والصيانة.</p>
            `);
        
        saveLastInputs('gas-vs-ev-calculator');
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
    restoreLastInputs('gas-vs-ev-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>