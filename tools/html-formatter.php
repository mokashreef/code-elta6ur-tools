<?php
/**
 * أداة: تنسيق وتجميل كود HTML (HTML Beautifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'html-formatter';
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
        <label class="form-label" for="rawHtmlInput">أدخل كود HTML غير المنسق</label>
        <textarea id="rawHtmlInput" class="form-control" rows="8" placeholder="ضع كود HTML هنا..." oninput="calculateTool()"><div class="container"><header><h1>عنوان الموقع</h1><nav><a href="#">الرئيسية</a><a href="#">من نحن</a></nav></header><main><p>محتوى تجريبي غير منسق.</p></main></div></textarea>
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
  0 => 'يرتب وسوم الـ HTML5 بهيكل هرمي واضح مع تمييز الوسوم ذاتية الإغلاق كـ img و input.',
  1 => 'يجعل الأكواد سهلة التتبع والصيانة واكتشاف أخطاء التنسيق.',
),
        'المسافة البادئة المعتمدة مسافتان (2 spaces).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يؤثر التنسيق على مظهر الصفحة في المتصفح؟',
    'a' => 'لا؛ لأن متصفحات الويب تتجاهل المسافات الفارغة المتعددة بين الوسوم، ويكون التأثير فقط على سهولة قراءة الكود للمطور.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'css-formatter',
  1 => 'js-formatter',
  2 => 'xml-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const html = document.getElementById('rawHtmlInput').value.trim();

            let formatted = '';
            let pad = 0;
            const cleanHtml = html.replace(/(>)(<)(\/*)/g, '$1\r\n$2$3');
            const lines = cleanHtml.split('\r\n');

            lines.forEach(node => {
                let indent = 0;
                if (node.match(/.+<\/\w[^>]*>$/)) {
                    indent = 0;
                } else if (node.match(/^<\/\w/)) {
                    if (pad !== 0) pad -= 1;
                } else if (node.match(/^<\w[^>]*[^\/]>.*$/) && !node.match(/<(input|img|br|hr|meta|link)[^>]*>/i)) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                formatted += '  '.repeat(pad) + node + '\n';
                pad += indent;
            });
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق كود HTML بنجاح', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر بعد التنسيق', value: lines.length + ' سطر', color: '#10b981' },
                { label: 'حجم الكود المنسق', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود HTML المنسق بهيكل هرمي واضح:</label>
                    <textarea class="form-control" rows="10" style="font-family:monospace;direction:ltr" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('html-formatter');
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
    restoreLastInputs('html-formatter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>