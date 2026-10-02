<?php
/**
 * أداة: عداد الفقرات والأسطر في النص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'paragraph-counter';
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
        <label class="form-label" for="paragraphInputText">ألصق المقال أو النص هنا</label>
        <textarea id="paragraphInputText" class="form-control" rows="8" placeholder="اكتب النص هنا..." oninput="calculateTool()">الذكاء الاصطناعي يغير عالمنا بسرعة مذهلة، ويفتح آفاقاً لا حدود لها في الطب والتعليم والهندسة.

من المهم أن نستعد للمستقبل باكتساب مهارات رقمية متقدمة تواكب هذا التطور السريع.

الاستمرار في التعلم هو الضمانة الحقيقية للنجاح والريادة في العصر الحديث.</textarea>
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
  0 => 'الفقرة تُعرّف في تحرير النصوص بوجود سطر فارغ مزدوج يفصل بين الفكرة والأخرى.',
  1 => 'في المقالات الرقمية ومواقع الويب، يُفضل ألا تتجاوز الفقرة 3 إلى 5 أسطر لتحسين تجربة القراءة على شاشات الهواتف المحمولة.',
),
        'يفترض ضغط مفتاح Enter مرتين للفصل بين الفقرات المتعاقبة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُفضل قصر الفقرات في المحتوى الرقمي؟',
    'a' => 'لأن القارئ على الهاتف المحمول يميل إلى التصفح السريع والمسح البصري (Scanning)، والفقرات الطويلة تبدو ككتل نصية مربكة ومنفرة للعين.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sentence-counter',
  1 => 'split-text-paragraphs',
  2 => 'remove-empty-lines',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('paragraphInputText').value;
            const paragraphs = text.split(/\n\s*\n/).filter(p => p.trim().length > 0);
            const lines = text.split(/\n/).filter(l => l.trim().length > 0);
            const emptyLines = text.split(/\n/).filter(l => l.trim().length === 0).length;
            const words = text.trim() ? text.trim().split(/\s+/).length : 0;
            const avgWordsPerPara = paragraphs.length > 0 ? (words / paragraphs.length) : 0;

            setPrimaryResult(paragraphs.length + ' فقرة (' + lines.length + ' سطر مكتوب)', 'إجمالي عدد الفقرات');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات المستقلة', value: paragraphs.length + ' فقرة', color: '#10b981' },
                { label: 'عدد الأسطر المكتوبة', value: lines.length + ' أسطر', color: '#3b82f6' },
                { label: 'الأسطر الفارغة الفاصلة', value: emptyLines + ' سطر فارغ', color: '#f59e0b' },
                { label: 'متوسط كلمات الفقرة', value: Math.round(avgWordsPerPara) + ' كلمة / فقرة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>${paragraphs.length} فقرة</strong> موزعة على <strong>${lines.length} سطر</strong> بمتوسط <strong>${Math.round(avgWordsPerPara)} كلمة لكل فقرة</strong>.</p>
            `);
        
        saveLastInputs('paragraph-counter');
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
    restoreLastInputs('paragraph-counter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>