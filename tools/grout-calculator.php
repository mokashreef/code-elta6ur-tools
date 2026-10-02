<?php
/**
 * أداة: حاسبة كمية الجراوت والترويبة للبلاط
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grout-calculator';
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
        <label class="form-label" for="tilingAreaGrout">المساحة المطلوب ترويبها (متر مربع)</label>
        <input type="number" id="tilingAreaGrout" class="form-control" value="50" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileLengthMm">طول البلاطة (مم)</label>
        <input type="number" id="tileLengthMm" class="form-control" value="600" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileWidthMm">عرض البلاطة (مم)</label>
        <input type="number" id="tileWidthMm" class="form-control" value="600" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileThicknessMm">سماكة البلاطة (مم) - عادة 8 إلى 10 مم</label>
        <input type="number" id="tileThicknessMm" class="form-control" value="9" min="4" max="30" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="jointWidthMm">عرض الفاصل بين البلاطات (مم) - عادة 2 إلى 3 مم</label>
        <input type="number" id="jointWidthMm" class="form-control" value="2" min="1" max="15" step="0.5"  oninput="calculateTool()">
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
  0 => 'معادلة حساب الترويبة: الاستهلاك = ((طول البلاطة + عرضها) ÷ (طولها × عرضها)) × سماكتها × عرض الفاصل × 1.7 (كثافة المادة).',
  1 => 'كلما كبر مقاس البلاطة قل عدد الفواصل وبالتالي قل استهلاك الترويبة.',
),
        'يفترض استخدام ترويبة إسمنتية مقاومة للرطوبة والبكتيريا ملائمة للحمامات والأرضيات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى يجب استخدام الترويبة الإيبوكسية (Epoxy Grout)؟',
    'a' => 'في حمامات السباحة، والمطابخ التجارية، وأرضيات المستشفيات لأنها لا تمتص السوائل والدهون نهائياً ولا يتغير لونها مع الزمن.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'floor-tiles-calculator',
  1 => 'ceramic-calculator',
  2 => 'tile-adhesive-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('tilingAreaGrout').value) || 50);
            const L = Math.max(50, parseFloat(document.getElementById('tileLengthMm').value) || 600);
            const W = Math.max(50, parseFloat(document.getElementById('tileWidthMm').value) || 600);
            const T = Math.max(4, parseFloat(document.getElementById('tileThicknessMm').value) || 9);
            const J = Math.max(1, parseFloat(document.getElementById('jointWidthMm').value) || 2);

            // معادلة الترويبة القياسية: kg/m² = ((L + W) / (L * W)) * T * J * 1.7
            const kgPerM2 = ((L + W) / (L * W)) * T * J * 1.7;
            const totalKg = area * kgPerM2;
            const bagSize = 5; // كيس ترويبة قياسي 5 كجم
            const bags = Math.ceil(totalKg / bagSize);

            setPrimaryResult(Math.ceil(totalKg) + ' كجم ترويبة (' + bags + ' أكياس سعة 5 كجم)', 'كمية مادة الجراوت المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'معدل استهلاك المتر المربع', value: kgPerM2.toFixed(3) + ' كجم / م²', color: '#3b82f6' },
                { label: 'المساحة الإجمالية للمشروع', value: area + ' م²', color: '#10b981' },
                { label: 'عرض الفاصل المعتمد', value: J + ' مم', color: '#f59e0b' },
                { label: 'أكياس الترويبة سعة 5 كجم', value: bags + ' أكياس', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لترويبة مساحة <strong>${area} م²</strong> ببلاط مقاس ${L/10}×${W/10} سم وفواصل بعرض <strong>${J} مم</strong>، تحتاج إلى <strong>${Math.ceil(totalKg)} كجم ترويبة</strong> (${bags} أكياس سعة 5 كجم).</p>
            `);
        
        saveLastInputs('grout-calculator');
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
    restoreLastInputs('grout-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>