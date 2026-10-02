<?php
/**
 * أداة: تنسيق وتجميل كود JavaScript (JS Beautifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'js-formatter';
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
        <label class="form-label" for="rawJsInput">أدخل كود الـ JavaScript لتنسيقه</label>
        <textarea id="rawJsInput" class="form-control" rows="8" placeholder="ضع كود JS هنا..." oninput="calculateTool()">function calculateSum(a,b){if(a>0&&b>0){return a+b;}else{console.warn('قيم سالبة');return 0;}}const result=calculateSum(10,20);console.log(result);</textarea>
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
  0 => 'يرتب كود الجافاسكريبت ويفصل الجمل البرمجية المنتهية بفاصلة منقوطة.',
  1 => 'يجعل الأكواد غير المنسقة والمضغوطة قابلة للقراءة والـ Debugging.',
),
        'يفترض كود JavaScript صالح البنية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يؤثر التنسيق على عمل الكود؟',
    'a' => 'لا مطلقاً؛ التنسيق يغير فقط المسافات البيضاء والأسطر ولا يغير أي منطق تنفيذي في الكود.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'json-formatter',
  1 => 'html-formatter',
  2 => 'css-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const js = document.getElementById('rawJsInput').value.trim();

            let formatted = js
                .replace(/\s*{\s*/g, ' {\n  ')
                .replace(/;\s*/g, ';\n  ')
                .replace(/\s*}\s*/g, '\n}\n')
                .replace(/\s*else\s*/g, ' else ')
                .replace(/  }/g, '}')
                .trim();

            setPrimaryResult('تم تنسيق كود JavaScript بنجاح', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر الناتجة', value: formatted.split(/\n/).length + ' سطر', color: '#10b981' },
                { label: 'طول الكود بالبايت', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود JavaScript المنسق:</label>
                    <textarea class="form-control" rows="10" style="font-family:monospace;direction:ltr" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('js-formatter');
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
    restoreLastInputs('js-formatter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>