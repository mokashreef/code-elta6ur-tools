<?php
/**
 * أداة: حاسبة حجم UPS المناسب
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ups-size-calculator';
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
        <label class="form-label" for="totalLoadWatts">إجمالي قدرة الأجهزة المراد حمايتها (بالواط Watt)</label>
        <input type="number" id="totalLoadWatts" class="form-control" value="600" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="powerFactor">معامل القدرة (Power Factor) - المعتاد 0.7 إلى 0.8</label>
        <input type="number" id="powerFactor" class="form-control" value="0.7" min="0.5" max="1.0" step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="safetyMargin">هامش الأمان والتوسع المستقبلي (%)- الموصى به 25%</label>
        <input type="number" id="safetyMargin" class="form-control" value="25" min="10" max="50" step="5"  oninput="calculateTool()">
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
  0 => 'القدرة الظاهرية (VA) = القدرة الفعالة (الواط) ÷ معامل القدرة (Power Factor).',
  1 => 'إضافة هامش أمان 25% ضروري جداً لتحمل تيارات البدء المفاجئة ولإمكانية إضافة أجهزة جديدة مستقبلاً.',
),
        'معظم أجهزة الكمبيوتر والسيرفرات المنزلية لها معامل قدرة يتراوح بين 0.65 إلى 0.75.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفرق بين الواط (Watt) والفولت أمبير (VA)؟',
    'a' => 'الواط هو القدرة الحقيقية المستهلكة من الجهاز، بينما الـ VA هو حاصل ضرب الجهد في التيار في دوائر التيار المتردد، وعادة ما تكون قيمة الـ VA أعلى من الواط بسبب المقاومة الحثية والسعوية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ups-runtime-calculator',
  1 => 'inverter-size-calculator',
  2 => 'battery-count-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const watts = Math.max(50, parseFloat(document.getElementById('totalLoadWatts').value) || 600);
            const pf = Math.max(0.5, Math.min(1.0, parseFloat(document.getElementById('powerFactor').value) || 0.7));
            const margin = Math.max(10, parseFloat(document.getElementById('safetyMargin').value) || 25) / 100;

            const wattsWithMargin = watts * (1 + margin);
            const requiredVA = wattsWithMargin / pf;
            // أحجام الـ UPS القياسية في السوق
            const standardSizes = [650, 850, 1000, 1200, 1500, 2000, 3000, 5000, 6000, 10000];
            let recommendedSize = standardSizes.find(s => s >= requiredVA) || Math.ceil(requiredVA / 1000) * 1000;

            setPrimaryResult(recommendedSize + ' VA (فولت أمبير)', 'الحجم القياسي الموصى به لـ UPS');
            showResultArea();

            setDetailStats([
                { label: 'القدرة الحسابية الدقيقة المطلوبة', value: Math.ceil(requiredVA) + ' VA', color: '#3b82f6' },
                { label: 'إجمالي الحمل بالواط مع الأمان', value: Math.ceil(wattsWithMargin) + ' واط', color: '#10b981' },
                { label: 'أقصى حمل صافي للجهاز المقترح', value: Math.round(recommendedSize * pf) + ' واط', color: '#f59e0b' },
                { label: 'معامل القدرة المعتمد', value: pf.toFixed(2), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتشغيل أحمال فعلية قدرها <strong>${watts} واط</strong> بأمان واستقرار، تحتاج إلى وحدة UPS بقدرة لا تقل عن <strong>${recommendedSize} VA</strong> لتفادي التحميل الزائد عند انقطاع التيار.</p>
            `);
        
        saveLastInputs('ups-size-calculator');
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
    restoreLastInputs('ups-size-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>