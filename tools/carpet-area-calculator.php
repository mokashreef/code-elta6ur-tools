<?php
/**
 * أداة: حاسبة مساحة السجاد والموكيت
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'carpet-area-calculator';
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
        <label class="form-label" for="carpetRoomLen">طول الغرفة (متر)</label>
        <input type="number" id="carpetRoomLen" class="form-control" value="5.5" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="carpetRoomWid">عرض الغرفة (متر)</label>
        <input type="number" id="carpetRoomWid" class="form-control" value="4.0" min="1"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="carpetType">نوع السجاد المطلوب</label>
        <select id="carpetType" class="form-control" onchange="calculateTool()">
            <option value="wall_to_wall" selected>موكيت يغطي الغرفة بالكامل من الجدار للجدار (Wall-to-Wall)</option>
            <option value="area_rug" >سجادة مركزية في وسط الغرفة (تترك 40 سم من الأطراف)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="rollStandardWidth">عرض رول الموكيت القياسي في السوق (المعتاد 4.0 متر)</label>
        <input type="number" id="rollStandardWidth" class="form-control" value="4.0" min="2" max="5" step="0.5"  oninput="calculateTool()">
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
  0 => 'رولات الموكيت تباع في السوق العربي بعرض قياسي قدره 4 أمتار.',
  1 => 'السجادة الوسطية (Area Rug) تترك مسافة 40 إلى 50 سم بين حافة السجادة والجدار لإظهار جمالية أرضية الباركيه أو البورسلان.',
),
        'يفترض غرفة مستطيلة منتظمة بدون أعمدة وزوايا حادة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما أهمية اللباد (Underlay) تحت الموكيت؟',
    'a' => 'اللباد يحمي خيوط الموكيت من التلف نتيجة الاحتكاك بالأرضية الصلبة، ويعطي شعوراً وثيراً وناعماً عند المشي، ويعمل كعازل للصوت والحرارة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'floor-tiles-calculator',
  1 => 'room-furniture-area-calculator',
  2 => 'skirting-board-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const len = Math.max(1, parseFloat(document.getElementById('carpetRoomLen').value) || 5.5);
            const wid = Math.max(1, parseFloat(document.getElementById('carpetRoomWid').value) || 4.0);
            const type = document.getElementById('carpetType').value;
            const rWidth = Math.max(2, parseFloat(document.getElementById('rollStandardWidth').value) || 4.0);

            let netArea = len * wid;
            let finalArea = netArea;
            let linearMeters = 0;

            if (type === 'area_rug') {
                const rugL = Math.max(1, len - 0.8);
                const rugW = Math.max(1, wid - 0.8);
                finalArea = rugL * rugW;
            } else {
                // موكيت كامل: يحسب على رول بعرض 4 أمتار
                linearMeters = len;
                finalArea = linearMeters * rWidth;
            }

            setPrimaryResult(finalArea.toFixed(2) + ' متر مربع', 'مساحة الموكيت / السجاد المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'مساحة أرضية الغرفة الصافية', value: netArea.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'نوع الفرش المعتمد', value: type === 'area_rug' ? 'سجادة وسطية (Rug)' : 'موكيت جدار لجدار', color: '#10b981' },
                { label: 'الأمتار الطولية من رول عرض ' + rWidth + 'م', value: (finalArea / rWidth).toFixed(2) + ' م طولي', color: '#f59e0b' },
                { label: 'مساحة اللباد الإسفنجي (Underlay)', value: netArea.toFixed(1) + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية الغرفة ${type === 'area_rug' ? 'بسجادة وسطية أنيقة' : 'بموكيت كامل'}، تحتاج إلى <strong>${finalArea.toFixed(2)} م²</strong>. يُنصح بإضافة طبقة لباد عازل تحت الموكيت لراحة القدمين وإطالة عمر السجاد.</p>
            `);
        
        saveLastInputs('carpet-area-calculator');
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
    restoreLastInputs('carpet-area-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>