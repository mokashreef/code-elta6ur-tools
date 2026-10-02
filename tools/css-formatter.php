<?php
/**
 * أداة: تنسيق وتجميل كود CSS (CSS Beautifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'css-formatter';
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
        <label class="form-label" for="rawCssInput">أدخل كود الـ CSS المراد تنسيقه</label>
        <textarea id="rawCssInput" class="form-control" rows="8" placeholder="ضع كود CSS هنا..." oninput="calculateTool()">.btn{padding:10px 20px;border-radius:8px;background:#6c63ff;color:#fff;cursor:pointer}.btn:hover{background:#5a52d5}.card{padding:1.5rem;border:1px solid #333;margin-bottom:1rem}</textarea>
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
  0 => 'يفصل كل خاصية CSS في سطر مستقل مع إزاحة بمقدار مسافتين لتسهيل القراءة.',
  1 => 'يضيف فاصلاً بين القواعد التصميمية (Selectors) لضمان تنظيم الأنماط.',
),
        'يفترض كود CSS صالح النحو والأقواس.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أفك ضغط ملف css مضغوط (minified)؟',
    'a' => 'ألصق الكود المضغوط في هذه الأداة وستقوم فوراً بإعادة توزيع الخصائص والأقواس في أسطر منظمة سهلة التعديل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'html-formatter',
  1 => 'js-formatter',
  2 => 'css-gradient-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const css = document.getElementById('rawCssInput').value.trim();

            let formatted = css
                .replace(/\s*{\s*/g, ' {\n  ')
                .replace(/;\s*/g, ';\n  ')
                .replace(/\s*}\s*/g, '\n}\n\n')
                .replace(/  }/g, '}')
                .trim();

            setPrimaryResult('تم تنسيق وتجميل كود CSS', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر الناتجة', value: formatted.split(/\n/).length + ' سطر', color: '#10b981' },
                { label: 'طول الكود المنسق', value: formatted.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود CSS المنسق مع بادئة مسافات منتظمة:</label>
                    <textarea class="form-control" rows="10" style="font-family:monospace;direction:ltr" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('css-formatter');
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
    restoreLastInputs('css-formatter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>