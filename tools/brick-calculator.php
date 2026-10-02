<?php
/**
 * أداة: حاسبة كمية الطوب الأحمر والطفلي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'brick-calculator';
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
        <label class="form-label" for="brickWallLength">طول الحائط (متر)</label>
        <input type="number" id="brickWallLength" class="form-control" value="20" min="1"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="brickWallHeight">ارتفاع الحائط (متر)</label>
        <input type="number" id="brickWallHeight" class="form-control" value="2.8" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="brickOpenings">مساحة الفتحات والأبواب المخصومة (م²)</label>
        <input type="number" id="brickOpenings" class="form-control" value="5" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="wallThicknessType">سماكة الجدار المبني</label>
        <select id="wallThicknessType" class="form-control" onchange="calculateTool()">
            <option value="half_brick" selected>جدار نصف طوبة (سمك 12 سم - قواطع داخلية) ~ 55 طوبة/م²</option>
            <option value="full_brick" >جدار طوبة كاملة (سمك 25 سم - جدران خارجية أو حاملة) ~ 110 طوبة/م²</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="brickWasteRate">نسبة الهالك والكسر (%)- عادة 5%</label>
        <input type="number" id="brickWasteRate" class="form-control" value="5" min="0" max="15" step="1"  oninput="calculateTool()">
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
  0 => 'مقاس الطوبة الحمراء القياسي: 25 × 12 × 6 سم.',
  1 => 'جدار نصف طوبة (12 سم) يحتاج حوالي 55 طوبة لكل متر مربع.',
  2 => 'جدار طوبة كاملة (25 سم) يحتاج حوالي 110 طوبة لكل متر مربع.',
),
        'الحساب يشمل فواصل المونة الإسمنتية القياسية بسماكة 1 سم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم كمية الأسمنت والرمل اللازمة لبناء 1000 طوبة؟',
    'a' => 'يحتاج كل ألف طوبة حمراء لحوالي 3 شكائر إسمنت و 0.6 م³ رمل ناعم للمونة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'block-calculator',
  1 => 'cement-calculator',
  2 => 'sand-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('brickWallLength').value) || 20);
            const height = Math.max(1, parseFloat(document.getElementById('brickWallHeight').value) || 2.8);
            const openings = Math.max(0, parseFloat(document.getElementById('brickOpenings').value) || 5);
            const thick = document.getElementById('wallThicknessType').value;
            const waste = Math.max(0, parseFloat(document.getElementById('brickWasteRate').value) || 5) / 100;

            const netArea = Math.max(1, (len * height) - openings);
            const factor = thick === 'full_brick' ? 110 : 55;
            const baseBricks = netArea * factor;
            const totalBricks = Math.ceil(baseBricks * (1 + waste));
            const thousandsCount = totalBricks / 1000;

            setPrimaryResult(totalBricks + ' طوبة (' + thousandsCount.toFixed(2) + ' ألف طوبة)', 'إجمالي كمية الطوب الأحمر المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للجدار', value: netArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'نوع الجدار المبني', value: thick === 'full_brick' ? 'طوبة كاملة (25 سم)' : 'نصف طوبة (12 سم)', color: '#10b981' },
                { label: 'معدل الطوب في المتر المربع', value: factor + ' طوبة/م²', color: '#f59e0b' },
                { label: 'عدد الألف طوبة', value: thousandsCount.toFixed(2) + ' ألف', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء جدار صافي بمساحة <strong>${netArea.toFixed(1)} م²</strong> بنظام <strong>${thick === 'full_brick' ? 'طوبة كاملة' : 'نصف طوبة'}</strong>، تحتاج إلى <strong>${totalBricks} طوبة</strong> (حوالي <strong>${thousandsCount.toFixed(2)} ألف طوبة</strong>).</p>
            `);
        
        saveLastInputs('brick-calculator');
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
    restoreLastInputs('brick-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>