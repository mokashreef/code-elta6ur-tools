<?php
/**
 * أداة: تحويل النص العربي إلى قائمة منسقة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'text-to-list-converter';
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
        <label class="form-label" for="rawListInput">أدخل العناصر (مفصولة بأسطر أو فواصل)</label>
        <textarea id="rawListInput" class="form-control" rows="6" placeholder="اكتب العناصر هنا..." oninput="calculateTool()">إتقان البرمجة
تصميم الواجهات
التسويق الرقمي
إدارة المشاريع</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="listFormatStyle">نوع وشكل القائمة المطلوبة</label>
        <select id="listFormatStyle" class="form-control" onchange="calculateTool()">
            <option value="bullets" selected>قائمة نقطية (• عنصر)</option>
            <option value="dashed" >قائمة شُرطية (- عنصر)</option>
            <option value="numbered" >قائمة مرقمة (1. عنصر)</option>
            <option value="letters" >قائمة هجائية (أ. ب. ج.)</option>
            <option value="html_ul" >كود HTML نقطي (<ul><li>)</option>
            <option value="html_ol" >كود HTML رقمي (<ol><li>)</option>
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
  0 => 'تحول النصوص المشتتة أو المدخلات المفصولة بفواصل إلى قوائم نقطية أو مرقمة أنيقة جاهزة للنشر.',
  1 => 'تدعم الترقيم الأبجدي العربي (أ، ب، ج، د) وكود HTML للقوائم.',
),
        'تتعرف الأداة على الفواصل العربية (،) والإنجليزية (,) كفواصل للعناصر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن تصديرها ككود HTML جاهز؟',
    'a' => 'نعم؛ باختيار خيار HTML UL أو OL يتم توليد وسم القائمة مع وسوم <li> لكل عنصر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'merge-lines-tool',
  1 => 'sort-text-lines',
  2 => 'markdown-to-html',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('rawListInput').value;
            const style = document.getElementById('listFormatStyle').value;

            // فصل العناصر سواء كانت أسطراً أو مفصولة بفواصل
            let items = text.split(/\n|,|،/).map(i => i.trim()).filter(i => i.length > 0);
            let formatted = '';

            const abjad = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح', 'ط', 'ي', 'ك', 'ل', 'م', 'ن', 'س', 'ع', 'ف', 'ص', 'ق', 'ر', 'ش', 'ت', 'ث', 'خ', 'ذ', 'ض', 'ظ', 'غ'];

            if (style === 'bullets') {
                formatted = items.map(i => '• ' + i).join('\n');
            } else if (style === 'dashed') {
                formatted = items.map(i => '- ' + i).join('\n');
            } else if (style === 'numbered') {
                formatted = items.map((i, idx) => (idx + 1) + '. ' + i).join('\n');
            } else if (style === 'letters') {
                formatted = items.map((i, idx) => (abjad[idx % abjad.length] || (idx+1)) + '. ' + i).join('\n');
            } else if (style === 'html_ul') {
                formatted = '<ul>\n' + items.map(i => '  <li>' + i + '</li>').join('\n') + '\n</ul>';
            } else if (style === 'html_ol') {
                formatted = '<ol>\n' + items.map(i => '  <li>' + i + '</li>').join('\n') + '\n</ol>';
            }

            setPrimaryResult('تم إنشاء قائمة من ' + items.length + ' عناصر', 'قائمة منسقة');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر بالقائمة', value: items.length + ' عناصر', color: '#10b981' },
                { label: 'النوع المعتمد', value: style, color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">القائمة المنسقة الناتجة:</label>
                    <textarea class="form-control" rows="7" style="direction:${style.includes('html') ? 'ltr' : 'rtl'};line-height:1.8" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('text-to-list-converter');
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
    restoreLastInputs('text-to-list-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>