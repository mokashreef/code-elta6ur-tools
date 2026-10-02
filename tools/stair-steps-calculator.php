<?php
/**
 * أداة: حاسبة عدد درجات السلم
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'stair-steps-calculator';
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
        <label class="form-label" for="floorHeightSteps">ارتفاع الدور من الأرضية للأرضية (سم)</label>
        <input type="number" id="floorHeightSteps" class="form-control" value="320" min="50" max="600" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="riserTarget">ارتفاع الدرجة المفضل (سم)</label>
        <input type="number" id="riserTarget" class="form-control" value="16.0" min="13" max="22" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="landingsCount">عدد البسطات / الصدفات (الاستراحات)</label>
        <input type="number" id="landingsCount" class="form-control" value="1" min="0" max="3" step="1"  oninput="calculateTool()">
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
  0 => 'عدد الدرجات = الارتفاع الكلي للطابق مقسوماً على ارتفاع القائمة.',
  1 => 'يجب أن تكون جميع الدرجات في السلم متطابقة في الارتفاع تماماً دون أي مليمتر فارق لمنع تعثر المستخدمين.',
),
        'الكود الهندسي يمنع وجود أكثر من 14 إلى 16 درجة متتالية دون صدفة / استراحة للراحة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُمنع اختلاف ارتفاع الدرجات في نفس السلم؟',
    'a' => 'لأن العقل البشري يبرمج حركة القدمين لا شعورياً بعد أول درجتين على نفس الإيقاع، وأي تغيير بمقدار 1 سم يؤدي إلى التعثر والسقوط فوراً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'stair-calculator',
  1 => 'floor-tiles-calculator',
  2 => 'house-building-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const h = Math.max(50, parseFloat(document.getElementById('floorHeightSteps').value) || 320);
            const r = Math.max(13, parseFloat(document.getElementById('riserTarget').value) || 16.0);
            const landings = parseInt(document.getElementById('landingsCount').value) || 1;

            const totalSteps = Math.round(h / r);
            const exactRiserHeight = h / totalSteps;
            const stepsPerFlight = Math.ceil(totalSteps / (landings + 1));

            setPrimaryResult(totalSteps + ' درجة', 'إجمالي عدد درجات السلم المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'الارتفاع الفعلي لكل درجة', value: exactRiserHeight.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'عدد القلبات / الشواحط', value: (landings + 1) + ' قلبات', color: '#3b82f6' },
                { label: 'متوسط الدرجات في كل قلبة', value: stepsPerFlight + ' درجات', color: '#f59e0b' },
                { label: 'الارتفاع الكلي للطابق', value: (h / 100).toFixed(2) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية ارتفاع <strong>${h} سم</strong>، تحتاج إلى <strong>${totalSteps} درجة</strong> بارتفاع دقيق <strong>${exactRiserHeight.toFixed(1)} سم</strong> لكل درجة، موزعة على <strong>${landings + 1} قلبات</strong> مع <strong>${landings} استراحة</strong>.</p>
            `);
        
        saveLastInputs('stair-steps-calculator');
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
    restoreLastInputs('stair-steps-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>