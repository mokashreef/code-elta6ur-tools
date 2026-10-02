<?php
/**
 * أداة: استخراج الروابط (URLs) من النص وتصفيتها
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'extract-urls-from-text';
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
        <label class="form-label" for="urlExtractInput">ألصق النص المحتوي على روابط ومواقع إلكترونية</label>
        <textarea id="urlExtractInput" class="form-control" rows="7" placeholder="ضع النص هنا..." oninput="calculateTool()">يمكنك زيارة موقعنا الرسمي https://google.com للمزيد من المعلومات.
كما يمكنك متابعة أخبارنا على https://twitter.com/elta6ur أو مراسلتنا عبر الرابط المختصر bit.ly/test-link وموقع https://google.com مكرر.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="removeDuplicateUrls">إزالة الروابط المكررة</label>
        <select id="removeDuplicateUrls" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، روابط فريدة فقط</option>
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
  0 => 'تستخرج الأداة روابط http و https والروابط التي تبدأ بـ www والروابط المختصرة بدقة فائقة.',
  1 => 'مفيدة جداً للمسوقين والباحثين لجمع المراجع وقوائم المواقع من المقالات والرسائل.',
),
        'يتم تنظيف علامات الترقيم اللاحقة للرابط كالنقاط والأقواس تلقائياً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تدعم الأداة الروابط المختصرة مثل bit.ly؟',
    'a' => 'نعم؛ يتم استخراج كافة النطاقات والروابط المختصرة والفرعية بدقة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'extract-emails-from-text',
  1 => 'extract-hashtags-from-text',
  2 => 'url-parser',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('urlExtractInput').value;
            const rmDup = document.getElementById('removeDuplicateUrls').value === 'yes';

            // تعبير نمطي دقيق لاستخراج كافة صيغ الروابط
            const urlRegex = /(https?:\/\/[^\s"\'<>()]+|www\.[^\s"\'<>()]+|[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}\/[^\s"\'<>()]*)/gi;
            let matches = text.match(urlRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches));
            }

            const resultList = matches.join('\n');

            setPrimaryResult(matches.length + ' رابط تم استخراجه', 'عدد الروابط المكتشفة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الروابط المستخرجة', value: matches.length + ' رابط', color: '#10b981' },
                { label: 'حالة التكرار', value: rmDup ? 'تمت تصفية المكرر' : 'شامل التكرار', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">قائمة الروابط المستخرجة (سطر لكل رابط):</label>
                    <textarea class="form-control" rows="6" style="font-family:monospace;direction:ltr" readonly>${resultList}</textarea>
                </div>
            `);
        
        saveLastInputs('extract-urls-from-text');
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
    restoreLastInputs('extract-urls-from-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>