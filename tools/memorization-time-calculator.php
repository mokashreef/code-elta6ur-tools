<?php
/**
 * أداة: حاسبة وقت الحفظ المتوقع (القرآن والمحتوى الأكاديمي)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'memorization-time-calculator';
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
        <label class="form-label" for="pagesToMemorize">عدد الصفحات أو الأوجه المراد حفظها</label>
        <input type="number" id="pagesToMemorize" class="form-control" value="30" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="minutesPerPageMem">الوقت المستغرق لحفظ الصفحة الواحدة (بالدقائق) - المعتاد 30 إلى 60 دقيقة</label>
        <input type="number" id="minutesPerPageMem" class="form-control" value="40" min="5"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyMemHours">الوقت المخصص للحفظ يومياً (ساعات)</label>
        <input type="number" id="dailyMemHours" class="form-control" value="1.5" min="0.25" max="10" step="0.25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="reviewRatioMem">نسبة وقت المراجعة والتثبيت (%)- الموصى به 30%</label>
        <input type="number" id="reviewRatioMem" class="form-control" value="30" min="10" max="60" step="5"  oninput="calculateTool()">
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
  0 => 'الحفظ المتين يتطلب دائماً تخصيص ثلث الوقت على الأقل لمراجعة المحفوظ القديم (التكرار التراكمي).',
  1 => 'الحفظ بعد صلاة الفجر في الصباح الباكر هو الأكثر ثباتاً وسرعة بسبب صفاء الذهن وقلة المشتتات.',
),
        'يفترض التلاوة الصحيحة قبل البدء بالحفظ لتفادي حفظ الأخطاء.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي أفضل طريقة لتثبيت المحفوظ؟',
    'a' => 'التسميع على زميل أو شيخ، وتكرار الصفحة من الذاكرة غيباً 10 إلى 15 مرة على مدار اليوم، والصلاة بما تم حفظه في قيام الليل والصلوات اليومية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'revision-plan-calculator',
  1 => 'daily-pages-calculator',
  2 => 'daily-study-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const pages = Math.max(1, parseInt(document.getElementById('pagesToMemorize').value) || 30);
            const minPage = Math.max(5, parseFloat(document.getElementById('minutesPerPageMem').value) || 40);
            const dailyHours = Math.max(0.25, parseFloat(document.getElementById('dailyMemHours').value) || 1.5);
            const revRate = Math.max(10, parseFloat(document.getElementById('reviewRatioMem').value) || 30) / 100;

            const pureMemMinutes = pages * minPage;
            const totalMinutesWithReview = pureMemMinutes * (1 + revRate);
            const totalHours = totalMinutesWithReview / 60;
            const daysNeeded = Math.ceil(totalHours / dailyHours);

            const pagesPerDay = (pages / daysNeeded);

            setPrimaryResult(daysNeeded + ' يوماً (' + totalHours.toFixed(1) + ' ساعة عمل)', 'المدة المتوقعة لإتمام الحفظ والتثبيت');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الساعات شاملة التثبيت', value: totalHours.toFixed(1) + ' ساعة', color: '#3b82f6' },
                { label: 'معدل الحفظ اليومي المطلوب', value: pagesPerDay.toFixed(1) + ' صفحة / يوم', color: '#10b981' },
                { label: 'وقت الحفظ الجديد الصافي', value: (pureMemMinutes / 60).toFixed(1) + ' ساعة', color: '#f59e0b' },
                { label: 'وقت المراجعة والربط المخصص', value: ((pureMemMinutes * revRate) / 60).toFixed(1) + ' ساعة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لحفظ وتثبيت <strong>${pages} صفحة</strong> بمعدل <strong>${dailyHours} ساعة يومياً</strong>، تحتاج إلى <strong>${daysNeeded} يوماً</strong> (بمعدل حفظ <strong>${pagesPerDay.toFixed(1)} صفحة يومياً</strong>) مع تخصيص 30% من الوقت للربط والمراجعة التراكمية.</p>
            `);
        
        saveLastInputs('memorization-time-calculator');
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
    restoreLastInputs('memorization-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>