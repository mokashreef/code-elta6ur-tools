<?php
/**
 * أداة: حاسبة طول الوزرة والنعلة للأرضيات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'skirting-board-calculator';
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
        <label class="form-label" for="skirtingRoomLen">طول الغرفة أو الصالة (متر)</label>
        <input type="number" id="skirtingRoomLen" class="form-control" value="6.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="skirtingRoomWid">عرض الغرفة (متر)</label>
        <input type="number" id="skirtingRoomWid" class="form-control" value="4.5" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="doorsWidthDeduct">إجمالي عرض فتحات الأبواب المخصومة (متر)</label>
        <input type="number" id="doorsWidthDeduct" class="form-control" value="1.0" min="0"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="skirtingWasteRate">نسبة الهالك لقص وتوصيل الزوايا (%)- عادة 5%</label>
        <input type="number" id="skirtingWasteRate" class="form-control" value="5" min="0" max="15" step="1"  oninput="calculateTool()">
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
  0 => 'الوزرة تُحسب بالمتر الطولي وليس بالمتر المربع.',
  1 => 'طول الوزرة الصافي = محيط الغرفة - عرض الأبواب.',
  2 => 'زوايا الأركان 45 درجة تتطلب قص أطراف الوزرة مما يستوجب زيادة هالك 5% إلى 10%.',
),
        'يفترض عدم وجود فتحات أرضية أخرى مثل النوافذ الساقطة حتى الأرض.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أفضل: نعلة السيراميك البارزة أم النعلة المخفية (Flush Baseboard)؟',
    'a' => 'النعلة المخفية تمنح مظهراً عصرياً فائق الأناقة وتمنع تراكم الغبار وتسمح بملاصقة الأثاث للجدار تماماً، لكنها تتطلب تخطيطاً مسبقاً أثناء مرحلة اللياسة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'floor-tiles-calculator',
  1 => 'wall-area-calculator',
  2 => 'carpet-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('skirtingRoomLen').value) || 6.0);
            const wid = Math.max(1, parseFloat(document.getElementById('skirtingRoomWid').value) || 4.5);
            const deduct = Math.max(0, parseFloat(document.getElementById('doorsWidthDeduct').value) || 1.0);
            const waste = Math.max(0, parseFloat(document.getElementById('skirtingWasteRate').value) || 5) / 100;

            const perimeter = 2 * (len + wid);
            const netLength = Math.max(1, perimeter - deduct);
            const totalWithWaste = netLength * (1 + waste);
            const standardPieces = Math.ceil(totalWithWaste / 2.4); // طول القطعة 2.4 م للخشب والفوم

            setPrimaryResult(totalWithWaste.toFixed(2) + ' متر طولي', 'إجمالي طول الوزرة المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'الطول الصافي للوزرة', value: netLength.toFixed(2) + ' متر', color: '#3b82f6' },
                { label: 'محيط الغرفة الكلي', value: perimeter.toFixed(1) + ' متر', color: '#10b981' },
                { label: 'عرض الأبواب المخصوم', value: deduct.toFixed(1) + ' متر', color: '#ef4444' },
                { label: 'عدد الأعواد (للقطع الخشبية/الفوم 2.4م)', value: standardPieces + ' عود', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج إلى <strong>${totalWithWaste.toFixed(2)} متر طولي</strong> من النعلة / الوزرة لتغطية محيط الغرفة بالكامل بعد خصم فتحات الأبواب واحتساب <strong>${(waste*100).toFixed(0)}%</strong> هالك لزوايا الأركان 45 درجة.</p>
            `);
        
        saveLastInputs('skirting-board-calculator');
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
    restoreLastInputs('skirting-board-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>