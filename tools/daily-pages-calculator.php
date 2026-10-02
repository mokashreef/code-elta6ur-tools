<?php
/**
 * أداة: حاسبة عدد الصفحات اليومية للمذاكرة والقراءة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'daily-pages-calculator';
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
        <label class="form-label" for="totalPagesBook">إجمالي عدد صفحات الكتاب أو المذكرة</label>
        <input type="number" id="totalPagesBook" class="form-control" value="240" min="5"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="readingDaysAvailable">عدد الأيام المتاحة للقراءة أو المذاكرة</label>
        <input type="number" id="readingDaysAvailable" class="form-control" value="15" min="1" max="365" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="minutesPerPage">متوسط الوقت المستغرق لقراءة واستيعاب الصفحة (بالدقائق)</label>
        <input type="number" id="minutesPerPage" class="form-control" value="4" min="1" max="30" step="1"  oninput="calculateTool()">
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
  0 => 'الصفحات اليومية = إجمالي صفحات الكتاب ÷ عدد الأيام المتاحة.',
  1 => 'قراءة 15 إلى 20 صفحة يومياً بانتظام تمكنك من إنهاء كتابين كاملين شهرياً (أكثر من 24 كتاباً في السنة).',
),
        'يفترض استيعاب وفهم النصوص وليس مجرد التصفح السريع.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أزيد سرعتي في قراءة الصفحات دون الإخلال بالفهم؟',
    'a' => 'تجنب التراجع لقراءة الكلمات السابقة، واستخدم إصبعك أو قلماً كدليل بصري لتحريك عينيك بسلاسة، وتخلص من نطق الكلمات داخلياً في عقلك (Subvocalization).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'reading-time-calculator',
  1 => 'daily-lectures-calculator',
  2 => 'syllabus-finish-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const pages = Math.max(5, parseFloat(document.getElementById('totalPagesBook').value) || 240);
            const days = Math.max(1, parseInt(document.getElementById('readingDaysAvailable').value) || 15);
            const minPage = Math.max(1, parseFloat(document.getElementById('minutesPerPage').value) || 4);

            const pagesPerDay = Math.ceil(pages / days);
            const dailyMinutes = pagesPerDay * minPage;
            const dailyHours = (dailyMinutes / 60);

            setPrimaryResult(pagesPerDay + ' صفحة يومياً', 'الورد اليومي المطلوب من الصفحات');
            showResultArea();

            setDetailStats([
                { label: 'الوقت اليومي المطلوب للقراءة', value: Math.round(dailyMinutes) + ' دقيقة (' + dailyHours.toFixed(1) + ' ساعة)', color: '#3b82f6' },
                { label: 'إجمالي صفحات الكتاب', value: pages + ' صفحة', color: '#10b981' },
                { label: 'الصفحات المقروءة في الأسبوع', value: (pagesPerDay * 7) + ' صفحة', color: '#f59e0b' },
                { label: 'المدة الإجمالية لإنهاء الكتاب', value: days + ' يوماً', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لإنهاء كتاب من <strong>${pages} صفحة</strong> خلال <strong>${days} يوماً</strong>، تحتاج لقراءة <strong>${pagesPerDay} صفحة يومياً</strong>، وتستغرق منك حوالي <strong>${Math.round(dailyMinutes)} دقيقة يومياً</strong>.</p>
            `);
        
        saveLastInputs('daily-pages-calculator');
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
    restoreLastInputs('daily-pages-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>