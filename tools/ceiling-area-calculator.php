<?php
/**
 * أداة: حاسبة مساحة السقف
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ceiling-area-calculator';
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
        <label class="form-label" for="ceilingLen">طول السقف (متر)</label>
        <input type="number" id="ceilingLen" class="form-control" value="6.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ceilingWid">عرض السقف (متر)</label>
        <input type="number" id="ceilingWid" class="form-control" value="4.5" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hasDrops">هل السقف يحتوي على جبس ساقط (بيت نور / كرانيش بارزة)؟</label>
        <select id="hasDrops" class="form-control" onchange="calculateTool()">
            <option value="flat" selected>سقف مستوٍ عادي (Flat Ceiling)</option>
            <option value="cove" >سقف معلق مع بيت نور وكرانيش (+20% مساحة إضافية للدهان)</option>
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
  0 => 'مساحة السقف المسطح المستوي تطابق مساحة الأرضية بالضبط.',
  1 => 'الأسقف المعلقة المعمارية ذات البيوت الساقطة والإنارة المخفية تزيد مساحة الدهان بمقدار 15% إلى 25% بسبب السقوط الجانبي للجبس.',
),
        'الغرفة مستطيلة أو مربعة؛ الأشكال غير المنتظمة يمكن تقسيمها لمستطيلات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحسب طول شريط الليد (LED Strip) للإنارة المخفية؟',
    'a' => 'طول شريط الليد يعادل محيط السقف الداخلي لبيت النور مطروحاً منه فتحات الصيانة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'gypsum-board-calculator',
  1 => 'wall-area-calculator',
  2 => 'paint-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('ceilingLen').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('ceilingWid').value) || 4.5);
            const type = document.getElementById('hasDrops').value;

            const flatArea = len * wid;
            const paintArea = type === 'cove' ? flatArea * 1.2 : flatArea;
            const perimeter = 2 * (len + wid);

            setPrimaryResult(flatArea.toFixed(2) + ' م²', 'مساحة السقف الصافية');
            showResultArea();

            setDetailStats([
                { label: 'المساحة المسطحة المستوية', value: flatArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الدهان المقدرة (مع الكرانيش)', value: paintArea.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'محيط السقف (طول الكرنيشة المطلوبة)', value: perimeter.toFixed(1) + ' متر طولي', color: '#f59e0b' },
                { label: 'ألواح الجبس بورد اللازمة للتغطية', value: Math.ceil((flatArea*1.1)/2.88) + ' لوح', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مساحة السقف الصافية هي <strong>${flatArea.toFixed(2)} متر مربع</strong>، ومحيطه <strong>${perimeter.toFixed(1)} متر طولي</strong> (وهو طول الكرانيش المطلوبة لزوايا السقف).</p>
            `);
        
        saveLastInputs('ceiling-area-calculator');
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
    restoreLastInputs('ceiling-area-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>