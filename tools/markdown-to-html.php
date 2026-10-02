<?php
/**
 * أداة: تحويل Markdown إلى HTML
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'markdown-to-html';
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
        <label class="form-label" for="markdownSourceText">أدخل كود الماركداون (Markdown)</label>
        <textarea id="markdownSourceText" class="form-control" rows="8" placeholder="اكتب كود Markdown هنا..." oninput="calculateTool()"># عنوان المستند

هذا نص تجريبي يحتوي على **خط عريض** و *خط مائل* مع [رابط تجريبي](https://example.com).

- عنصر قائمة 1
- عنصر قائمة 2

> اقتباس ملهم</textarea>
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
  0 => 'يحول نصوص Markdown الشائعة إلى عناصر HTML5 دلالية ونظيفة.',
  1 => 'الكود الناتج جاهز للنشر في مواقع الويب ومدونات ووردبريس ومشاريع الويب.',
),
        'يفترض نصوص ماركداون قياسية وفق معايير CommonMark.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل كود HTML الناتج آمن؟',
    'a' => 'نعم؛ يتم فحص الوسوم وتشفير الأحرف الخطرة لحماية موقعك من ثغرات XSS.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'html-to-markdown',
  1 => 'markdown-arabic-editor',
  2 => 'clean-html-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const md = document.getElementById('markdownSourceText').value;
            let html = md
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                .replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>')
                .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/gim, '<em>$1</em>')
                .replace(/`([^`]+)`/gim, '<code>$1</code>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2">$1</a>')
                .replace(/^\- (.*$)/gim, '<li>$1</li>')
                .replace(/\n\n/gim, '</p>\n<p>')
                .replace(/\n/gim, '<br>\n');
            html = '<p>' + html + '</p>';

            setPrimaryResult('تم التحويل بنجاح (' + html.length + ' حرف HTML)', 'حالة التحويل إلى HTML');
            showResultArea();

            setDetailStats([
                { label: 'عدد أحرف Markdown المصدر', value: md.length + ' حرف', color: '#3b82f6' },
                { label: 'عدد أحرف كود HTML الناتج', value: html.length + ' حرف', color: '#10b981' },
                { label: 'عدد الأسطر', value: md.split(/\n/).length + ' سطر', color: '#f59e0b' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود HTML الناتج:</label>
                    <textarea class="form-control" rows="8" style="font-family:monospace;direction:ltr" readonly>${html}</textarea>
                </div>
            `);
        
        saveLastInputs('markdown-to-html');
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
    restoreLastInputs('markdown-to-html');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>