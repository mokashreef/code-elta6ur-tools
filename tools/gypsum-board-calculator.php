<?php
/**
 * أداة: حاسبة كمية الجبس بورد ومستلزماته
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'gypsum-board-calculator';
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
        <label class="form-label" for="ceilingLength">طول السقف المراد تغطيته (متر)</label>
        <input type="number" id="ceilingLength" class="form-control" value="6.0" min="1"  step="0.2"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ceilingWidth">عرض السقف (متر)</label>
        <input type="number" id="ceilingWidth" class="form-control" value="4.5" min="1"  step="0.2"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="sheetSize">مقاس لوح الجبس بورد</label>
        <select id="sheetSize" class="form-control" onchange="calculateTool()">
            <option value="standard" selected>1.20 م × 2.40 م (المقاس القياسي الأكثر شيوعاً - 2.88 م²)</option>
            <option value="long" >1.20 م × 3.00 م (ألواح طويلة - 3.60 م²)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="wasteRate">نسبة الهالك والقص (%)- عادة 10%</label>
        <input type="number" id="wasteRate" class="form-control" value="10" min="5" max="25" step="1"  oninput="calculateTool()">
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
  0 => 'مساحة السقف = الطول × العرض.',
  1 => 'عدد الألواح = (مساحة السقف × (1 + نسبة الهالك)) ÷ مساحة اللوح الواحد.',
  2 => 'مساحة اللوح القياسي = 1.20 × 2.40 = 2.88 م².',
),
        'النسب تشمل الهياكل المعدنية المعلقة (أوميجا وزوايا وتيش تعليق) وفق المعايير الإنشائية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو نوع الجبس بورد المناسب للمطابخ والحمامات؟',
    'a' => 'يجب استخدام الألواح الخضراء المقاومة للرطوبة، أو الألواح الأسمنتية (Cement Board) المعزولة لمنع تكون العفن والتلف الناتج عن البخار.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ceiling-area-calculator',
  1 => 'putty-calculator',
  2 => 'paint-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('ceilingLength').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('ceilingWidth').value) || 4.5);
            const size = document.getElementById('sheetSize').value;
            const waste = Math.max(5, parseFloat(document.getElementById('wasteRate').value) || 10) / 100;

            const area = len * wid;
            const sheetArea = size === 'long' ? 3.60 : 2.88;
            const areaWithWaste = area * (1 + waste);
            const sheetsCount = Math.ceil(areaWithWaste / sheetArea);

            // حسابات تقريبية للإكسسوارات لكل لوح جبس بورد
            const omegaRails = Math.ceil(sheetsCount * 1.5); // قطاع أوميجا 3 متر
            const cStuds = Math.ceil(sheetsCount * 1.2); // قطاع سي 3 متر
            const angles = Math.ceil((2 * (len + wid)) / 3); // زوايا جدارية محيطية طول 3 م
            const screwsBoxes = Math.ceil(sheetsCount / 12); // علبة مسامير جبس لكل 12 لوح

            setPrimaryResult(sheetsCount + ' لوح جبس بورد', 'عدد ألواح الجبس بورد المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة السقف الصافية', value: area.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'قواطع أوميجا (Omega rails)', value: omegaRails + ' عود (طول 3م)', color: '#10b981' },
                { label: 'زوايا جدارية محيطية', value: angles + ' عود (طول 3م)', color: '#f59e0b' },
                { label: 'براغي تثبيت وشريط فواصل', value: screwsBoxes + ' علبة مسامير + رول فيبر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية سقف بمساحة <strong>${area.toFixed(1)} م²</strong> مع نسبة هالك <strong>${(waste*100).toFixed(0)}%</strong>، تحتاج إلى <strong>${sheetsCount} لوح</strong> بمقاس ${sheetArea} م²، بالإضافة إلى هيكل الحديد ومستلزمات التثبيت.</p>
            `);
        
        saveLastInputs('gypsum-board-calculator');
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
    restoreLastInputs('gypsum-board-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>