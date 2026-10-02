<?php
/**
 * أداة: حاسبة ساعات الدراسة اليومية وجدول الامتحانات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'daily-study-hours-calculator';
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
        <label class="form-label" for="coursesCount">عدد المواد أو المقررات المسجلة</label>
        <input type="number" id="coursesCount" class="form-control" value="5" min="1" max="12" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="daysUntilExams">الأيام المتبقية حتى بدء الامتحانات</label>
        <input type="number" id="daysUntilExams" class="form-control" value="21" min="1" max="120" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="materialDifficulty">متوسط صعوبة المقررات</label>
        <select id="materialDifficulty" class="form-control" onchange="calculateTool()">
            <option value="light" >مواد نظرية خفيفة وقصيرة (حوالي 25 ساعة لكل مادة)</option>
            <option value="medium" selected>مواد متوسطة ومتوازنة (حوالي 40 ساعة لكل مادة)</option>
            <option value="heavy" >مواد علمية/طبية/هندسية دسمة (حوالي 65 ساعة لكل مادة)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="breakDaysCount">أيام راحة تخصصها للطوارئ والترفيه</label>
        <input type="number" id="breakDaysCount" class="form-control" value="2" min="0" max="15" step="1"  oninput="calculateTool()">
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
  0 => 'ساعات الدراسة اليومية = إجمالي الساعات التقديرية للمواد ÷ أيام الدراسة الفعلية.',
  1 => 'تطبيق تقنية بومودورو (25 دقيقة تركيز + 5 دقائق راحة) يزيد من قدرة الاستيعاب ويمنع الإجهاد الذهني.',
),
        'يفترض التركيز والابتعاد عن المشتتات والهواتف أثناء ساعات المذاكرة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الأفضل دراسة مادة واحدة في اليوم أم عدة مواد؟',
    'a' => 'الدراسات المعرفية تفضل التبديل بين مادتين مختلفتين يومياً (Interleaving Practice) مثل مادة حسابية ومادة نظرية لتنشيط فصوص الدماغ المختلفة ومنع الملل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'syllabus-finish-time-calculator',
  1 => 'exam-countdown-calculator',
  2 => 'revision-plan-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const courses = Math.max(1, parseInt(document.getElementById('coursesCount').value) || 5);
            const totalDays = Math.max(1, parseInt(document.getElementById('daysUntilExams').value) || 21);
            const diff = document.getElementById('materialDifficulty').value;
            const breakDays = Math.max(0, parseInt(document.getElementById('breakDaysCount').value) || 2);

            let hoursPerCourse = 40;
            if (diff === 'light') hoursPerCourse = 25;
            if (diff === 'heavy') hoursPerCourse = 65;

            const effectiveDays = Math.max(1, totalDays - breakDays);
            const totalRequiredHours = courses * hoursPerCourse;
            const dailyHours = totalRequiredHours / effectiveDays;

            let status = 'جدول متوازن ومريح جداً ✅';
            let color = '#10b981';
            if (dailyHours > 8) { status = 'مكثف وشاق جداً! ابدأ فوراً وقلل أيام الراحة ⚠️'; color = '#ef4444'; }
            else if (dailyHours > 5) { status = 'متوسط ويحتاج التزاماً وانضباطاً يومياً 🎯'; color = '#f59e0b'; }

            setPrimaryResult(dailyHours.toFixed(1) + ' ساعة دراسة يومياً', 'الخطة اليومية الموصى بها');
            showResultArea();

            setDetailStats([
                { label: 'ساعات الدراسة الصافية يومياً', value: dailyHours.toFixed(1) + ' ساعة / يوم', color: '#3b82f6' },
                { label: 'إجمالي الساعات المطلوبة للمنهج كاملاً', value: totalRequiredHours + ' ساعة', color: '#10b981' },
                { label: 'أيام الدراسة الفعلية', value: effectiveDays + ' يوماً', color: '#f59e0b' },
                { label: 'تقييم ضغط الجدول', value: status, color: color }
            ]);

            setResultContent(`
                <p>لدراسة <strong>${courses} مواد</strong> خلال <strong>${effectiveDays} يوماً دراسياً</strong>، تحتاج إلى <strong>${dailyHours.toFixed(1)} ساعة مذاكرة يومياً</strong> بمعدل جلسات بومودورو مقسمة بانتظام.</p>
            `);
        
        saveLastInputs('daily-study-hours-calculator');
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
    restoreLastInputs('daily-study-hours-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>