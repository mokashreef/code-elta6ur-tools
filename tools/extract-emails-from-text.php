<?php
/**
 * أداة: استخراج البريد الإلكتروني من النص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'extract-emails-from-text';
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
        <label class="form-label" for="emailExtractInput">ألصق النص أو المستند المحتوي على إيميلات</label>
        <textarea id="emailExtractInput" class="form-control" rows="7" placeholder="ضع النص هنا..." oninput="calculateTool()">تواصل مع فريق الدعم عبر support@example.com أو المبيعات sales@elta6ur.com لمزيد من التفاصيل.
يمكنك أيضاً مراسلة info@example.com أو support@example.com.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="removeDupEmails">تصفية الإيميلات المكررة</label>
        <select id="removeDupEmails" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، إيميلات فريدة فقط</option>
            <option value="no" >لا</option>
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
  0 => 'تستخرج كافة عناوين البريد الإلكتروني الصالحة وتوفرها في قائمة مرتبة سطر لكل إيميل.',
  1 => 'تحول العناوين تلقائياً إلى حروف صغيرة (Lowercase) لتوحيد وتسهيل استيرادها في منصات التسويق البريدي.',
),
        'يفترض عناوين بريد إلكتروني قياسية تحتوي على علامة @ ونطاق صحيح.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن تصديرها كملف جاهز للاستيراد في برامج الـ CRM؟',
    'a' => 'نعم؛ انسخ القائمة مباشرة بضغطة زر والصقها في ملف Excel أو CSV للاستيراد الفوري في أدوات الـ Mailchimp وغيرها.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'extract-urls-from-text',
  1 => 'extract-hashtags-from-text',
  2 => 'remove-duplicate-lines',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('emailExtractInput').value;
            const rmDup = document.getElementById('removeDupEmails').value === 'yes';

            const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})/gi;
            let matches = text.match(emailRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches.map(e => e.toLowerCase())));
            }

            const resultText = matches.join('\n');

            setPrimaryResult(matches.length + ' بريد إلكتروني تم استخراجه', 'نتيجة الاستخراج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الإيميلات المكتشفة', value: matches.length + ' إيميل', color: '#10b981' },
                { label: 'تصفية الحروف الكبيرة والمكرر', value: rmDup ? 'مفعلة (Lowercase)' : 'معطلة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">قائمة عناوين البريد الإلكتروني المستخرجة:</label>
                    <textarea class="form-control" rows="6" style="font-family:monospace;direction:ltr" readonly>${resultText}</textarea>
                </div>
            `);
        
        saveLastInputs('extract-emails-from-text');
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
    restoreLastInputs('extract-emails-from-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>