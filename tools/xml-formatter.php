<?php
/**
 * أداة: تنسيق وتجميل كود XML (XML Beautifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'xml-formatter';
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
        <label class="form-label" for="rawXmlInput">أدخل كود XML غير المنسق</label>
        <textarea id="rawXmlInput" class="form-control" rows="8" placeholder="ضع كود XML هنا..." oninput="calculateTool()"><root><user id="1"><name>أحمد</name><role>مطور</role></user><user id="2"><name>سارة</name><role>مصممة</role></user></root></textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="indentSizeXml">مقدار المسافة البادئة (Indentation)</label>
        <select id="indentSizeXml" class="form-control" onchange="calculateTool()">
            <option value="2" selected>مسافتان (2 Spaces - موصى به)</option>
            <option value="4" >4 مسافات (4 Spaces)</option>
            <option value="tab" >علامة تبويب (Tab)</option>
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
  0 => 'يعيد ترتيب وسوم XML المتداخلة بشكل شجري أنيق يسهل قراءته وتعديله.',
  1 => 'يدعم ملفات خرائط المواقع sitemap.xml وملفات الـ RSS وتكوينات الأنظمة.',
),
        'يفترض وسوم XML متوافقة البنية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يفيد تنسيق XML في محركات البحث؟',
    'a' => 'التنسيق يسهل على المطور فحص ملفات sitemap.xml والتحقق من صحة الروابط وتواريخ التعديل قبل رفعها للسيرفر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'xml-validator',
  1 => 'html-formatter',
  2 => 'json-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const xml = document.getElementById('rawXmlInput').value.trim();
            const indentOpt = document.getElementById('indentSizeXml').value;
            const indentStr = indentOpt === 'tab' ? '\t' : ' '.repeat(parseInt(indentOpt) || 2);

            let formatted = '';
            let pad = 0;
            // تنظيف وتقطيع وسوم XML
            const reg = /(>)(<)(\/*)/g;
            const cleanXml = xml.replace(reg, '$1\r\n$2$3');
            const lines = cleanXml.split('\r\n');

            lines.forEach(node => {
                let indent = 0;
                if (node.match(/.+<\/\w[^>]*>$/)) {
                    indent = 0;
                } else if (node.match(/^<\/\w/)) {
                    if (pad !== 0) pad -= 1;
                } else if (node.match(/^<\w[^>]*[^\/]>.*$/)) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                formatted += indentStr.repeat(pad) + node + '\n';
                pad += indent;
            });
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق كود XML بنجاح (' + lines.length + ' سطر)', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المنسقة', value: lines.length + ' سطر', color: '#10b981' },
                { label: 'حجم الكود بالبايت', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود XML المنسق المنظم:</label>
                    <textarea class="form-control" rows="10" style="font-family:monospace;direction:ltr" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('xml-formatter');
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
    restoreLastInputs('xml-formatter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>