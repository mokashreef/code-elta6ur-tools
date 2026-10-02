<?php
/**
 * أداة: مولد تعابير الجدولة (Cron Expression Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cron-expression-generator';
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
        <label class="form-label" for="cronSchedulePreset">جدول التكرار المفضل</label>
        <select id="cronSchedulePreset" class="form-control" onchange="calculateTool()">
            <option value="every_minute" >كل دقيقة (* * * * *)</option>
            <option value="every_5_minutes" >كل 5 دقائق (*/5 * * * *)</option>
            <option value="every_15_minutes" >كل 15 دقيقة (*/15 * * * *)</option>
            <option value="every_hour" >كل ساعة بالضبط عند الدقيقة 0 (0 * * * *)</option>
            <option value="daily_midnight" selected>يومياً عند منتصف الليل 12:00 ص (0 0 * * *)</option>
            <option value="daily_noon" >يومياً ظهراً الساعة 12:00 م (0 12 * * *)</option>
            <option value="weekly_friday" >أسبوعياً كل يوم جمعة منتصف الليل (0 0 * * 5)</option>
            <option value="monthly_first" >شهرياً في أول يوم من كل شهر (0 0 1 * *)</option>
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
  0 => 'يتكون تعبير Cron القياسي في لينكس و Crontab من 5 حقول: (الدقيقة 0-59، الساعة 0-23، يوم الشهر 1-31، الشهر 1-12، يوم الأسبوع 0-7).',
  1 => 'علامة النجمة (*) تعني كل قيمة ممكنة بدون استثناء.',
),
        'متوافق مع خوادم Linux ومعالجات وظائف السحابة AWS Cron و GitHub Actions.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا تعني علامة السلاش مثل */10؟',
    'a' => 'تعني التكرار الدوري بخطوة محددة؛ فمثلاً */10 في خانة الدقائق تعني كل 10 دقائق (في الدقائق 0، 10، 20، 30، 40، 50).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cron-expression-explainer',
  1 => 'timestamp-converter',
  2 => 'user-agent-parser',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const preset = document.getElementById('cronSchedulePreset').value;

            let cronExpr = '0 0 * * *';
            let desc = 'يومياً عند منتصف الليل';

            if (preset === 'every_minute') { cronExpr = '* * * * *'; desc = 'يتم التنفيذ كل دقيقة باستمرار'; }
            if (preset === 'every_5_minutes') { cronExpr = '*/5 * * * *'; desc = 'يتم التنفيذ كل 5 دقائق'; }
            if (preset === 'every_15_minutes') { cronExpr = '*/15 * * * *'; desc = 'يتم التنفيذ كل 15 دقيقة'; }
            if (preset === 'every_hour') { cronExpr = '0 * * * *'; desc = 'يتم التنفيذ رأس كل ساعة بالضبط'; }
            if (preset === 'daily_midnight') { cronExpr = '0 0 * * *'; desc = 'يومياً عند الساعة 12:00 ص (منتصف الليل)'; }
            if (preset === 'daily_noon') { cronExpr = '0 12 * * *'; desc = 'يومياً عند الساعة 12:00 ظهراً'; }
            if (preset === 'weekly_friday') { cronExpr = '0 0 * * 5'; desc = 'أسبوعياً كل يوم جمعة عند منتصف الليل'; }
            if (preset === 'monthly_first') { cronExpr = '0 0 1 * *'; desc = 'شهرياً في اليوم الأول من كل شهر'; }

            setPrimaryResult(cronExpr, 'تعبير Cron');
            showResultArea();

            setDetailStats([
                { label: 'شرح التعبير بالعربية', value: desc, color: '#10b981' },
                { label: 'الحقول الخمسة', value: 'دقيقة | ساعة | يوم شهر | شهر | يوم أسبوع', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">تعبير Cron الجاهز للاستخدام في السيرفر أو المهام المجدولة:</label>
                    <input type="text" class="form-control" style="font-family:monospace;direction:ltr;font-size:1.2rem;font-weight:bold;color:var(--text-accent-light)" value="${cronExpr}" readonly>
                    <p style="margin-top:0.75rem;color:var(--text-secondary)"><i class="fas fa-info-circle"></i> ${desc}.</p>
                </div>
            `);
        
        saveLastInputs('cron-expression-generator');
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
    restoreLastInputs('cron-expression-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>