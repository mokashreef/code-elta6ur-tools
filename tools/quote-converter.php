<?php
/**
 * أداة: تحويل علامات الاقتباس والأقواس
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'quote-converter';
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
        <label class="form-label" for="quoteTextInput">أدخل النص المحتوي على اقتباسات</label>
        <textarea id="quoteTextInput" class="form-control" rows="6" placeholder="ضع النص هنا..." oninput="calculateTool()">قال الحكيم: "العلم في الصغر كالنقش على الحجر"، وأكد زميله: 'التكرار أم المهارات'.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="targetQuoteStyle">تحويل الاقتباسات إلى</label>
        <select id="targetQuoteStyle" class="form-control" onchange="calculateTool()">
            <option value="arabic_guillemets" selected>أقواس الاقتباس العربية التقليدية « »</option>
            <option value="curly_quotes" >علامات اقتباس منحنية فاخرة “ ”</option>
            <option value="straight_double" >اقتباس مزدوج مستقيم " "</option>
            <option value="single_quotes" >اقتباس مفرد ' '</option>
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
  0 => 'الأقواس المزدوجة الصغيرة « » (Guillemets) هي العلامة المعتمدة رسمياً في الطباعة والنشر العربي الكلاسيكي.',
  1 => 'توحيد علامات الاقتباس في الأبحاث والكتب يمنح العمل مظهراً أكاديمياً رصيناً واحترافياً.',
),
        'يفترض وجود أزواج اقتباس متطابقة (فتح وإغلاق).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أكتب القوسين « » على لوحة المفاتيح؟',
    'a' => 'على ويندوز باللغة العربية يمكنك كتابتها بالضغط على Shift + حرف الزاي وحرف الراء في بعض التوزيعات، أو استخدام هذه الأداة للتحويل التلقائي بنقرة زر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'clean-arabic-text',
  1 => 'clean-word-text',
  2 => 'arabic-english-numbers-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let text = document.getElementById('quoteTextInput').value;
            const style = document.getElementById('targetQuoteStyle').value;

            let openQ = '«';
            let closeQ = '»';
            if (style === 'curly_quotes') { openQ = '”'; closeQ = '“'; }
            if (style === 'straight_double') { openQ = '"'; closeQ = '"'; }
            if (style === 'single_quotes') { openQ = "'"; closeQ = "'"; }

            // استبدال الاقتباس المزدوج والمفرد والأقواس الفرنسية
            let converted = text.replace(/["“«](.*?)["”»]/g, openQ + '$1' + closeQ);
            converted = converted.replace(/['’](.*?)['’]/g, openQ + '$1' + closeQ);

            setPrimaryResult('تم تحويل علامات الاقتباس بنجاح', 'حالة الاقتباسات');
            showResultArea();

            setDetailStats([
                { label: 'النمط المعتمد الجديد', value: openQ + ' نص الاقتباس ' + closeQ, color: '#10b981' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص بعد ضبط علامات الاقتباس:</label>
                    <textarea class="form-control" rows="6" style="direction:rtl;line-height:1.8" readonly>${converted}</textarea>
                </div>
            `);
        
        saveLastInputs('quote-converter');
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
    restoreLastInputs('quote-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>