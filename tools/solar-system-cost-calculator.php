<?php
/**
 * أداة: حاسبة تكلفة المنظومة الشمسية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-system-cost-calculator';
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
        <label class="form-label" for="solarSysType">نوع المنظومة الشمسية</label>
        <select id="solarSysType" class="form-control" onchange="calculateTool()">
            <option value="ongrid" >منظومة متصلة بالشبكة الحكومية بدون بطاريات (On-Grid)</option>
            <option value="hybrid_lithium" selected>منظومة هجينة متطورة مع بطاريات ليثيوم (Hybrid + LiFePO4)</option>
            <option value="offgrid" >منظومة معزولة تماماً عن الشبكة مع بطاريات جيل (Off-Grid)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="systemCapKw">حجم المنظومة بالكيلوواط (kW)</label>
        <input type="number" id="systemCapKw" class="form-control" value="6.0" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="batteryCapacityKwh">سعة البطاريات المطلوبة (كيلوواط ساعة kWh) - إذا كانت مع بطاريات</label>
        <input type="number" id="batteryCapacityKwh" class="form-control" value="10" min="0"  step="5"  oninput="calculateTool()">
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
  0 => 'المنظومات المربوطة بالشبكة (On-Grid) هي الأقل تكلفة لأنها لا تحتوي على بطاريات وتبيع الفائض لشركة الكهرباء.',
  1 => 'البطاريات تمثل بين 35% إلى 50% من تكلفة المنظومات الهجينة والمعزولة ولكنها توفر كهرباء مستمرة 24/7 دون انقطاع.',
),
        'الأسعار مبنية على متوسط أسعار السوق للطاقة الشمسية للعام 2025/2026.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم تبلغ مدة ضمان الألواح والانفرتر؟',
    'a' => 'الألواح الشمسية ذات الجودة العالية تأتي بضمان أداء كفاءة لمدة 25 إلى 30 سنة، بينما الانفرترات تأتي بضمان 5 سنوات، وبطاريات الليثيوم 5 إلى 10 سنوات.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-payback-calculator',
  1 => 'grid-vs-solar-calculator',
  2 => 'solar-panels-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const type = document.getElementById('solarSysType').value;
            const kw = Math.max(1, parseFloat(document.getElementById('systemCapKw').value) || 6.0);
            const battKwh = Math.max(0, parseFloat(document.getElementById('batteryCapacityKwh').value) || 10);
            const curr = getSelectedCurrency();

            // تكلفة الكيلوواط ألواح مع الشاسيهات والأسلاك والتركيب حوالي 350-450 دولار
            const solarPanelsAndMountingCost = kw * 400;
            // تكلفة الانفرتر
            let inverterCost = 600;
            if (type === 'hybrid_lithium') inverterCost = 1300;
            if (type === 'ongrid') inverterCost = 800;

            // تكلفة البطاريات
            let batteryCost = 0;
            if (type === 'hybrid_lithium') {
                batteryCost = battKwh * 250; // سعر كيلوواط الليثيوم مع BMS
            } else if (type === 'offgrid') {
                batteryCost = battKwh * 160; // سعر بطاريات الجيل
            }

            const laborAndPermits = 400;
            const totalUSD = solarPanelsAndMountingCost + inverterCost + batteryCost + laborAndPermits;
            // التحويل للعملة
            const total = totalUSD;

            setPrimaryResult(formatMoney(total, curr), 'التكلفة الإجمالية التقديرية للمنظومة مع التركيب');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة الألواح والهياكل المعدنية', value: formatMoney(solarPanelsAndMountingCost, curr), color: '#3b82f6' },
                { label: 'تكلفة بنك البطاريات (' + battKwh + ' kWh)', value: formatMoney(batteryCost, curr), color: '#10b981' },
                { label: 'تكلفة الانفرتر الذكي ولوحة القواطع', value: formatMoney(inverterCost, curr), color: '#f59e0b' },
                { label: 'أجور التركيب والكابلات والحماية', value: formatMoney(laborAndPermits, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفة تركيب منظومة <strong>${kw} kW</strong> من نوع <strong>${type === 'ongrid' ? 'متصلة بالشبكة' : 'هجينة مع بطاريات'}</strong> تبلغ حوالي <strong>${formatMoney(total, curr)}</strong> شاملة كافة المعدات والتوصيلات وضمان الألواح لمدة 25 سنة.</p>
            `);
        
        saveLastInputs('solar-system-cost-calculator');
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
    restoreLastInputs('solar-system-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>