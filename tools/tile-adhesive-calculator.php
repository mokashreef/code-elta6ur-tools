<?php
/**
 * أداة: حاسبة كمية الغراء للبلاط والبورسلان
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'tile-adhesive-calculator';
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
        <label class="form-label" for="tileAreaAdhesive">المساحة المطلوب تبليطها بالغراء (متر مربع)</label>
        <input type="number" id="tileAreaAdhesive" class="form-control" value="40" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="notchTrowelSize">حجم أسنان المالج (البروة) وسمك الغراء</label>
        <select id="notchTrowelSize" class="form-control" onchange="calculateTool()">
            <option value="small" >مالج 6 مم (بلاط صغير وسيراميك عادي) ~ 3.5 كجم/م²</option>
            <option value="medium" selected>مالج 8-10 مم (بورسلان 60×60 سم) ~ 5 كجم/م²</option>
            <option value="large" >مالج 12 مم أو دبل دهان (بورسلان كبير 60×120 سم) ~ 7 كجم/م²</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="bagWeight">وزن شيكارة الغراء (كجم) - الشائع 20 أو 25 كجم</label>
        <input type="number" id="bagWeight" class="form-control" value="20" min="10" max="50" step="5"  oninput="calculateTool()">
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
  0 => 'البورسلان قليل الامتصاص ويجب تركيبه بغراء بوليمري مخصص C2TE.',
  1 => 'البلاط كبير الحجم (أكبر من 60×60) يتطلب دهان الغراء على الأرضية وظهر البلاطة معاً (Back Buttering).',
),
        'يفترض أرضية مستوية تماماً مجهزة بصبة سكريد ناعمة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن لصق البورسلان بالإسمنت العادي؟',
    'a' => 'ممنوع هندسياً؛ لأن البورسلان لا يمتص الماء بنسبة 99% وبالتالي ينفصل (يطبل) بعد فترة قصيرة إذا رُكب بالأسمنت بدون غراء عالي الجودة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ceramic-calculator',
  1 => 'floor-tiles-calculator',
  2 => 'grout-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('tileAreaAdhesive').value) || 40);
            const trowel = document.getElementById('notchTrowelSize').value;
            const bag = Math.max(10, parseFloat(document.getElementById('bagWeight').value) || 20);

            let rate = 5.0; // كجم / م²
            if (trowel === 'small') rate = 3.5;
            if (trowel === 'large') rate = 7.0;

            const totalKg = area * rate;
            const bagsCount = Math.ceil(totalKg / bag);

            setPrimaryResult(bagsCount + ' شيكارة غراء (' + totalKg + ' كجم)', 'كمية غراء البلاط المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الوزن الصافي المطلوب', value: totalKg + ' كجم', color: '#3b82f6' },
                { label: 'المساحة المراد لصقها', value: area + ' م²', color: '#10b981' },
                { label: 'معدل الاستهلاك لكل م²', value: rate + ' كجم / م²', color: '#f59e0b' },
                { label: 'وزن الشيكارة المعتمدة', value: bag + ' كجم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للصق مساحة <strong>${area} م²</strong> بمعدل استهلاك <strong>${rate} كجم/م²</strong>، تحتاج إلى <strong>${bagsCount} شيكارة غراء</strong> سعة ${bag} كجم.</p>
            `);
        
        saveLastInputs('tile-adhesive-calculator');
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
    restoreLastInputs('tile-adhesive-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>