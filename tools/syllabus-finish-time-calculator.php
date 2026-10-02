<?php
/**
 * أداة: حاسبة وقت إنهاء المنهج الدراسي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'syllabus-finish-time-calculator';
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
        <label class="form-label" for="totalLessonsLeft">عدد الدروس أو الفصول المتبقية لإنهاء المنهج</label>
        <input type="number" id="totalLessonsLeft" class="form-control" value="24" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="lessonsPerDaySpeed">عدد الدروس التي تستطيع إنجازها يومياً</label>
        <input type="number" id="lessonsPerDaySpeed" class="form-control" value="2" min="0.5" max="10" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="revisionDaysBuffer">أيام إضافية مخصصة للمراجعة الشاملة وحل الامتحانات</label>
        <input type="number" id="revisionDaysBuffer" class="form-control" value="4" min="0" max="20" step="1"  oninput="calculateTool()">
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
  0 => 'مدة الإنهاء = (عدد الدروس المتبقية ÷ معدل الإنجاز اليومي) + أيام المراجعة الاحتياطية.',
  1 => 'تخصيص أيام منفصلة لحل نماذج السنوات السابقة يرفع درجة الطالب بما لا يقل عن 15% إلى 20%.',
),
        'يفترض التزاماً يومياً ثابتاً بالمعدل المكتوب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف ألتزم بإنهاء الدروس المقررة يومياً؟',
    'a' => 'حدد وقت بداية المذاكرة بدقة (مثلاً السادسة صباحاً أو الرابعة عصراً) واعتبره موعداً مقدساً غير قابل للتأجيل، وتخلص من الهاتف خارج غرفة المذاكرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'daily-study-hours-calculator',
  1 => 'curriculum-progress-calculator',
  2 => 'exam-countdown-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const lessons = Math.max(1, parseFloat(document.getElementById('totalLessonsLeft').value) || 24);
            const speed = Math.max(0.5, parseFloat(document.getElementById('lessonsPerDaySpeed').value) || 2);
            const revision = Math.max(0, parseInt(document.getElementById('revisionDaysBuffer').value) || 4);

            const studyDays = Math.ceil(lessons / speed);
            const totalDaysWithRevision = studyDays + revision;

            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + totalDaysWithRevision);
            const finishDateStr = targetDate.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

            setPrimaryResult(totalDaysWithRevision + ' يوماً (' + finishDateStr + ')', 'الموعد المتوقع لختم المنهج والمراجعة');
            showResultArea();

            setDetailStats([
                { label: 'أيام دراسة المنهج الجديد', value: studyDays + ' يوماً', color: '#3b82f6' },
                { label: 'أيام المراجعة وحل النماذج', value: revision + ' أيام', color: '#10b981' },
                { label: 'معدل الدروس المنجزة أسبوعياً', value: (speed * 7).toFixed(1) + ' درس', color: '#f59e0b' },
                { label: 'عدد الدروس المتبقية', value: lessons + ' درساً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بمعدل إنجاز <strong>${speed} دروس يومياً</strong>، ستختم المنهج خلال <strong>${studyDays} يوماً</strong>، ومع إضافة <strong>${revision} أيام للمراجعة</strong>، تكون جاهزاً تماماً بحلول <strong>${finishDateStr}</strong>.</p>
            `);
        
        saveLastInputs('syllabus-finish-time-calculator');
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
    restoreLastInputs('syllabus-finish-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>