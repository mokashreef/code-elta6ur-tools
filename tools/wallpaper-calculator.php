<?php
/**
 * أداة: حاسبة كمية ورق الجدران
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'wallpaper-calculator';
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
        <label class="form-label" for="roomPerimWallpaper">محيط الجدران المراد تغطيتها (متر)</label>
        <input type="number" id="roomPerimWallpaper" class="form-control" value="14" min="1"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="wallHeightWallpaper">ارتفاع الجدار (متر)</label>
        <input type="number" id="wallHeightWallpaper" class="form-control" value="2.8" min="1.5" max="5" step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="rollWidth">عرض رول ورق الجدران (متر) - القياسي 0.53 م</label>
        <input type="number" id="rollWidth" class="form-control" value="0.53" min="0.4" max="1.5" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="rollLength">طول رول ورق الجدران (متر) - القياسي 10.0 م</label>
        <input type="number" id="rollLength" class="form-control" value="10.0" min="5" max="25" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="patternRepeat">تطابق النقشة (Pattern Repeat)</label>
        <select id="patternRepeat" class="form-control" onchange="calculateTool()">
            <option value="none" >سادة بدون نقشة متكررة (هالك قليل جداً)</option>
            <option value="repeat" selected>نقشة متكررة تحتاج مطابقة أفقية (هالك 15%)</option>
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
  0 => 'الرول القياسي الأكثر انتشاراً عالمياً هو 0.53 متر عرض × 10 أمتار طول (يغطي حوالي 5.3 م²).',
  1 => 'الرول الواحد يعطي في الغالب 3 شرائح كاملة لارتفاع السقف المعتاد (2.7 إلى 2.9 م).',
),
        'يفترض عدم خصم النوافذ والأبواب الصغيرة كاحتياطي لتطابق الرسم والنقشات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُنصح بعدم خصم الأبواب والنوافذ عند حساب ورق الجدران؟',
    'a' => 'لأن قص الشريحة عند النافذة أو الباب لا يسمح غالباً بإعادة استخدام باقي الشريحة في مكان آخر بسبب ضرورة تطابق ارتفاع النقشة مع الشريحة المجاورة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'wall-area-calculator',
  1 => 'paint-calculator',
  2 => 'putty-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const perim = Math.max(1, parseFloat(document.getElementById('roomPerimWallpaper').value) || 14);
            const height = Math.max(1.5, parseFloat(document.getElementById('wallHeightWallpaper').value) || 2.8);
            const rWidth = Math.max(0.4, parseFloat(document.getElementById('rollWidth').value) || 0.53);
            const rLen = Math.max(5, parseFloat(document.getElementById('rollLength').value) || 10.0);
            const pattern = document.getElementById('patternRepeat').value;

            // عدد الشرائح الرأسية في المحيط
            const stripsNeeded = Math.ceil(perim / rWidth);
            // عدد الشرائح التي يخرجها الرول الواحد
            let effectiveHeight = height + (pattern === 'repeat' ? 0.3 : 0.1);
            const stripsPerRoll = Math.floor(rLen / effectiveHeight) || 1;
            const rollsCount = Math.ceil(stripsNeeded / stripsPerRoll);
            const totalWallArea = perim * height;

            setPrimaryResult(rollsCount + ' رول ورق جدران', 'عدد الرولات المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الجدران الإجمالية', value: totalWallArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'عدد الشرائح الرأسية المطلوبة', value: stripsNeeded + ' شريحة', color: '#10b981' },
                { label: 'عدد الشرائح الناتجة من الرول الواحد', value: stripsPerRoll + ' شرائح', color: '#f59e0b' },
                { label: 'مساحة الرول الواحد', value: (rWidth * rLen).toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية جدران بمحيط <strong>${perim} متر</strong> وارتفاع <strong>${height} متر</strong>، تحتاج إلى <strong>${stripsNeeded} شريحة رأسية</strong>، ما يتطلب شراء <strong>${rollsCount} رول قياسي</strong> لضمان استمرار النقشة ومحاذاتها بدقة.</p>
            `);
        
        saveLastInputs('wallpaper-calculator');
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
    restoreLastInputs('wallpaper-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>