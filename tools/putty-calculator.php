<?php
/**
 * أداة: حاسبة كمية المعجون للجدران
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'putty-calculator';
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
        <label class="form-label" for="wallNetArea">المساحة الصافية للجدران المراد سحبها معجون (م²)</label>
        <input type="number" id="wallNetArea" class="form-control" value="45" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="knivesCount">عدد طبقات (سكاكين) المعجون</label>
        <select id="knivesCount" class="form-control" onchange="calculateTool()">
            <option value="2" >سكينتين (طبقتين - للجدران الناعمة مسبقاً)</option>
            <option value="3" selected>3 سكاكين (المعيار الهندسي لتسوية اللياسة الجديدة)</option>
            <option value="4" >4 سكاكين (للتشطيبات الفاخرة جداً وأسطح الجبس بورد)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="puttyType">نوع المعجون المستخدم</label>
        <select id="puttyType" class="form-control" onchange="calculateTool()">
            <option value="paste_bucket" selected>معجون مجهز جاهز (براميل بلاستيك سعة 15-20 كجم)</option>
            <option value="powder_bag" >معجون بودرة يتم خلطه بالماء (شكائر سعة 20 كجم)</option>
        </select>
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
  0 => 'السكين الأول يملأ مسامات اللياسة ويستهلك كمية أكبر، بينما السكين الثاني والثالث ينعمان السطح.',
  1 => 'متوسط استهلاك المتر المربع حوالي 0.5 كجم لكل طبقة معجون.',
),
        'يفترض سطح لياسة مستوٍ؛ الأسطح شديدة التعرج تحتاج زيادة 15% في كمية المعجون.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أفضل: معجون البودرة أم المعجون الجاهز؟',
    'a' => 'معجون البودرة ممتاز للطبقات الأولى لملء الفراغات وتوفير التكلفة، بينما المعجون الجاهز ممتاز للطبقة النهائية لأنه يعطي ملمساً فائق النعومة وسهل الصنفرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'paint-calculator',
  1 => 'wall-area-calculator',
  2 => 'gypsum-board-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('wallNetArea').value) || 45);
            const knives = parseInt(document.getElementById('knivesCount').value) || 3;
            const type = document.getElementById('puttyType').value;

            // استهلاك المتر المربع للسكين الواحد حوالي 0.5 إلى 0.6 كجم
            const kgPerM2PerCoat = 0.55;
            const totalKg = area * knives * kgPerM2PerCoat;
            const packageWeight = 20; // كجم للعبوة الواحدة
            const packagesCount = Math.ceil(totalKg / packageWeight);

            setPrimaryResult(Math.ceil(totalKg) + ' كجم معجون (' + packagesCount + ' ' + (type === 'paste_bucket' ? 'برميل' : 'شيكارة') + ')', 'كمية المعجون المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الإجمالية للمحارة', value: area + ' م²', color: '#3b82f6' },
                { label: 'عدد طبقات وسكاكين المعجون', value: knives + ' سكاكين', color: '#10b981' },
                { label: 'إجمالي الوزن المطلوب', value: Math.ceil(totalKg) + ' كجم', color: '#f59e0b' },
                { label: 'عدد العبوات سعة 20 كجم', value: packagesCount + ' عبوة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتنفيذ <strong>${knives} طبقات معجون</strong> على مساحة <strong>${area} م²</strong>، تحتاج حوالي <strong>${Math.ceil(totalKg)} كجم</strong> من المعجون، أي ما يعادل <strong>${packagesCount} عبوة سعة 20 كجم</strong>.</p>
            `);
        
        saveLastInputs('putty-calculator');
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
    restoreLastInputs('putty-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>