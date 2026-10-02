<?php
/**
 * أداة: حاسبة عدد ساعات المشروع (طريقة PERT)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'project-hours-calculator';
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
        <label class="form-label" for="optimisticHours">الوقت المتفائل (إذا سار كل شيء بسلاسة وبدون أي عوائق)</label>
        <input type="number" id="optimisticHours" class="form-control" value="20" min="1"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="realisticHours">الوقت الأكثر ترجيحاً وواقعية (التقدير المعتاد)</label>
        <input type="number" id="realisticHours" class="form-control" value="35" min="1"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="pessimisticHours">الوقت المتشائم (إذا حدثت مشاكل تقنية وتأخيرات غير متوقعة)</label>
        <input type="number" id="pessimisticHours" class="form-control" value="65" min="1"  step="1" oninput="calculateTool()">
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
  0 => 'معادلة PERT المعتمدة دولياً: الوقت المتوقع = (الوقت المتفائل + 4 × الوقت الواقعي + الوقت المتشائم) ÷ 6.',
  1 => 'تمنع هذه الطريقة الوقوع في فخ التفاؤل المفرط وتضمن تسليم المشاريع في مواعيدها المحددة.',
),
        'تستخدم هذه الطريقة من قِبل مدراء المشاريع المحترفين (PMP) لتسعير وجدولة المهام المعقدة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُعطى الوقت الواقعي وزن 4 أضعاف؟',
    'a' => 'لأنه في التوزيع الاحتمالي الإحصائي (Beta Distribution)، يكون السيناريو المرجح هو الأقرب للحدوث، مع مراعاة احتمالات الطوارئ المتشائمة بنسبة محسوبة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'freelancer-project-price-calculator',
  1 => 'dev-pricing-calculator',
  2 => 'design-pricing-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const opt = Math.max(1, parseFloat(document.getElementById('optimisticHours').value) || 1);
            const real = Math.max(opt, parseFloat(document.getElementById('realisticHours').value) || opt);
            const pess = Math.max(real, parseFloat(document.getElementById('pessimisticHours').value) || real);

            // PERT Formula: Expected = (O + 4M + P) / 6
            const expectedHours = (opt + (4 * real) + pess) / 6;
            // Standard deviation = (P - O) / 6
            const sd = (pess - opt) / 6;

            setPrimaryResult(expectedHours.toFixed(1) + ' ساعة عمل', 'الوقت المتوقع بدقة علمية (PERT)');
            showResultArea();

            setDetailStats([
                { label: 'النطاق الآمن للإنجاز (احتمالية 95%)', value: (expectedHours - 2*sd).toFixed(0) + ' إلى ' + (expectedHours + 2*sd).toFixed(0) + ' ساعة', color: '#10b981' },
                { label: 'التقدير الواقعي المباشر', value: real + ' ساعة', color: '#3b82f6' },
                { label: 'أيام العمل المقدرة (بمعدل 5 ساعات يومياً)', value: (expectedHours / 5).toFixed(1) + ' يوم', color: '#8b5cf6' },
                { label: 'عامل المخاطرة والتقلب في المشروع', value: '±' + sd.toFixed(1) + ' ساعة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>بناءً على نموذج PERT لإدارة المشاريع الهندسية والبرمجية، التقدير الزمني الأكثر أماناً للتسليم هو <strong>${expectedHours.toFixed(1)} ساعة</strong>، ونطاق الأمان يتراوح بين <strong>${(expectedHours - sd).toFixed(0)}</strong> و <strong>${(expectedHours + sd).toFixed(0)} ساعة</strong>.</p>
            `);
        
        saveLastInputs('project-hours-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('project-hours-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>