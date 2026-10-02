<?php
/**
 * أداة: حاسبة مساحة الغرفة المطلوبة للأثاث
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'room-furniture-area-calculator';
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
        <label class="form-label" for="roomLength">طول الغرفة (متر)</label>
        <input type="number" id="roomLength" class="form-control" value="4.5" min="2"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roomWidth">عرض الغرفة (متر)</label>
        <input type="number" id="roomWidth" class="form-control" value="4.0" min="2"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="furnitureAreaManual">مساحة الأثاث الفعلية التقديرية (متر مربع)</label>
        <input type="number" id="furnitureAreaManual" class="form-control" value="7.5" min="1"  step="0.5"  oninput="calculateTool()">
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
  0 => 'مساحة الغرفة = الطول × العرض.',
  1 => 'نسبة الإشغال = (مساحة الأثاث ÷ مساحة الغرفة) × 100.',
  2 => 'المساحة الحرة للحركة يجب ألا تقل عن 60% من مساحة الغرفة.',
),
        'المعايير المعمارية تشترط مسار حركة لا يقل عن 80-90 سم بين السرير والدولاب أو الجدار.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الحل إذا كانت مساحة الأثاث تزيد عن 50%؟',
    'a' => 'استخدم أثاثاً متعدد الوظائف، مثل الأسرة ذات الأدراج السفلية، والدواليب ذات الأبواب السحابة (Sliding Doors) بدلاً من الأبواب المفصلية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'furniture-quantity-calculator',
  1 => 'carpet-area-calculator',
  2 => 'wall-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(2, parseFloat(document.getElementById('roomLength').value) || 4.5);
            const wid = Math.max(2, parseFloat(document.getElementById('roomWidth').value) || 4.0);
            const furn = Math.max(1, parseFloat(document.getElementById('furnitureAreaManual').value) || 7.5);

            const totalRoomArea = len * wid;
            const furnitureRatio = (furn / totalRoomArea) * 100;
            const freeArea = totalRoomArea - furn;

            let status = 'ممتاز ومريح جداً ✅';
            let color = '#10b981';
            if (furnitureRatio > 40 && furnitureRatio <= 55) { status = 'مقبول ومتوازن ⚠️'; color = '#f59e0b'; }
            if (furnitureRatio > 55) { status = 'مزدحم جداً ويعيق الحركة ❌'; color = '#ef4444'; }

            setPrimaryResult(furnitureRatio.toFixed(1) + '% من مساحة الغرفة', 'نسبة إشغال الأثاث للغرفة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الكلية للغرفة', value: totalRoomArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الفراغ والحركة الحرة', value: freeArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'مساحة الأثاث المشغولة', value: furn.toFixed(1) + ' م²', color: '#8b5cf6' },
                { label: 'تقييم الراحة والحركة', value: status, color: color }
            ]);

            setResultContent(`
                <p>تشغل قطع الأثاث <strong>${furnitureRatio.toFixed(1)}%</strong> من الغرفة. التوصية المعمارية القياسية هي أن تشغل المفروشات ما بين <strong>30% إلى 40%</strong> كحد أقصى لضمان راحة العين وسهولة فتح الأبواب والدواليب.</p>
            `);
        
        saveLastInputs('room-furniture-area-calculator');
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
    restoreLastInputs('room-furniture-area-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>