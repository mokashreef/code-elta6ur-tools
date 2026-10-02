<?php
/**
 * أداة: حاسبة الأيام المتبقية للامتحان (العد التنازلي)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'exam-countdown-calculator';
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
        <label class="form-label" for="examDateInput">تاريخ بدء الامتحانات</label>
        <input type="text" id="examDateInput" class="form-control" value="2026-06-15"    placeholder="YYYY-MM-DD" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="studyHoursPerDayAvail">ساعات المذاكرة المتاحة لك يومياً</label>
        <input type="number" id="studyHoursPerDayAvail" class="form-control" value="5" min="1" max="16" step="1"  oninput="calculateTool()">
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
  0 => 'العد التنازلي يساعد في كسر التسويف وتحويل الوقت المتبقي إلى ساعات عمل ملموسة وواضحة.',
  1 => 'حساب الساعات المتاحة يمنحك تصورا واقعيا لما يمكنك إنجازه ويقلل من التوتر النفسي.',
),
        'يفترض الحساب التاريخ الحالي لليوم مقارنة بتاريخ الاختبار.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أتعامل مع قلق وتوتر اقتراب موعد الامتحانات؟',
    'a' => 'التوتر ينتج من المجهول، وعندما تقسم المنهج إلى مهام يومية صغيرة ومكتوبة تشعر بالسيطرة على الموقف، بالإضافة للنوم الجيد لمدة 7 ساعات ليلاً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'daily-study-hours-calculator',
  1 => 'revision-plan-calculator',
  2 => 'syllabus-finish-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const dateStr = document.getElementById('examDateInput').value;
            const hoursDay = Math.max(1, parseFloat(document.getElementById('studyHoursPerDayAvail').value) || 5);

            const examDate = new Date(dateStr);
            const now = new Date();
            const diffTime = examDate - now;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (isNaN(diffDays) || diffDays < 0) {
                alert('يرجى إدخال تاريخ مستقبلي صحيح للامتحان بالصيغة YYYY-MM-DD');
                return;
            }

            const totalStudyHoursRemaining = diffDays * hoursDay;
            const weeksRemaining = (diffDays / 7).toFixed(1);

            setPrimaryResult(diffDays + ' يوماً متبقية (' + weeksRemaining + ' أسبوع)', 'العد التنازلي للامتحان');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ساعات المذاكرة المتاحة', value: totalStudyHoursRemaining + ' ساعة', color: '#10b981' },
                { label: 'عدد الأسابيع المتبقية', value: weeksRemaining + ' أسبوع', color: '#3b82f6' },
                { label: 'ساعات اليوم المعتمدة', value: hoursDay + ' ساعات / يوم', color: '#f59e0b' },
                { label: 'تاريخ الامتحان المحدد', value: examDate.toLocaleDateString('ar-EG'), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متبقي أمامك <strong>${diffDays} يوماً</strong> حتى الامتحان. إذا خصصت <strong>${hoursDay} ساعات يومياً</strong>، فسيكون لديك <strong>${totalStudyHoursRemaining} ساعة مذاكرة صافية</strong> كافية جداً لتحقيق التفوق بالانضباط والتركيز.</p>
            `);
        
        saveLastInputs('exam-countdown-calculator');
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
    restoreLastInputs('exam-countdown-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>