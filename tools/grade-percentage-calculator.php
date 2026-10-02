<?php
/**
 * أداة: حاسبة النسبة من العلامات والتقدير الأكاديمي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grade-percentage-calculator';
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
        <label class="form-label" for="studentScore">العلامة / الدرجة التي حصلت عليها</label>
        <input type="number" id="studentScore" class="form-control" value="88" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="maxScore">الدرجة الكلية العظمى للامتحان</label>
        <input type="number" id="maxScore" class="form-control" value="100" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'النسبة المئوية = (درجة الطالب ÷ الدرجة العظمى) × 100.',
  1 => 'التقديرات المعتمدة مطابقة لمعايير الجامعات العربية والسلالم الأكاديمية الدولية.',
),
        'يفترض حد أدنى للنجاح 60% لمعظم الكليات الجامعية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحول النسبة المئوية لمعدل من 4 أو 5؟',
    'a' => 'استخدم حاسبة المعدل التراكمي GPA المخصصة أو جدول التحويل المعياري للأحرف والرموز.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'passing-grade-calculator',
  1 => 'target-gpa-calculator',
  2 => 'cumulative-gpa-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const score = Math.max(0, parseFloat(document.getElementById('studentScore').value) || 0);
            const max = Math.max(1, parseFloat(document.getElementById('maxScore').value) || 100);

            const percentage = (score / max) * 100;
            let rating = 'راسب ❌';
            let gpa4 = 0;
            let letter = 'F';
            let color = '#ef4444';

            if (percentage >= 95) { rating = 'ممتاز مرتفع (A+) ⭐'; gpa4 = 4.0; letter = 'A+'; color = '#10b981'; }
            else if (percentage >= 90) { rating = 'ممتاز (A) 🌟'; gpa4 = 3.75; letter = 'A'; color = '#10b981'; }
            else if (percentage >= 85) { rating = 'جيد جداً مرتفع (B+)'; gpa4 = 3.5; letter = 'B+'; color = '#3b82f6'; }
            else if (percentage >= 80) { rating = 'جيد جداً (B)'; gpa4 = 3.0; letter = 'B'; color = '#3b82f6'; }
            else if (percentage >= 75) { rating = 'جيد مرتفع (C+)'; gpa4 = 2.5; letter = 'C+'; color = '#f59e0b'; }
            else if (percentage >= 70) { rating = 'جيد (C)'; gpa4 = 2.0; letter = 'C'; color = '#f59e0b'; }
            else if (percentage >= 65) { rating = 'مقبول مرتفع (D+)'; gpa4 = 1.5; letter = 'D+'; color = '#f59e0b'; }
            else if (percentage >= 60) { rating = 'مقبول (D)'; gpa4 = 1.0; letter = 'D'; color = '#f59e0b'; }

            setPrimaryResult(percentage.toFixed(2) + '% (' + letter + ')', 'النسبة المئوية والتقدير الأكاديمي');
            showResultArea();

            setDetailStats([
                { label: 'التقدير العام المعتمد', value: rating, color: color },
                { label: 'المعدل المكافئ من 4.0 (GPA)', value: gpa4.toFixed(2) + ' / 4.0', color: '#3b82f6' },
                { label: 'الدرجات المفقودة من الدرجة النهائية', value: (max - score).toFixed(1) + ' درجة', color: '#ef4444' },
                { label: 'الدرجة المدخلة', value: score + ' من ' + max, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>نسبتك المئوية هي <strong>${percentage.toFixed(2)}%</strong> بتقدير <strong>${rating}</strong> ومعدل <strong>${gpa4.toFixed(2)} من 4.0</strong>.</p>
            `);
        
        saveLastInputs('grade-percentage-calculator');
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
    restoreLastInputs('grade-percentage-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>