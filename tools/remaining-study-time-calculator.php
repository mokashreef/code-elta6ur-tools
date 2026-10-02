<?php
/**
 * أداة: حاسبة وقت الدراسة المتبقي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'remaining-study-time-calculator';
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
        <label class="form-label" for="totalSubjectEstimatedHours">إجمالي الساعات المقدرة للمادة بالكامل</label>
        <input type="number" id="totalSubjectEstimatedHours" class="form-control" value="50" min="1"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hoursAlreadyStudied">عدد الساعات التي درستها بالفعل حتى الآن</label>
        <input type="number" id="hoursAlreadyStudied" class="form-control" value="18" min="0"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyPaceHours">معدل دراستك اليومي المعتاد (ساعات / يوم)</label>
        <input type="number" id="dailyPaceHours" class="form-control" value="4" min="0.5" max="16" step="0.5"  oninput="calculateTool()">
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
  0 => 'الساعات المتبقية = إجمالي الساعات المقدرة للمادة - الساعات المدروسة.',
  1 => 'الأيام المطلوبة = الساعات المتبقية ÷ معدل الساعات اليومي.',
),
        'يفترض التزاماً بالسرعة اليومية المدخلة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أقدر ساعات المادة إذا كنت لا أعرفها؟',
    'a' => 'القاعدة الجامعية المعتادة: كل ساعة معتمدة في الجامعة تتطلب ساعتين إلى 3 ساعات مذاكرة مستقلة أسبوعياً (مادة 3 ساعات تتطلب حوالي 40 إلى 50 ساعة مذاكرة خلال الفصل).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'curriculum-progress-calculator',
  1 => 'daily-study-hours-calculator',
  2 => 'syllabus-finish-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const total = Math.max(1, parseFloat(document.getElementById('totalSubjectEstimatedHours').value) || 50);
            const studied = Math.max(0, parseFloat(document.getElementById('hoursAlreadyStudied').value) || 18);
            const pace = Math.max(0.5, parseFloat(document.getElementById('dailyPaceHours').value) || 4);

            const remainingHours = Math.max(0, total - studied);
            const daysNeeded = Math.ceil(remainingHours / pace);
            const completionRate = Math.min(100, (studied / total) * 100);

            setPrimaryResult(remainingHours.toFixed(1) + ' ساعة متبقية (' + daysNeeded + ' أيام)', 'الوقت الصافي المتبقي لإنهاء المادة');
            showResultArea();

            setDetailStats([
                { label: 'الساعات المتبقية للمذاكرة', value: remainingHours.toFixed(1) + ' ساعة', color: '#ef4444' },
                { label: 'الأيام المطلوبة بالمعدل الحالي', value: daysNeeded + ' يوماً', color: '#f59e0b' },
                { label: 'نسبة الإنجاز الزمني للمقرر', value: completionRate.toFixed(1) + '%', color: '#10b981' },
                { label: 'الساعات المنجزة حتى الآن', value: studied + ' ساعة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>متبقي لك <strong>${remainingHours.toFixed(1)} ساعة مذاكرة</strong>. بمعدل <strong>${pace} ساعات يومياً</strong>، تحتاج إلى <strong>${daysNeeded} أيام</strong> للانتهاء من المادة تماماً.</p>
            `);
        
        saveLastInputs('remaining-study-time-calculator');
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
    restoreLastInputs('remaining-study-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>