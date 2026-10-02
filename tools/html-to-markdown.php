<?php
/**
 * أداة: تحويل كود HTML إلى Markdown
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'html-to-markdown';
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
        <label class="form-label" for="htmlSourceText">أدخل كود الـ HTML المطلوب تحويله</label>
        <textarea id="htmlSourceText" class="form-control" rows="8" placeholder="ضع كود HTML هنا..." oninput="calculateTool()"><h1>عنوان الصفحة</h1>
<p>مرحباً بكم في <strong>منصتنا</strong>، لقراءة المزيد زوروا <a href="https://example.com">موقعنا</a>.</p>
<ul>
  <li>الميزة الأولى</li>
  <li>الميزة الثانية</li>
</ul></textarea>
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
  0 => 'يجرد أكواد HTML من وسوم التنسيق الزائدة ويحولها إلى صيغة ماركداون نقية.',
  1 => 'مفيد جداً لنقل المقالات والمحتوى من ووردبريس أو مواقع الأخبار إلى ملفات التوثيق GitHub README.',
),
        'يفترض كود HTML صالح البنية مع وسوم فتح وإغلاق قياسية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحافظ التحويل على الروابط والخط العريض؟',
    'a' => 'نعم؛ يتم الحفاظ على الروابط وعناوين المقالات والتعداد النقطي والخطوط العريضة والمائلة بدقة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'markdown-to-html',
  1 => 'markdown-arabic-editor',
  2 => 'clean-html-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const html = document.getElementById('htmlSourceText').value;
            let md = html
                .replace(/<h1[^>]*>(.*?)<\/h1>/gi, '# $1\n\n')
                .replace(/<h2[^>]*>(.*?)<\/h2>/gi, '## $1\n\n')
                .replace(/<h3[^>]*>(.*?)<\/h3>/gi, '### $1\n\n')
                .replace(/<strong[^>]*>(.*?)<\/strong>/gi, '**$1**')
                .replace(/<b[^>]*>(.*?)<\/b>/gi, '**$1**')
                .replace(/<em[^>]*>(.*?)<\/em>/gi, '*$1*')
                .replace(/<i[^>]*>(.*?)<\/i>/gi, '*$1*')
                .replace(/<code[^>]*>(.*?)<\/code>/gi, '`$1`')
                .replace(/<blockquote[^>]*>(.*?)<\/blockquote>/gi, '> $1\n\n')
                .replace(/<a[^>]*href=["\\']([^"\\']*)["\\'][^>]*>(.*?)<\/a>/gi, '[$2]($1)')
                .replace(/<li[^>]*>(.*?)<\/li>/gi, '- $1\n')
                .replace(/<ul[^>]*>|<\/ul>|<ol[^>]*>|<\/ol>/gi, '')
                .replace(/<p[^>]*>(.*?)<\/p>/gi, '$1\n\n')
                .replace(/<br[^>]*>/gi, '\n')
                .replace(/<[^>]+>/g, '') // إزالة أي وسوم متبقية
                .replace(/\n{3,}/g, '\n\n')
                .trim();

            setPrimaryResult('تم التحويل إلى Markdown (' + md.length + ' حرف)', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'أحرف كود HTML المصدر', value: html.length + ' حرف', color: '#3b82f6' },
                { label: 'أحرف Markdown الصافية', value: md.length + ' حرف', color: '#10b981' },
                { label: 'نسبة تقليص الحجم', value: html.length > 0 ? (((html.length - md.length)/html.length)*100).toFixed(0) + '% أنظف' : '0%', color: '#f59e0b' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود Markdown الناتج:</label>
                    <textarea class="form-control" rows="8" style="font-family:monospace;direction:rtl" readonly>${md}</textarea>
                </div>
            `);
        
        saveLastInputs('html-to-markdown');
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
    restoreLastInputs('html-to-markdown');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>