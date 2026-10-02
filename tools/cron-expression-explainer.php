<?php
/**
 * أداة: شرح وتفسير تعبير Cron باللغة العربية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cron-expression-explainer';
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
        <label class="form-label" for="cronToExplainInput">أدخل تعبير Cron المكون من 5 حقول</label>
        <input type="text" id="cronToExplainInput" class="form-control" value="30 4 * * 1-5"    placeholder="30 4 * * 1-5" oninput="calculateTool()">
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
  0 => 'يترجم رموز الجدولة المعقدة إلى جملة عربية واضحة ومباشرة لتفادي الأخطاء في إطلاق الوظائف المؤتمتة.',
  1 => 'يدعم النطاقات المفصولة بشرطة (مثل 1-5) والخطوات المتكررة (*/10).',
),
        'يفترض تعبير لينكس القياسي (5 أجزاء).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يبدأ ترقيم أيام الأسبوع بـ 0 أم بـ 1؟',
    'a' => 'في نظام Cron القياسي، الرقم 0 أو 7 يمثل يوم الأحد، والرقم 1 يمثل الاثنين، والرقم 5 يمثل الجمعة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cron-expression-generator',
  1 => 'timestamp-converter',
  2 => 'regex-tester',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const expr = document.getElementById('cronToExplainInput').value.trim();
            const parts = expr.split(/\s+/);

            if (parts.length !== 5) {
                alert('تعبير Cron غير مكتمل! يجب أن يحتوي على 5 حقول تفصلها مسافات (دقيقة ساعة يوم شهر يوم_أسبوع).');
                return;
            }

            const min = parts[0];
            const hour = parts[1];
            const dom = parts[2];
            const mon = parts[3];
            const dow = parts[4];

            let explanation = 'يتم تنفيذ المهمة: ';

            // الدقائق
            if (min === '*') explanation += 'في كل دقيقة ';
            else if (min.startsWith('*/')) explanation += 'كل ' + min.replace('*/', '') + ' دقائق ';
            else explanation += 'عند الدقيقة ' + min + ' ';

            // الساعات
            if (hour === '*') explanation += 'من كل ساعة ';
            else if (hour.startsWith('*/')) explanation += 'كل ' + hour.replace('*/', '') + ' ساعات ';
            else explanation += 'في الساعة ' + (parseInt(hour) > 12 ? (parseInt(hour)-12) + ' مساءً' : (parseInt(hour)===0 ? '12 منتصف الليل' : hour + ' صباحاً')) + ' ';

            // أيام الشهر
            if (dom !== '*') explanation += 'في اليوم رقم ' + dom + ' من الشهر ';

            // الأشهر
            if (mon !== '*') explanation += 'في شهر ' + mon + ' ';

            // أيام الأسبوع
            if (dow === '1-5') explanation += 'من يوم الاثنين إلى الجمعة (أيام العمل) ';
            else if (dow === '5') explanation += 'في يوم الجمعة ';
            else if (dow !== '*') explanation += 'في اليوم رقم ' + dow + ' من الأسبوع ';

            setPrimaryResult(explanation, 'التفسير باللغة العربية');
            showResultArea();

            setDetailStats([
                { label: 'الدقيقة (Minute)', value: min, color: '#3b82f6' },
                { label: 'الساعة (Hour)', value: hour, color: '#10b981' },
                { label: 'يوم الشهر (Day of Month)', value: dom, color: '#f59e0b' },
                { label: 'يوم الأسبوع (Day of Week)', value: dow, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div class="alert alert-info" style="margin-top:1rem">
                    <i class="fas fa-clock"></i> <strong>المعنى التنفيذي:</strong> ${explanation}.
                </div>
            `);
        
        saveLastInputs('cron-expression-explainer');
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
    restoreLastInputs('cron-expression-explainer');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>