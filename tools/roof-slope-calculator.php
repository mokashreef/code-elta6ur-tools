<?php
/**
 * أداة: حاسبة ميلان السطح وتصريف المياه (خرسانة الميول)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'roof-slope-calculator';
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
        <label class="form-label" for="roofRunLength">المسافة من أعلى نقطة في السطح إلى المزراب / المزراب (متر)</label>
        <input type="number" id="roofRunLength" class="form-control" value="12" min="1"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="slopePercentage">نسبة الميل الموصى بها (%) - القياسي 1% إلى 1.5%</label>
        <input type="number" id="slopePercentage" class="form-control" value="1.0" min="0.5" max="5.0" step="0.25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="minThicknessAtDrain">أقل سماكة لخرسانة الميول عند المزراب (سم) - عادة 3 إلى 5 سم</label>
        <input type="number" id="minThicknessAtDrain" class="form-control" value="4.0" min="2" max="10" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roofTotalArea">إجمالي مساحة السطح المراد صبه (متر مربع)</label>
        <input type="number" id="roofTotalArea" class="form-control" value="150" min="10"  step="5"  oninput="calculateTool()">
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
  0 => 'فارق المنسوب = المسافة الأفقية × نسبة الميل.',
  1 => 'نسبة الميل القياسية لتصريف مياه الأمطار المعمارية تتراوح بين 1% (1 سم لكل متر) إلى 1.5% (1.5 سم لكل متر).',
  2 => 'يُفضل استخدام الخرسانة الرغوية (Foam Concrete) لعمل الميول لأنها خفيفة الوزن وتوفر عزلاً حرارياً إضافياً دون تحميل السقف أوزاناً زائدة.',
),
        'يفترض توزيع مدروس لمزاريب تصريف الأمطار (مزراب لكل 80-100 م² كحد أقصى).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يحدث إذا كانت نسبة ميل السطح أقل من 1%؟',
    'a' => 'ستتكون برك مياه راكدة على السطح بعد الأمطار (Water Ponding)، وهو ما يؤدي بمرور الوقت إلى تحلل وتلف طبقات العزل وتسرب المياه للمنزل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'waterproofing-calculator',
  1 => 'insulation-calculator',
  2 => 'cement-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const run = Math.max(1, parseFloat(document.getElementById('roofRunLength').value) || 12);
            const slope = Math.max(0.5, parseFloat(document.getElementById('slopePercentage').value) || 1.0) / 100;
            const minThick = Math.max(2, parseFloat(document.getElementById('minThicknessAtDrain').value) || 4.0);
            const area = Math.max(10, parseFloat(document.getElementById('roofTotalArea').value) || 150);

            const dropHeightCm = (run * slope) * 100;
            const maxThicknessCm = minThick + dropHeightCm;
            const avgThicknessCm = (minThick + maxThicknessCm) / 2;
            const foamConcreteVolumeM3 = area * (avgThicknessCm / 100);

            setPrimaryResult(dropHeightCm.toFixed(1) + ' سم فارق منسوب الميول', 'فارق الارتفاع المطلوب لتصريف المياه');
            showResultArea();

            setDetailStats([
                { label: 'أعلى سماكة للصبة (عند أعلى نقطة)', value: maxThicknessCm.toFixed(1) + ' سم', color: '#ef4444' },
                { label: 'أقل سماكة (عند مخرج المزراب)', value: minThick.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'متوسط سماكة صبة الميول', value: avgThicknessCm.toFixed(1) + ' سم', color: '#f59e0b' },
                { label: 'حجم الخرسانة الرغوية المطلوبة', value: foamConcreteVolumeM3.toFixed(2) + ' م³', color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>على مسافة <strong>${run} متر</strong> بنسبة ميل <strong>${(slope*100).toFixed(1)}%</strong>، يجب أن يرتفع السطح بمقدار <strong>${dropHeightCm.toFixed(1)} سم</strong> فوق المزراب. يتطلب صب السطح حوالي <strong>${foamConcreteVolumeM3.toFixed(2)} م³</strong> من الخرسانة الرغوية الخفيفة.</p>
            `);
        
        saveLastInputs('roof-slope-calculator');
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
    restoreLastInputs('roof-slope-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>