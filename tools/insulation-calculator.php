<?php
/**
 * أداة: حاسبة كمية العزل الشاملة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'insulation-calculator';
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
        <label class="form-label" for="insulationArea">المساحة المراد عزلها (متر مربع)</label>
        <input type="number" id="insulationArea" class="form-control" value="120" min="1"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="insulationLayers">عدد طبقات العزل</label>
        <input type="number" id="insulationLayers" class="form-control" value="2" min="1" max="4" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="overlapRate">نسبة ركوب الفواصل (Overlap) والرقبة (%)- عادة 10%</label>
        <input type="number" id="overlapRate" class="form-control" value="10" min="5" max="25" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="rollCoverage">المساحة الصافية للرول الواحد (م²) - الشائع 10 م²</label>
        <input type="number" id="rollCoverage" class="form-control" value="10" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'لفائف الممبرين العازل تتطلب ركوباً لا يقل عن 10 سم بين اللفة والأخرى لضمان منع التسرب.',
  1 => 'يجب رفع العزل على الجدران المحيطة (الوزرة / رقبة الزجاجة) بارتفاع لا يقل عن 20 إلى 30 سم.',
),
        'يفترض سطحاً نظيفاً معالجاً بالبرايمر البيتوميني قبل اللحام باللهب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم مدة اختبار العزل بالماء للأسطح والحمامات؟',
    'a' => 'يجب غمر السطح أو الحمام بالماء لارتفاع 10-15 سم لمدة 48 ساعة متواصلة على الأقل للتأكد من عدم وجود أي رشح أو تنميل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'waterproofing-calculator',
  1 => 'thermal-insulation-calculator',
  2 => 'roof-slope-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('insulationArea').value) || 120);
            const layers = Math.max(1, parseInt(document.getElementById('insulationLayers').value) || 2);
            const overlap = Math.max(5, parseFloat(document.getElementById('overlapRate').value) || 10) / 100;
            const rollCov = Math.max(1, parseFloat(document.getElementById('rollCoverage').value) || 10);

            const totalCoverageArea = area * layers;
            const totalWithOverlap = totalCoverageArea * (1 + overlap);
            const rollsCount = Math.ceil(totalWithOverlap / rollCov);

            setPrimaryResult(rollsCount + ' رول عزل (' + totalWithOverlap.toFixed(1) + ' م²)', 'كمية لفائف العزل المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للسطح', value: area + ' م²', color: '#3b82f6' },
                { label: 'إجمالي مساحة الطبقات (' + layers + ' طبقات)', value: totalCoverageArea + ' م²', color: '#10b981' },
                { label: 'مخصص ركوب الفواصل (10 سم)', value: (totalCoverageArea * overlap).toFixed(1) + ' م²', color: '#f59e0b' },
                { label: 'مساحة الرول الواحد', value: rollCov + ' م²', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل مساحة <strong>${area} م²</strong> بعدد <strong>${layers} طبقات</strong> مع ركوب فواصل 10 سم، تحتاج إلى <strong>${rollsCount} رول</strong> لتغطية إجمالي مساحة <strong>${totalWithOverlap.toFixed(1)} متر مربع</strong>.</p>
            `);
        
        saveLastInputs('insulation-calculator');
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
    restoreLastInputs('insulation-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>