<?php
/**
 * أداة: حاسبة إنتاج الألواح الشمسية السنوي والشهري
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-yield-calculator';
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
        <label class="form-label" for="systemSizeKw">حجم المنظومة الشمسية بالكيلوواط (kWp)</label>
        <input type="number" id="systemSizeKw" class="form-control" value="8.0" min="0.5"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tiltAngleOrientation">زاوية التوجيه والميل</label>
        <select id="tiltAngleOrientation" class="form-control" onchange="calculateTool()">
            <option value="optimal" selected>توجيه مثالي نحو الجنوب مع زاوية ميل مثالية (100% كفاءة)</option>
            <option value="east_west" >توجيه شرق - غرب (إنتاج ممتد مع فاقد 12%)</option>
            <option value="flat" >ألواح أفقية مسطحة تماماً بدون ميل (فاقد 15% وصعوبة تنظيف)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="yearlySunIndex">معدل الإنتاج النوعي للمنطقة (kWh / kWp سنوياً) - المعتاد عربياً 1650 إلى 1850</label>
        <input type="number" id="yearlySunIndex" class="form-control" value="1750" min="1200" max="2200" step="50"  oninput="calculateTool()">
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
  0 => 'الإنتاجية النوعية (Specific Yield) في المنطقة العربية تتراوح بين 1600 إلى 1900 كيلوواط ساعة لكل 1 كيلوواط من الألواح سنوياً.',
  1 => 'التوجيه جنوباً بزاوية 25°-30° يضمن تعامداً أمثل لأشعة الشمس واستغلالاً كاملاً لذروة الإنتاج.',
),
        'يفترض تنظيف دوري للألواح من الغبار كل أسبوعين إلى شهر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم تبلغ نسبة انخفاض إنتاج الألواح بسبب الغبار؟',
    'a' => 'تراكم الغبار في المناطق الصحراوية والجافة قد يخفض إنتاج الألواح بنسبة تتراوح بين 10% إلى 25% إذا لم يتم تنظيفها بانتظام بالماء النظيف وممسحة السيليكون.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-panels-calculator',
  1 => 'solar-payback-calculator',
  2 => 'grid-vs-solar-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kw = Math.max(0.5, parseFloat(document.getElementById('systemSizeKw').value) || 8.0);
            const orient = document.getElementById('tiltAngleOrientation').value;
            const index = Math.max(1200, parseFloat(document.getElementById('yearlySunIndex').value) || 1750);

            let factor = 1.0;
            if (orient === 'east_west') factor = 0.88;
            if (orient === 'flat') factor = 0.85;

            const annualKwh = kw * index * factor;
            const monthlyKwhAvg = annualKwh / 12;
            const dailyKwhAvg = annualKwh / 365;

            // وفر انبعاثات الكربون: حوالي 0.65 كجم CO2 لكل كيلوواط ساعة
            const co2SavedTons = (annualKwh * 0.65) / 1000;

            setPrimaryResult(Math.round(annualKwh).toLocaleString() + ' kWh سنوياً', 'إجمالي إنتاج الطاقة الكهربائية المتوقع');
            showResultArea();

            setDetailStats([
                { label: 'متوسط الإنتاج الشهري', value: Math.round(monthlyKwhAvg).toLocaleString() + ' kWh', color: '#3b82f6' },
                { label: 'متوسط الإنتاج اليومي', value: dailyKwhAvg.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'وفر انبعاثات الكربون السنوي', value: co2SavedTons.toFixed(1) + ' طن CO2', color: '#10b981' },
                { label: 'حجم المنظومة المعتمد', value: kw + ' kWp', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>منظومة شمسية بقدرة <strong>${kw} كيلوواط</strong> تنتج حوالي <strong>${Math.round(annualKwh).toLocaleString()} كيلوواط ساعة سنوياً</strong> (بمعدل <strong>${dailyKwhAvg.toFixed(1)} kWh يومياً</strong>)، وتوفر انبعاث <strong>${co2SavedTons.toFixed(1)} طن من غاز الكربون</strong> في الغلاف الجوي سنوياً.</p>
            `);
        
        saveLastInputs('solar-yield-calculator');
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
    restoreLastInputs('solar-yield-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>