<?php
/**
 * أداة: حاسبة الساعات المطلوبة للتخرج والخطة الدراسية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'graduation-hours-calculator';
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
        <label class="form-label" for="totalDegreeHours">إجمالي الساعات المعتمدة المطلوبة للتخرج بالخطة</label>
        <input type="number" id="totalDegreeHours" class="form-control" value="132" min="90" max="220" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="passedHoursSoFar">الساعات المجتازة بنجاح حتى الآن</label>
        <input type="number" id="passedHoursSoFar" class="form-control" value="78" min="0"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentEnrolledHours">الساعات المسجلة في الفصل الحالي (قيد الدراسة)</label>
        <input type="number" id="currentEnrolledHours" class="form-control" value="15" min="0" max="25" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="avgHoursPerSemester">متوسط الساعات التي تخطط لتسجيلها في كل فصل قادم</label>
        <input type="number" id="avgHoursPerSemester" class="form-control" value="15" min="9" max="22" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="summerSemestersPlanned">هل تخطط لدراسة فصول صيفية؟</label>
        <select id="summerSemestersPlanned" class="form-control" onchange="calculateTool()">
            <option value="none" selected>لا، فصول اعتيادية فقط (خريف وربيع)</option>
            <option value="one_summer" >نعم، فصل صيفي واحد (حوالي 6 إلى 9 ساعات)</option>
            <option value="two_summers" >نعم، فصلين صيفيين</option>
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
  0 => 'الساعات المتبقية = ساعات الخطة الكلية - (الساعات المجتازة + الساعات المسجلة حالياً).',
  1 => 'استغلال الفصول الصيفية لتسجيل 6 إلى 9 ساعات يختصر فصلاً دراسياً كاملاً ويسرع التخرج بنصف سنة.',
),
        'يفترض النجاح في جميع المقررات المسجلة دون رسوب أو تأجيل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم أقصى عدد ساعات يسمح للطالب بتسجيله في فصل التخرج؟',
    'a' => 'تسمح معظم اللوائح الجامعية للطالب الخريج بزيادة العبء الدراسي (Overload) ليصل إلى 21 أو 24 ساعة معتمدة في فصل تخرجه الأخير لتفادي تأخير التخرج فصلاً إضافياً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cumulative-gpa-calculator',
  1 => 'target-gpa-calculator',
  2 => 'daily-study-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const total = Math.max(90, parseInt(document.getElementById('totalDegreeHours').value) || 132);
            const passed = Math.max(0, parseInt(document.getElementById('passedHoursSoFar').value) || 78);
            const enrolled = Math.max(0, parseInt(document.getElementById('currentEnrolledHours').value) || 15);
            const pace = Math.max(9, parseInt(document.getElementById('avgHoursPerSemester').value) || 15);
            const summer = document.getElementById('summerSemestersPlanned').value;

            const remainingAfterCurrent = Math.max(0, total - (passed + enrolled));
            let summerHoursDeduct = 0;
            if (summer === 'one_summer') summerHoursDeduct = 8;
            if (summer === 'two_summers') summerHoursDeduct = 16;

            const hoursForRegularSemesters = Math.max(0, remainingAfterCurrent - summerHoursDeduct);
            const semestersLeft = Math.ceil(hoursForRegularSemesters / pace);
            const yearsLeft = (semestersLeft / 2).toFixed(1);
            const completionPercent = ((passed + enrolled) / total) * 100;

            setPrimaryResult(semestersLeft + ' فصول دراسية متبقية (' + remainingAfterCurrent + ' ساعة)', 'المدة المتبقية للتخرج');
            showResultArea();

            setDetailStats([
                { label: 'الساعات المتبقية بعد الفصل الحالي', value: remainingAfterCurrent + ' ساعة معتمدة', color: '#ef4444' },
                { label: 'نسبة إنجاز الخطة الدراسية', value: completionPercent.toFixed(1) + '%', color: '#10b981' },
                { label: 'السنوات الدراسية المتبقية تقريباً', value: yearsLeft + ' سنة', color: '#3b82f6' },
                { label: 'إجمالي الساعات المنجزة والمسجلة', value: (passed + enrolled) + ' من ' + total, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متبقي لك <strong>${remainingAfterCurrent} ساعة معتمدة</strong> بعد اجتياز الفصل الحالي. بمعدل <strong>${pace} ساعة لكل فصل</strong>، ستتخرج بإذن الله خلال <strong>${semestersLeft} فصول دراسية</strong> (حوالي <strong>${yearsLeft} سنة</strong>).</p>
            `);
        
        saveLastInputs('graduation-hours-calculator');
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
    restoreLastInputs('graduation-hours-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>