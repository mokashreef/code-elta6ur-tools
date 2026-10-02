<?php
/**
 * أداة: حاسبة تكلفة تشغيل المولد
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'generator-cost-calculator';
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
        <label class="form-label" for="genFuelPerHour">استهلاك المولد من الوقود (لتر في الساعة)</label>
        <input type="number" id="genFuelPerHour" class="form-control" value="3.5" min="0.2"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelLiterPrice">سعر لتر الوقود</label>
        <input type="number" id="fuelLiterPrice" class="form-control" value="0.80" min="0.05"  step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyHoursGenCost">ساعات التشغيل اليومية</label>
        <input type="number" id="dailyHoursGenCost" class="form-control" value="8" min="1" max="24" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="oilChangeCostPer100h">تكلفة غيار الزيت والفلاتر الدورية (كل 100 ساعة تشغيل)</label>
        <input type="number" id="oilChangeCostPer100h" class="form-control" value="35" min="0"  step="5"  oninput="calculateTool()">
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
  0 => 'تكلفة المولد لا تقتصر على الوقود فقط؛ فتكاليف استهلاك الزيت والفلاتر والإهلاك تمثل بين 15% إلى 25% من تكلفة التشغيل الحقيقية.',
  1 => 'زيت المولد يتطلب تغييراً دورياً كل 100 إلى 150 ساعة تشغيل لحماية بساتم المحرك من الاحتراق.',
),
        'يفترض أسعار وقود وزيوت محلية مطابقة للمدخلات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أرخص لتشغيل المنزل: المولد أم منظومة الطاقة الشمسية؟',
    'a' => 'على المدى المتوسط (سنتين فأكثر)، تكون الطاقة الشمسية مع البطاريات أرخص بكثير؛ لأن تكلفة وقود وصيانة المولد شهرياً تتجاوز قيمة شراء الألواح الشمسية في فترة وجيزة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'generator-fuel-calculator',
  1 => 'generator-capacity-calculator',
  2 => 'power-source-comparison-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const lph = Math.max(0.2, parseFloat(document.getElementById('genFuelPerHour').value) || 3.5);
            const literPrice = Math.max(0.05, parseFloat(document.getElementById('fuelLiterPrice').value) || 0.80);
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('dailyHoursGenCost').value) || 8));
            const oilMaint = Math.max(0, parseFloat(document.getElementById('oilChangeCostPer100h').value) || 35);
            const curr = getSelectedCurrency();

            const hourlyFuelCost = lph * literPrice;
            const hourlyMaintCost = oilMaint / 100;
            const totalHourlyCost = hourlyFuelCost + hourlyMaintCost;

            const dailyCost = totalHourlyCost * hours;
            const monthlyCost = dailyCost * 30;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'إجمالي تكلفة تشغيل المولد شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الساعة الواحدة (وقود وصيانة)', value: formatMoney(totalHourlyCost, curr), color: '#3b82f6' },
                { label: 'تكلفة الوقود اليومية', value: formatMoney(hourlyFuelCost * hours, curr), color: '#10b981' },
                { label: 'تكلفة الصيانة والزيوت شهرياً', value: formatMoney(hourlyMaintCost * hours * 30, curr), color: '#f59e0b' },
                { label: 'استهلاك الوقود الشهري باللتر', value: (lph * hours * 30).toFixed(0) + ' لتر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يكلفك تشغيل المولد <strong>${formatMoney(totalHourlyCost, curr)} لكل ساعة</strong>. تبلغ الفاتورة الشهرية <strong>${formatMoney(monthlyCost, curr)}</strong> شاملة الوقود وغيارات الزيت والفلاتر الدورية.</p>
            `);
        
        saveLastInputs('generator-cost-calculator');
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
    restoreLastInputs('generator-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>