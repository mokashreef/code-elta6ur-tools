<?php
/**
 * أداة: حاسبة توزيع العلامات التكتيكي للوصول لمعدل معين
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grade-distribution-calculator';
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
        <label class="form-label" for="targetSemesterGpaTactical">المعدل الفصلي المستهدف (GPA من 4.0)</label>
        <input type="number" id="targetSemesterGpaTactical" class="form-control" value="3.50" min="2.0" max="4.0" step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="totalSubjectsCount">عدد مواد الفصل الدراسي (بافتراض 3 ساعات لكل مادة)</label>
        <input type="number" id="totalSubjectsCount" class="form-control" value="5" min="2" max="8" step="1"  oninput="calculateTool()">
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
  0 => 'التوزيع التكتيكي يساعدك على توجيه طاقتك الذهنية إلى المواد ذات الأثر الأكبر أو المواد السهلة لضمان تقدير A فيها.',
  1 => 'الحصول على تقدير B في مادة معقدة لا يمنعك من تحقيق معدل امتياز إذا وازنته بتقديرات A في بقية المواد.',
),
        'يفترض تساوي الساعات المعتمدة للمواد (3 ساعات لكل مقرر).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أختار المواد التي أركز عليها لرفع المعدل؟',
    'a' => 'ركز على المواد ذات الساعات المعتمدة الأعلى (مثلاً 4 ساعات) ومقررات المتطلبات العامة السهلة لرفع النقاط التراكمية بأقل مخاطرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'target-gpa-calculator',
  1 => 'cumulative-gpa-calculator',
  2 => 'next-semester-gpa-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const target = Math.max(2.0, Math.min(4.0, parseFloat(document.getElementById('targetSemesterGpaTactical').value) || 3.50));
            const count = Math.max(2, parseInt(document.getElementById('totalSubjectsCount').value) || 5);

            // توزيع الدرجات المقترح:
            // إذا كان الهدف 3.5: يحتاج مثلاً 3 مواد A (4.0) و 2 مواد B (3.0) -> (12 + 6)/5 = 3.6
            const totalPointsNeeded = target * count;
            let aCount = 0;
            let bCount = 0;
            let cCount = 0;

            for (let a = count; a >= 0; a--) {
                for (let b = count - a; b >= 0; b--) {
                    let c = count - a - b;
                    let pts = (a * 4.0) + (b * 3.0) + (c * 2.0);
                    if (pts >= totalPointsNeeded) {
                        aCount = a;
                        bCount = b;
                        cCount = c;
                    }
                }
            }

            const achievedGpa = ((aCount * 4.0) + (bCount * 3.0) + (cCount * 2.0)) / count;

            setPrimaryResult(aCount + ' مواد (A) + ' + bCount + ' مواد (B)' + (cCount > 0 ? ' + ' + cCount + ' مواد (C)' : ''), 'التوزيع التكتيكي المقترح لدرجات المواد');
            showResultArea();

            setDetailStats([
                { label: 'عدد المواد بتقدير ممتاز A (4.0)', value: aCount + ' مواد', color: '#10b981' },
                { label: 'عدد المواد بتقدير جيد جداً B (3.0)', value: bCount + ' مواد', color: '#3b82f6' },
                { label: 'عدد المواد بتقدير جيد C (2.0)', value: cCount + ' مواد', color: '#f59e0b' },
                { label: 'المعدل الناتج المتوقع', value: achievedGpa.toFixed(2) + ' / 4.0', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتحقيق معدل <strong>${target}</strong> عبر <strong>${count} مواد</strong>، يمكنك توزيع مجهودك بذكاء بالحصول على <strong>${aCount} مواد بتقدير A</strong> في المواد السهلة و <strong>${bCount} مواد بتقدير B</strong> في المواد الصعبة، لتحقق معدلاً نهائياً <strong>${achievedGpa.toFixed(2)}</strong>.</p>
            `);
        
        saveLastInputs('grade-distribution-calculator');
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
    restoreLastInputs('grade-distribution-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>