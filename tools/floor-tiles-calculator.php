<?php
/**
 * أداة: حاسبة كمية البلاط للأرضيات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'floor-tiles-calculator';
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
        <label class="form-label" for="roomLenTile">طول الأرضية (متر)</label>
        <input type="number" id="roomLenTile" class="form-control" value="6.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roomWidTile">عرض الأرضية (متر)</label>
        <input type="number" id="roomWidTile" class="form-control" value="4.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileWidthCm">عرض البلاطة (سم)</label>
        <input type="number" id="tileWidthCm" class="form-control" value="60" min="10"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileLengthCm">طول البلاطة (سم)</label>
        <input type="number" id="tileLengthCm" class="form-control" value="60" min="10"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tileInstallPattern">طريقة تركيب البلاط</label>
        <select id="tileInstallPattern" class="form-control" onchange="calculateTool()">
            <option value="straight" selected>تركيب مستقيم عادي (هالك 5% إلى 7%)</option>
            <option value="diagonal" >تركيب قطري / مائل 45 درجة (سمبوسة) - هالك 12% إلى 15%</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="tilesPerBox">عدد البلاطات في الكرتونة الواحدة</label>
        <input type="number" id="tilesPerBox" class="form-control" value="4" min="1" max="30" step="1"  oninput="calculateTool()">
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
  0 => 'مساحة الأرضية = الطول × العرض.',
  1 => 'مساحة البلاطة = الطول (بالمتر) × العرض (بالمتر).',
  2 => 'التركيب المائل يتطلب دائماً نسبة هالك أعلى (12-15%) بسبب كثرة المثلثات والقصات الجدارية.',
),
        'يفترض عدم وجود عيوب مصنعية في البلاط ومهارة فني تركيب جيدة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يجب الاحتفاظ بكرتونة إضافية بعد انتهاء التبليط؟',
    'a' => 'نعم؛ يُوصى بشدة بالاحتفاظ بكرتونة إضافية من نفس رقم الطبخة (Batch Number / Shade) لأي إصلاحات مستقبلية في السباكة، لأن الألوان تختلف من إنتاج لآخر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ceramic-calculator',
  1 => 'tile-adhesive-calculator',
  2 => 'grout-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('roomLenTile').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('roomWidTile').value) || 4.0);
            const tileW = Math.max(10, parseFloat(document.getElementById('tileWidthCm').value) || 60) / 100;
            const tileL = Math.max(10, parseFloat(document.getElementById('tileLengthCm').value) || 60) / 100;
            const pattern = document.getElementById('tileInstallPattern').value;
            const boxCount = Math.max(1, parseInt(document.getElementById('tilesPerBox').value) || 4);

            const floorArea = len * wid;
            const singleTileArea = tileW * tileL;
            const wasteRate = pattern === 'diagonal' ? 0.12 : 0.06;
            const totalAreaWithWaste = floorArea * (1 + wasteRate);
            const totalTiles = Math.ceil(totalAreaWithWaste / singleTileArea);
            const boxesNeeded = Math.ceil(totalTiles / boxCount);

            setPrimaryResult(boxesNeeded + ' كرتونة بلاط (' + totalTiles + ' بلاطة)', 'كمية البلاط المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الأرضية الصافية', value: floorArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'إجمالي المساحة المطلوبة مع الهالك', value: totalAreaWithWaste.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'نسبة هالك القص المعتمدة', value: (wasteRate * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'مساحة البلاطة الواحدة', value: singleTileArea.toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية أرضية بمساحة <strong>${floorArea.toFixed(2)} م²</strong> ببلاط مقاس ${(tileW*100)}×${(tileL*100)} سم، تحتاج إلى <strong>${totalTiles} بلاطة</strong> معبأة في <strong>${boxesNeeded} كرتونة</strong> لضمان تغطية هالك القص والزوايا.</p>
            `);
        
        saveLastInputs('floor-tiles-calculator');
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
    restoreLastInputs('floor-tiles-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>