<?php
/**
 * أداة: حاسبة الألواح الشمسية المطلوبة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-panels-calculator';
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
        <label class="form-label" for="dailyKwhConsumption">الاستهلاك اليومي المطلوب توليده (كيلوواط ساعة kWh)</label>
        <input type="number" id="dailyKwhConsumption" class="form-control" value="15" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="peakSunHours">ساعات ذروة الشمس اليومية في منطقتك (Peak Sun Hours) - عربياً 5 إلى 6 ساعات</label>
        <input type="number" id="peakSunHours" class="form-control" value="5.5" min="3" max="8" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="panelWattRating">قدرة اللوح الشمسي الواحد (واط Watt)</label>
        <select id="panelWattRating" class="form-control" onchange="calculateTool()">
            <option value="450" >لوح 450 واط مونو كريستالين</option>
            <option value="550" selected>لوح 550 واط حديث (المقاس الأوسع انتشاراً حالياً)</option>
            <option value="600" >لوح 600 واط تقنية TOPCon / Bifacial</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="systemLossesRate">نسبة الفواقد البيئية والحرارة والتوصيل (%)- عادة 20%</label>
        <input type="number" id="systemLossesRate" class="form-control" value="20" min="10" max="35" step="5"  oninput="calculateTool()">
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
  0 => 'القدرة الإجمالية للمنظومة (واط) = (الاستهلاك اليومي بالواط-ساعة) ÷ (ساعات ذروة الشمس × معامل كفاءة النظام).',
  1 => 'ساعات ذروة الشمس في الدول العربية والشرق الأوسط من بين الأعلى عالمياً وتتراوح بين 5.0 إلى 6.5 ساعة يومياً.',
  2 => 'فواقد النظام تشمل تأثير حرارة الصيف على كفاءة السيليكون، الغبار، وفواقد كابلات التيار المستمر DC والانفرتر.',
),
        'يفترض توجيه الألواح نحو الجنوب الجغرافي بزاوية ميل مطابقة لخط عرض المدينة (بين 25° إلى 32°).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تؤثر الحرارة الشديدة سلباً على كفاءة الألواح الشمسية؟',
    'a' => 'نعم؛ يفقد اللوح الشمسي حوالي 0.35% من قدرته لكل درجة مئوية ترتفع فوق 25°، لذلك فالألواح تنتج في الأيام الربيعية المشمسة المعتدلة طاقة أكبر من أيام الصيف الحارقة جداً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-batteries-calculator',
  1 => 'solar-area-calculator',
  2 => 'solar-yield-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kwh = Math.max(1, parseFloat(document.getElementById('dailyKwhConsumption').value) || 15);
            const sunHours = Math.max(3, parseFloat(document.getElementById('peakSunHours').value) || 5.5);
            const panelW = parseFloat(document.getElementById('panelWattRating').value) || 550;
            const losses = Math.max(10, parseFloat(document.getElementById('systemLossesRate').value) || 20) / 100;

            // القدرة الإجمالية المطلوبة للألواح بالواط مع تعويض الفواقد
            const effectiveFactor = 1 - losses;
            const totalSystemWatts = (kwh * 1000) / (sunHours * effectiveFactor);
            const panelsCount = Math.ceil(totalSystemWatts / panelW);
            const actualTotalKw = (panelsCount * panelW) / 1000;
            const roofAreaNeeded = panelsCount * 2.5; // متوسط مساحة اللوح مع الممرات 2.5 م²

            setPrimaryResult(panelsCount + ' ألواح شمسية (' + actualTotalKw.toFixed(2) + ' kW)', 'عدد الألواح الشمسية المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'القدرة الإجمالية للألواح', value: actualTotalKw.toFixed(2) + ' kW (' + Math.round(panelsCount * panelW) + ' واط)', color: '#3b82f6' },
                { label: 'الإنتاج اليومي المتوقع في الصيف', value: (actualTotalKw * sunHours * effectiveFactor).toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'المساحة التقريبية المطلوبة على السطح', value: Math.ceil(roofAreaNeeded) + ' م²', color: '#f59e0b' },
                { label: 'قدرة اللوح المعتمد', value: panelW + ' واط', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتوليد <strong>${kwh} كيلوواط ساعة يومياً</strong> في منطقة بساعات شمس <strong>${sunHours} ساعات</strong>، تحتاج إلى <strong>${panelsCount} لوح شمسي قدرة ${panelW}W</strong>، بإجمالي قدرة <strong>${actualTotalKw.toFixed(2)} kW</strong> ومساحة سطح تقدر بـ <strong>${Math.ceil(roofAreaNeeded)} م²</strong>.</p>
            `);
        
        saveLastInputs('solar-panels-calculator');
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
    restoreLastInputs('solar-panels-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>