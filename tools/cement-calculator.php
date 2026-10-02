<?php
/**
 * أداة: حاسبة كمية الإسمنت للخرسانة والمحارة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cement-calculator';
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
        <label class="form-label" for="workType">نوع العمل الخرساني</label>
        <select id="workType" class="form-control" onchange="calculateTool()">
            <option value="reinforced" selected>خرسانة مسلحة (أسقف، أعمدة، كمرات) - 350 كجم/م³ (7 شكائر)</option>
            <option value="plain" >خرسانة عادية (نظافة، أرضيات) - 250 كجم/م³ (5 شكائر)</option>
            <option value="plaster" >محارة ولياسة جدران - 300 كجم/م³ رمل</option>
            <option value="mortar" >مونة بناء البلوك والطوب</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="volumeOrArea">الحجم بالمتر المكعب (م³) - أو المساحة للمحارة بالمتر المربع (م²)</label>
        <input type="number" id="volumeOrArea" class="form-control" value="15" min="0.5"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="plasterThickness">سماكة المحارة (سم) - إذا اخترت محارة فقط</label>
        <input type="number" id="plasterThickness" class="form-control" value="2.5" min="1" max="5" step="0.5"  oninput="calculateTool()">
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
  0 => 'الخرسانة المسلحة القياسية تستهلك 7 شكائر إسمنت (350 كجم) لكل متر مكعب.',
  1 => 'الخرسانة العادية تستهلك 5 شكائر إسمنت (250 كجم) لكل متر مكعب.',
  2 => 'وزن شيكارة الإسمنت القياسية عالمياً هو 50 كجم (20 شيكارة تعادل 1 طن).',
),
        'النسب مطابقة للمواصفات الهندسية والكود العربي الموحد للخرسانة المسلحة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم شيكارة إسمنت في الطن الواحد؟',
    'a' => 'الطن يحتوي بالضبط على 20 شيكارة إسمنت سعة كل شيكارة 50 كجم.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sand-calculator',
  1 => 'gravel-calculator',
  2 => 'house-building-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const type = document.getElementById('workType').value;
            const inputVal = Math.max(0.5, parseFloat(document.getElementById('volumeOrArea').value) || 15);
            const thick = Math.max(1, parseFloat(document.getElementById('plasterThickness').value) || 2.5);

            let totalVolumeM3 = inputVal;
            let kgPerM3 = 350;

            if (type === 'plain') kgPerM3 = 250;
            if (type === 'plaster') {
                // سمك المحارة يحول المساحة لحجم
                totalVolumeM3 = inputVal * (thick / 100);
                kgPerM3 = 300;
            }
            if (type === 'mortar') kgPerM3 = 300;

            const totalKg = totalVolumeM3 * kgPerM3;
            const bagsCount = Math.ceil(totalKg / 50); // شيكارة الإسمنت 50 كجم
            const tonsCount = totalKg / 1000;

            setPrimaryResult(bagsCount + ' شيكارة إسمنت (' + tonsCount.toFixed(2) + ' طن)', 'كمية الإسمنت المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي حجم المونة أو الخرسانة', value: totalVolumeM3.toFixed(2) + ' م³', color: '#3b82f6' },
                { label: 'الوزن الصافي للإسمنت', value: Math.ceil(totalKg) + ' كجم', color: '#10b981' },
                { label: 'معيار الإسمنت المعتمد', value: kgPerM3 + ' كجم/م³', color: '#f59e0b' },
                { label: 'عدد الشكائر سعة 50 كجم', value: bagsCount + ' شيكارة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتنفيذ حجم قدره <strong>${totalVolumeM3.toFixed(2)} متر مكعب</strong> بمعدل <strong>${kgPerM3} كجم/م³</strong>، تحتاج إلى <strong>${bagsCount} شيكارة إسمنت</strong> (ما يعادل <strong>${tonsCount.toFixed(2)} طن</strong>).</p>
            `);
        
        saveLastInputs('cement-calculator');
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
    restoreLastInputs('cement-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>