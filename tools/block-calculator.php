<?php
/**
 * أداة: حاسبة كمية البلوك الإسمنتي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'block-calculator';
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
        <label class="form-label" for="wallsLengthTotal">إجمالي أطوال الجدران المراد بناؤها (متر)</label>
        <input type="number" id="wallsLengthTotal" class="form-control" value="30" min="1"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="wallHeightBlock">ارتفاع الجدار (متر)</label>
        <input type="number" id="wallHeightBlock" class="form-control" value="3.0" min="1" max="10" step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="openingsAreaDeduct">مساحة الفتحات المخصومة (أبواب وشبابيك م²)</label>
        <input type="number" id="openingsAreaDeduct" class="form-control" value="8" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="blockTypeSize">مقاس البلوك المستخدم</label>
        <select id="blockTypeSize" class="form-control" onchange="calculateTool()">
            <option value="20x40" selected>بلوك قياسي 20×40 سم (12.5 بلوكة للمتر المربع)</option>
            <option value="15x40" >بلوك قواطع 15×40 سم (12.5 بلوكة للمتر المربع)</option>
            <option value="10x40" >بلوك رفيع 10×40 سم (12.5 بلوكة للمتر المربع)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="wastePercent">نسبة الهالك والكسر (%)- عادة 5%</label>
        <input type="number" id="wastePercent" class="form-control" value="5" min="0" max="15" step="1"  oninput="calculateTool()">
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
  0 => 'أبعاد البلوكة القياسية: 40 سم طول × 20 سم ارتفاع.',
  1 => 'المتر المربع يحتوي بالضبط على: 1 ÷ (0.40 × 0.20) = 12.5 بلوكة.',
  2 => 'إجمالي البلوك = المساحة الصافية × 12.5 × (1 + نسبة الهالك).',
),
        'سماكة عراميس المونة الإسمنتية 1 سم مأخوذة في الاعتبار ضمن الأبعاد القياسية للبلوك.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أفضل للجدران الخارجية: البلوك البركاني أم الإسمنتي المصمت؟',
    'a' => 'البلوك البركاني المعزول بالبوليستيرين هو الخيار الأفضل للجدران الخارجية لتميزه بخفة الوزن وعزله الحراري الممتاز المطابق لكود البناء.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'brick-calculator',
  1 => 'cement-calculator',
  2 => 'wall-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('wallsLengthTotal').value) || 30);
            const height = Math.max(1, parseFloat(document.getElementById('wallHeightBlock').value) || 3.0);
            const openings = Math.max(0, parseFloat(document.getElementById('openingsAreaDeduct').value) || 8);
            const waste = Math.max(0, parseFloat(document.getElementById('wastePercent').value) || 5) / 100;

            const grossArea = len * height;
            const netArea = Math.max(1, grossArea - openings);
            // المقاس القياسي 40 سم طول × 20 سم ارتفاع = 0.08 م² للبلوكة -> 12.5 بلوكة/م²
            const blocksPerM2 = 12.5;
            const baseBlocks = netArea * blocksPerM2;
            const totalBlocks = Math.ceil(baseBlocks * (1 + waste));

            setPrimaryResult(totalBlocks + ' بلوكة إسمنتية', 'إجمالي عدد البلوك المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للبناء', value: netArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'عدد البلوك بدون هالك', value: Math.ceil(baseBlocks) + ' بلوكة', color: '#10b981' },
                { label: 'مخصص الهالك والقص (' + (waste * 100) + '%)', value: Math.ceil(totalBlocks - baseBlocks) + ' بلوكة', color: '#f59e0b' },
                { label: 'إجمالي مساحة الفتحات المخصومة', value: openings + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء جدران بمساحة صافية <strong>${netArea.toFixed(1)} م²</strong>، تحتاج إلى <strong>${totalBlocks} بلوكة</strong> شاملة <strong>${(waste*100).toFixed(0)}%</strong> نسبة هالك للقص والتشبيك مع الأعمدة.</p>
            `);
        
        saveLastInputs('block-calculator');
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
    restoreLastInputs('block-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>