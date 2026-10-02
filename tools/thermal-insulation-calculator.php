<?php
/**
 * أداة: حاسبة كمية العزل الحراري
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'thermal-insulation-calculator';
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
        <label class="form-label" for="thermalArea">مساحة الأسطح أو الجدران المطلوب عزلها حرارياً (م²)</label>
        <input type="number" id="thermalArea" class="form-control" value="150" min="1"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="boardType">نوع العازل الحراري</label>
        <select id="boardType" class="form-control" onchange="calculateTool()">
            <option value="xps" selected>بوليستيرين مبثوق (XPS أزرق/وردي) - كثافة 35 كجم/م³</option>
            <option value="rockwool" >صوف صخري (Rockwool) - عزل حراري وصوتي ومقاوم للحريق</option>
            <option value="polyurethane" >رغوة فوم بولي يوريثان رش (Spray Foam)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="thicknessCm">سماكة العازل (سم) - الموصى به 5 إلى 7 سم</label>
        <input type="number" id="thicknessCm" class="form-control" value="5.0" min="2" max="15" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="boardSize">مقاس لوح العزل (متر) - الشائع 1.25 × 0.60 م = 0.75 م²</label>
        <input type="number" id="boardSize" class="form-control" value="0.75" min="0.5" max="3" step="0.05"  oninput="calculateTool()">
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
  0 => 'سماكة 5 سم من البوليستيرين المبثوق (XPS) تعادل جداراً خرسانياً بسماكة تزيد عن متر في كفاءة العزل الحراري.',
  1 => 'عزل الأسطح والجدران يخفض فاتورة كهرباء التكييف بنسبة تصل إلى 40%.',
),
        'مطابق للاشتراطات الفنية لكود البناء السعودي ولائحة كفاءة الطاقة للمباني السكنية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما يوضع أولاً على السطح: العازل المائي أم الحراري؟',
    'a' => 'في نظام السطح المقلوب (Inverted Roof) الأكثر أماناً، يوضع العازل المائي أولاً فوق الخرسانة وميول التصريف، ثم يوضع فوقه العازل الحراري لحماية المائي من حرارة الشمس المباشرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'waterproofing-calculator',
  1 => 'insulation-calculator',
  2 => 'roof-slope-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('thermalArea').value) || 150);
            const type = document.getElementById('boardType').value;
            const thick = Math.max(2, parseFloat(document.getElementById('thicknessCm').value) || 5.0);
            const bSize = Math.max(0.5, parseFloat(document.getElementById('boardSize').value) || 0.75);

            const areaWithWaste = area * 1.05; // 5% هالك
            const boardsCount = Math.ceil(areaWithWaste / bSize);
            const volumeM3 = area * (thick / 100);

            setPrimaryResult(type === 'polyurethane' ? volumeM3.toFixed(2) + ' م³ فوم' : boardsCount + ' لوح عازل', 'الكمية المطلوبة من العازل الحراري');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية', value: area + ' م²', color: '#3b82f6' },
                { label: 'حجم العازل بالمتر المكعب', value: volumeM3.toFixed(2) + ' م³', color: '#10b981' },
                { label: 'سماكة العزل المعتمدة', value: thick + ' سم', color: '#f59e0b' },
                { label: 'عدد الألواح التقريبي', value: boardsCount + ' لوح', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل مساحة <strong>${area} م²</strong> بسماكة <strong>${thick} سم</strong>، تحتاج إلى <strong>${boardsCount} لوح</strong> بمقاس ${bSize} م² (أو <strong>${volumeM3.toFixed(2)} متر مكعب</strong> من مادة العزل)، وهو ما يوفر حتى 40% من استهلاك مكيفات الهواء صيفاً.</p>
            `);
        
        saveLastInputs('thermal-insulation-calculator');
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
    restoreLastInputs('thermal-insulation-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>