<?php
/**
 * أداة: حاسبة مساحة الجدران والدهانات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'wall-area-calculator';
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
        <label class="form-label" for="roomLenArea">طول الغرفة (متر)</label>
        <input type="number" id="roomLenArea" class="form-control" value="5.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roomWidArea">عرض الغرفة (متر)</label>
        <input type="number" id="roomWidArea" class="form-control" value="4.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roomHeightArea">ارتفاع السقف (متر)</label>
        <input type="number" id="roomHeightArea" class="form-control" value="3.0" min="1.5" max="8" step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="doorsCountArea">عدد الأبواب (المعتاد 2 م² لكل باب)</label>
        <input type="number" id="doorsCountArea" class="form-control" value="1" min="0"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="windowsCountArea">عدد النوافذ (المعتاد 1.5 م² لكل نافذة)</label>
        <input type="number" id="windowsCountArea" class="form-control" value="1" min="0"  step="1"  oninput="calculateTool()">
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
  0 => 'محيط الغرفة = 2 × (الطول + العرض).',
  1 => 'مساحة الجدران الإجمالية = محيط الغرفة × الارتفاع.',
  2 => 'المساحة الصافية = المساحة الإجمالية - مساحات الفتحات (الأبواب والشبابيك).',
),
        'متوسط مساحة الباب القياسي 2 م² (عرض 1م × ارتفاع 2م)، ومساحة النافذة المعتادة 1.5 م².'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحسب جدار له شكل مثلث أو سقف مائل؟',
    'a' => 'احسب الجزء المستطيل أولاً (الطول × الارتفاع الأصغر)، ثم احسب المثلث العلوي (نصف القاعدة × الارتفاع المتبقي).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ceiling-area-calculator',
  1 => 'paint-calculator',
  2 => 'wallpaper-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('roomLenArea').value) || 5.0);
            const wid = Math.max(1, parseFloat(document.getElementById('roomWidArea').value) || 4.0);
            const height = Math.max(1.5, parseFloat(document.getElementById('roomHeightArea').value) || 3.0);
            const doors = Math.max(0, parseInt(document.getElementById('doorsCountArea').value) || 0);
            const windows = Math.max(0, parseInt(document.getElementById('windowsCountArea').value) || 0);

            const perimeter = 2 * (len + wid);
            const grossWallArea = perimeter * height;
            const doorsArea = doors * 2.0;
            const windowsArea = windows * 1.5;
            const totalDeductions = doorsArea + windowsArea;
            const netWallArea = Math.max(1, grossWallArea - totalDeductions);

            setPrimaryResult(netWallArea.toFixed(2) + ' م²', 'المساحة الصافية الإجمالية للجدران');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الإجمالية قبل الخصم', value: grossWallArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'محيط الغرفة الكلي', value: perimeter.toFixed(1) + ' متر', color: '#10b981' },
                { label: 'مساحة الأبواب والنوافذ المخصومة', value: totalDeductions.toFixed(1) + ' م²', color: '#ef4444' },
                { label: 'مساحة السقف المقابلة', value: (len * wid).toFixed(2) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>محيط الغرفة <strong>${perimeter.toFixed(1)} متر</strong>. المساحة الإجمالية للحوائط <strong>${grossWallArea.toFixed(2)} م²</strong>، وبعد خصم <strong>${totalDeductions.toFixed(1)} م²</strong> للأبواب والشبابيك، تصبح المساحة الصافية للدهان والتشطيب <strong>${netWallArea.toFixed(2)} متر مربع</strong>.</p>
            `);
        
        saveLastInputs('wall-area-calculator');
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
    restoreLastInputs('wall-area-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>