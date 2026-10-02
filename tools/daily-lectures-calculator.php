<?php
/**
 * أداة: حاسبة عدد المحاضرات اليومية لمشاهدة الكورسات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'daily-lectures-calculator';
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
        <label class="form-label" for="totalLecturesCount">عدد المحاضرات أو الفيديوهات المتبقية في الدورة</label>
        <input type="number" id="totalLecturesCount" class="form-control" value="36" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="avgLectureMinutes">متوسط مدة المحاضرة الواحدة (بالدقائق)</label>
        <input type="number" id="avgLectureMinutes" class="form-control" value="45" min="5"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="daysToFinishCourse">عدد الأيام المحددة لإنهاء الكورس</label>
        <input type="number" id="daysToFinishCourse" class="form-control" value="12" min="1" max="180" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="playbackSpeed">سرعة تشغيل الفيديو المفضلة</label>
        <select id="playbackSpeed" class="form-control" onchange="calculateTool()">
            <option value="1.0" >السرعة العادية (1.0x)</option>
            <option value="1.25" selected>تسريع خفيف مريح (1.25x - توفير 20% وقت)</option>
            <option value="1.5" >تسريع متوسط (1.5x - توفير 33% وقت)</option>
            <option value="1.75" >تسريع فائق (1.75x)</option>
            <option value="2.0" >سرعة مضاعفة (2.0x - توفير 50% وقت)</option>
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
  0 => 'المدة الفعلية بعد التسريع = مدة الفيديو الأصلية ÷ سرعة التشغيل.',
  1 => 'الاستماع بسرعة 1.25x إلى 1.5x يحافظ على وضوح مخارج الحروف مع تدريب العقل على المعالجة الذهنية السريعة وتوفير ثلث الوقت.',
),
        'يفترض تدوين الملاحظات بالتوازي أثناء الاستماع.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل التسريع يقلل من الاستيعاب؟',
    'a' => 'تثبت الدراسات أن سرعة 1.25x لا تؤثر مطلقاً على نسبة الاستيعاب لمعظم الطلاب، بل قد تزيد من التركيز لأنها تمنع العقل من السرحان وتشتت الانتباه المصاحب للحديث البطيء.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'daily-pages-calculator',
  1 => 'daily-study-hours-calculator',
  2 => 'syllabus-finish-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const count = Math.max(1, parseInt(document.getElementById('totalLecturesCount').value) || 36);
            const mins = Math.max(5, parseFloat(document.getElementById('avgLectureMinutes').value) || 45);
            const days = Math.max(1, parseInt(document.getElementById('daysToFinishCourse').value) || 12);
            const speed = parseFloat(document.getElementById('playbackSpeed').value) || 1.25;

            const lecturesPerDay = count / days;
            const actualLectureDuration = mins / speed;
            const dailyWatchingMinutes = lecturesPerDay * actualLectureDuration;
            const dailyHours = dailyWatchingMinutes / 60;
            const totalHoursSaved = ((count * mins) - (count * actualLectureDuration)) / 60;

            setPrimaryResult(lecturesPerDay.toFixed(1) + ' محاضرة يومياً (' + dailyHours.toFixed(1) + ' ساعة/يوم)', 'المعدل اليومي المطلوب لمشاهدة المحاضرات');
            showResultArea();

            setDetailStats([
                { label: 'المحاضرات المطلوبة في اليوم', value: lecturesPerDay.toFixed(1) + ' محاضرة', color: '#3b82f6' },
                { label: 'الوقت اليومي بالسرعة المختارة', value: Math.round(dailyWatchingMinutes) + ' دقيقة / يوم', color: '#10b981' },
                { label: 'ساعات الوقت الموفرة بفضل التسريع', value: totalHoursSaved.toFixed(1) + ' ساعة وفر', color: '#f59e0b' },
                { label: 'مدة المحاضرة الفعلية بعد التسريع', value: actualLectureDuration.toFixed(0) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لإنهاء <strong>${count} محاضرة</strong> خلال <strong>${days} يوماً</strong> بسرعة <strong>${speed}x</strong>، تحتاج لمشاهدة <strong>${lecturesPerDay.toFixed(1)} محاضرة يومياً</strong>، وتستغرق منك <strong>${dailyHours.toFixed(1)} ساعة</strong> يومياً مع توفير <strong>${totalHoursSaved.toFixed(1)} ساعة</strong> من وقتك الكلي.</p>
            `);
        
        saveLastInputs('daily-lectures-calculator');
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
    restoreLastInputs('daily-lectures-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>