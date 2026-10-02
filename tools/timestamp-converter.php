<?php
/**
 * أداة: محول التاريخ و Unix Timestamp
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'timestamp-converter';
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
        <label class="form-label" for="timestampInputVal">أدخل Unix Timestamp (بالثواني أو الميلي ثانية) - أو اتركه فارغاً للوقت الحالي</label>
        <input type="text" id="timestampInputVal" class="form-control" value=""    placeholder="مثال: 1718452800" oninput="calculateTool()">
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
  0 => 'الـ Unix Timestamp هو عدد الثواني المنقضية منذ منتصف ليل 1 يناير 1970 (UTC).',
  1 => 'تستخدمه كافة أنظمة التشغيل وقواعد البيانات لمعاملة الوقت كأرقام صحيحة سهلة المقارنة والفرز.',
),
        'التحويل يعرض التاريخ بالتوقيت المحلي لمتصفح المستخدم وتوقيت UTC الدولي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أفرق بين Timestamp الثواني والميلي ثانية؟',
    'a' => 'الختم الزمني بالثواني يتكون حالياً من 10 أرقام (مثل 1718452800)، بينما بالميلي ثانية يتكون من 13 رقماً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'exam-countdown-calculator',
  1 => 'cron-expression-generator',
  2 => 'cron-expression-explainer',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let val = document.getElementById('timestampInputVal').value.trim();
            let date;

            if (!val) {
                date = new Date();
                val = Math.floor(date.getTime() / 1000).toString();
                document.getElementById('timestampInputVal').value = val;
            } else {
                let num = parseInt(val);
                if (val.length <= 10) num *= 1000; // بالثواني
                date = new Date(num);
            }

            if (isNaN(date.getTime())) {
                alert('Timestamp غير صالح!');
                return;
            }

            const unixSeconds = Math.floor(date.getTime() / 1000);
            const unixMillis = date.getTime();
            const isoStr = date.toISOString();
            const arabicFullDate = date.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });

            setPrimaryResult(arabicFullDate, 'التاريخ والوقت المحول');
            showResultArea();

            setDetailStats([
                { label: 'Unix Timestamp (ثواني)', value: unixSeconds, color: '#3b82f6' },
                { label: 'Unix Timestamp (ميلي ثانية)', value: unixMillis, color: '#10b981' },
                { label: 'صيغة ISO 8601 القياسية', value: isoStr, color: '#f59e0b' },
                { label: 'توقيت غرينتش (UTC)', value: date.toUTCString(), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>الختم الزمني <strong>${unixSeconds}</strong> يوافق بالتوقيت المحلي: <strong>${arabicFullDate}</strong>.</p>
            `);
        
        saveLastInputs('timestamp-converter');
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
    restoreLastInputs('timestamp-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>