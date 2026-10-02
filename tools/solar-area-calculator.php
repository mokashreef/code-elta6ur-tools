<?php
/**
 * أداة: حاسبة مساحة الألواح الشمسية على السطح
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-area-calculator';
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
        <label class="form-label" for="panelsCountInput">عدد الألواح الشمسية المخطط تركيبها</label>
        <input type="number" id="panelsCountInput" class="form-control" value="16" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roofTypeTilt">نوع السطح وزاوية التركيب</label>
        <select id="roofTypeTilt" class="form-control" onchange="calculateTool()">
            <option value="flat_tilt" selected>سطح خرساني مستوٍ مع قواعد شاسيهات مائلة (يحتاج مسافات تباعد لمنع الظل) ~ 2.8 م²/لوح</option>
            <option value="tilted_roof" >سطح مائل (قرميد أو زنك) يركب عليه اللوح مباشرة بدون تباعد ~ 2.4 م²/لوح</option>
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
  0 => 'الأسطح المستوية تتطلب مسافة تباعد بين صفوف الألواح تعادل مرتين إلى مرتين ونصف ارتفاع اللوح لمنع إلقاء الظل في الشتاء.',
  1 => 'يجب ترك ممرات كافية بين المصفوفات (بعرض 60-80 سم) لتسهيل عمليات الغسيل والصيانة الدورية.',
),
        'أبعاد اللوح الشمسي المعتمدة: 2.28 متر طول × 1.13 متر عرض (ألواح 550-600W الحديثة).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن تركيب الألواح فوق المظلات أو برجولات السطح؟',
    'a' => 'نعم؛ ويعتبر خياراً معمارياً رائعاً لاستغلال مساحة السطح كجلسة مظللة وفي نفس الوقت توليد الطاقة الكهربائية النظيفة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-panels-calculator',
  1 => 'solar-yield-calculator',
  2 => 'solar-system-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const count = Math.max(1, parseInt(document.getElementById('panelsCountInput').value) || 16);
            const type = document.getElementById('roofTypeTilt').value;

            // مساحة اللوح الواحد 550 واط أبعاده 2.28 م × 1.13 م = 2.58 م²
            const netPanelArea = 2.58;
            const factor = type === 'flat_tilt' ? 2.8 : 2.58;
            const totalRequiredArea = count * factor;

            setPrimaryResult(Math.ceil(totalRequiredArea) + ' متر مربع', 'المساحة المطلوبة على السطح');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للألواح فقط', value: (count * netPanelArea).toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة ممرات الصيانة والتباعد لمنع الظلال', value: (totalRequiredArea - (count * netPanelArea)).toFixed(1) + ' م²', color: '#10b981' },
                { label: 'عدد الألواح الإجمالي', value: count + ' لوح', color: '#f59e0b' },
                { label: 'الوزن التقريبي للألواح والشاسيهات', value: Math.round(count * 32) + ' كجم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتركيب <strong>${count} ألواح شمسية</strong> على ${type === 'flat_tilt' ? 'سطح خرساني بقواعد مائلة' : 'سطح مائل مباشر'}، تحتاج إلى مساحة حرة خالية من العوائق والظلال قدرها <strong>${Math.ceil(totalRequiredArea)} متر مربع</strong> لضمان ممرات تنظيف وتفادي ظلال الصفوف الأمامية على الخلفية.</p>
            `);
        
        saveLastInputs('solar-area-calculator');
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
    restoreLastInputs('solar-area-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>