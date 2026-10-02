<?php
/**
 * أداة: تنظيف النص المنسوخ من HTML واستخراج النص الصافي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'clean-html-text';
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
        <label class="form-label" for="htmlCleanInput">ألصق كود أو صفحة HTML المراد استخراج النص النظيف منها</label>
        <textarea id="htmlCleanInput" class="form-control" rows="8" placeholder="ضع كود HTML هنا..." oninput="calculateTool()"><div class="article-content">
  <h2>عنوان المقال الهام</h2>
  <p>هذا نص <strong>مهم جداً</strong> داخل المقال، ويمكنك الضغط على <a href="#">هذا الرابط</a> للمزيد.</p>
  <span style="color:red">ملاحظة ختامية مميزة</span>
</div></textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="keepLineBreaks">الحفاظ على فواصل الفقرات والأسطر</label>
        <select id="keepLineBreaks" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، أسطر جديدة لكل فقرة</option>
            <option value="no" >لا، دمج كامل في سطر واحد</option>
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
  0 => 'يحذف وسوم التنسيق والـ HTML وسكريبتات الجافاسكريبت وتنسيقات الـ CSS بالكامل.',
  1 => 'يستبدل رموز الـ HTML Entities (مثل &amp; و &nbsp;) بمقابلاتها النصية الحقيقية.',
),
        'يحافظ على المحتوى النصي المعروض للمستخدم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحذف محتوى سكربتات الإعلانات والـ CSS؟',
    'a' => 'نعم؛ يتم التخلص تماماً من نصوص وسكريبتات الـ style والـ script ولا تظهر في النص المستخرج.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'clean-word-text',
  1 => 'html-to-markdown',
  2 => 'clean-arabic-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const raw = document.getElementById('htmlCleanInput').value;
            const keepLines = document.getElementById('keepLineBreaks').value === 'yes';

            let cleaned = raw
                .replace(/<script[^>]*>[\s\S]*?<\/script>/gi, '') // إزالة الجافاسكريبت
                .replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '') // إزالة أكواد CSS
                .replace(/<br\s*\/?>/gi, '\n')
                .replace(/<\/p>|<\/div>|<\/h[1-6]>|<\/li>/gi, '\n\n')
                .replace(/<[^>]+>/g, '') // إزالة كافة وسوم HTML
                .replace(/&nbsp;/g, ' ')
                .replace(/&amp;/g, '&')
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&quot;/g, '"')
                .replace(/&#39;/g, "'");

            if (!keepLines) {
                cleaned = cleaned.replace(/\s+/g, ' ');
            } else {
                cleaned = cleaned.replace(/\n{3,}/g, '\n\n');
            }
            cleaned = cleaned.trim();

            setPrimaryResult('تم تجريد النص من وسوم HTML بنجاح', 'استخراج النص الصافي');
            showResultArea();

            setDetailStats([
                { label: 'طول كود HTML الأصلي', value: raw.length + ' حرف', color: '#ef4444' },
                { label: 'طول النص الصافي بعد التنظيف', value: cleaned.length + ' حرف', color: '#10b981' },
                { label: 'نسبة تنظيف الأكواد الزائدة', value: raw.length > 0 ? (((raw.length - cleaned.length)/raw.length)*100).toFixed(0) + '%' : '0%', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص الصافي المستخرج بدون وسوم HTML:</label>
                    <textarea class="form-control" rows="7" style="direction:rtl;line-height:1.8" readonly>${cleaned}</textarea>
                </div>
            `);
        
        saveLastInputs('clean-html-text');
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
    restoreLastInputs('clean-html-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>