<?php
/**
 * أداة: حاسبة العلامة المطلوبة للنجاح في المادة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'passing-grade-calculator';
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
        <label class="form-label" for="courseworkScore">مجموع درجات أعمال السنة والامتحان النصفي التي حصلت عليها</label>
        <input type="number" id="courseworkScore" class="form-control" value="28" min="0" max="100" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="courseworkMax">الدرجة العظمى لأعمال السنة (المعتاد 40 إلى 50 درجة)</label>
        <input type="number" id="courseworkMax" class="form-control" value="40" min="10" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="passingScoreNeeded">درجة النجاح الكلية المشروطة في المادة (المعتاد 50 أو 60)</label>
        <input type="number" id="passingScoreNeeded" class="form-control" value="60" min="40" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="finalExamMax">الدرجة العظمى للامتحان النهائي (Final Exam)</label>
        <input type="number" id="finalExamMax" class="form-control" value="60" min="10" max="100" step="5"  oninput="calculateTool()">
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
  0 => 'الدرجة المطلوبة في النهائي = درجة النجاح المطلوبة - مجموع درجات أعمال السنة المحققة.',
  1 => 'انتبه إلى اشتراط بعض الجامعات حصول الطالب على نسبة معينة في الامتحان النهائي كشرط نجاح مستقل (مثلاً 30% من ورقة الفاينل).',
),
        'يفترض عدم وجود شرط رسوب منفصل للورقة الامتحانية النهائية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا لو كانت درجتي الحالية أعلى من درجة النجاح بالفعل؟',
    'a' => 'إذا كانت درجات أعمالك تجاوزت 60 درجة مسبقاً، فأنت ناجح في المادة رسمياً، ولكن حضور الامتحان النهائي يظل إلزامياً للحصول على تقدير مرتفع ولمنع الحرمان.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'final-grade-calculator',
  1 => 'target-gpa-calculator',
  2 => 'grade-percentage-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const current = Math.max(0, parseFloat(document.getElementById('courseworkScore').value) || 0);
            const cwMax = Math.max(10, parseFloat(document.getElementById('courseworkMax').value) || 40);
            const passReq = Math.max(40, parseFloat(document.getElementById('passingScoreNeeded').value) || 60);
            const finalMax = Math.max(10, parseFloat(document.getElementById('finalExamMax').value) || 60);

            const neededFinal = Math.max(0, passReq - current);
            const neededPercentageOfFinal = (neededFinal / finalMax) * 100;

            let status = 'فرصة ممتازة وسهلة التحقيق ✅';
            let color = '#10b981';
            if (neededFinal > finalMax) { status = 'للأسف يستحيل النجاح حتى بالدرجة النهائية الكاملة ❌'; color = '#ef4444'; }
            else if (neededPercentageOfFinal > 75) { status = 'صعبة وتحتاج لمذاكرة مركزة جداً ⚠️'; color = '#f59e0b'; }

            setPrimaryResult(neededFinal.toFixed(1) + ' من ' + finalMax + ' (' + neededPercentageOfFinal.toFixed(1) + '%)', 'الدرجة المطلوبة في الفاينل للنجاح');
            showResultArea();

            setDetailStats([
                { label: 'العلامة التي تضمن النجاح بالضبط', value: neededFinal.toFixed(1) + ' درجة', color: '#3b82f6' },
                { label: 'نسبة الإنجاز المطلوبة من ورقة الفاينل', value: neededPercentageOfFinal.toFixed(1) + '%', color: '#10b981' },
                { label: 'تقييم فرصة النجاح', value: status, color: color },
                { label: 'مجموع درجاتك الحالي', value: current + ' من ' + cwMax, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج للحصول على <strong>${neededFinal.toFixed(1)} درجة من أصل ${finalMax}</strong> في الامتحان النهائي (أي إجابة صحيحة بنسبة <strong>${neededPercentageOfFinal.toFixed(1)}%</strong> من أسئلة الامتحان) للوصول لدرجة النجاح ${passReq}.</p>
            `);
        
        saveLastInputs('passing-grade-calculator');
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
    restoreLastInputs('passing-grade-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>