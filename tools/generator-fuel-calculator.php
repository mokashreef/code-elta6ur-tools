<?php
/**
 * أداة: حاسبة استهلاك المولد للوقود
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'generator-fuel-calculator';
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
        <label class="form-label" for="genKvaRating">قدرة المولد (بالـ KVA)</label>
        <input type="number" id="genKvaRating" class="form-control" value="15" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelTypeGen">نوع وقود المولد</label>
        <select id="fuelTypeGen" class="form-control" onchange="calculateTool()">
            <option value="diesel" selected>ديزل (سولار) - كفاءة أعلى واستهلاك أقل ~ 0.28 لتر / KVA / ساعة</option>
            <option value="gasoline" >بنزين (غازولين) - استهلاك أعلى ~ 0.38 لتر / KVA / ساعة</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="loadPercentageGen">نسبة التحميل على المولد</label>
        <select id="loadPercentageGen" class="form-control" onchange="calculateTool()">
            <option value="25" >تحميل خفيف (25%)</option>
            <option value="50" >تحميل متوسط (50%)</option>
            <option value="75" selected>تحميل قياسي موصى به (75%)</option>
            <option value="100" >تحميل كامل أقصى (100%)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="genDailyRunHours">ساعات التشغيل اليومية</label>
        <input type="number" id="genDailyRunHours" class="form-control" value="8" min="1" max="24" step="1"  oninput="calculateTool()">
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
  0 => 'مولدات الديزل أكثر كفاءة في استهلاك الوقود بنسبة 30% إلى 40% مقارنة بمولدات البنزين ذات القدرة المماثلة.',
  1 => 'أفضل كفاءة تشغيلية للمولد وعمر أطول للمحرك تكون عند نسبة تحميل بين 70% إلى 80% من قدرته القصوى.',
),
        'يفترض محرك مولد سليم مع فلاتر هواء ووقود نظيفة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تشغيل المولد بدون حمل (Idle) يوفر الوقود بشكل كامل؟',
    'a' => 'لا؛ فالمحرك يستهلك حوالي 25% إلى 30% من استهلاكه الأقصى فقط للحفاظ على دورانه وتوليد التردد 50Hz حتى لو لم تكن هناك أجهزة متصلة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'generator-cost-calculator',
  1 => 'generator-capacity-calculator',
  2 => 'power-source-comparison-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kva = Math.max(1, parseFloat(document.getElementById('genKvaRating').value) || 15);
            const fuel = document.getElementById('fuelTypeGen').value;
            const loadPercent = parseFloat(document.getElementById('loadPercentageGen').value) || 75;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('genDailyRunHours').value) || 8));

            // معدل استهلاك اللتر لكل KVA عند الحمل الكامل
            const fullLoadRate = fuel === 'diesel' ? 0.28 : 0.38;
            // الاستهلاك الفعلي يتناسب تقريباً مع التحميل + نسبة احتكاك المحرك
            const factor = (loadPercent / 100) * 0.75 + 0.25;
            const litersPerHour = kva * fullLoadRate * factor;

            const dailyLiters = litersPerHour * hours;
            const monthlyLiters = dailyLiters * 30;

            setPrimaryResult(litersPerHour.toFixed(2) + ' لتر وقود في الساعة', 'معدل استهلاك المولد للوقود');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك اليومي (' + hours + ' ساعات)', value: dailyLiters.toFixed(1) + ' لتر', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري التقديري', value: monthlyLiters.toFixed(0) + ' لتر', color: '#10b981' },
                { label: 'القدرة الفعلية المولدة بالكيلوواط (kW)', value: (kva * 0.8 * (loadPercent/100)).toFixed(1) + ' kW', color: '#f59e0b' },
                { label: 'نوع الوقود المعتمد', value: fuel === 'diesel' ? 'ديزل (سولار)' : 'بنزين', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مولد بقدرة <strong>${kva} KVA</strong> يعمل بوقود <strong>${fuel === 'diesel' ? 'الديزل' : 'البنزين'}</strong> عند نسبة تحميل <strong>${loadPercent}%</strong>، يستهلك حوالي <strong>${litersPerHour.toFixed(2)} لتر/ساعة</strong>، أي ما يعادل <strong>${dailyLiters.toFixed(1)} لتر يومياً</strong>.</p>
            `);
        
        saveLastInputs('generator-fuel-calculator');
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
    restoreLastInputs('generator-fuel-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>