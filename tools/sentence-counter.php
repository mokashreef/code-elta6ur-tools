<?php
/**
 * أداة: عداد الجمل في النص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sentence-counter';
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
        <label class="form-label" for="sentenceTextInput">ألصق النص هنا لعد جمله</label>
        <textarea id="sentenceTextInput" class="form-control" rows="6" placeholder="اكتب النص هنا..." oninput="calculateTool()">العلم نور، والجهل ظلام. هل فكرت يوماً في سر تقدم الأمم؟ إنها القراءة والمعرفة والعمل الجاد! لا تتوقف عن التعلم مهما تقدم بك العمر.</textarea>
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
  0 => 'يتم التعرف على نهاية الجمل بواسطة علامات الترقيم الختامية: النقطة (.)، علامة الاستفهام (؟ / ?)، علامة التعجب (!)، والفاصلة المنقوطة (؛).',
  1 => 'تجنب الجمل شديدة الطول (أكثر من 30 كلمة) يرفع من جودة الكتابة وسهولة فهمها للقارئ.',
),
        'الجمل التي تقل عن 3 أحرف تستبعد لتفادي النقاط الزائدة في الاختصارات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الفاصلة العادية (،) تنهي الجملة؟',
    'a' => 'الفاصلة العادية تفصل بين أجزاء الجملة الواحدة وليست نهاية تامة، وتعتبر النقطة وعلامات الاستفهام والتعجب هي المحددات الرسمية لختام الجملة التامة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'paragraph-counter',
  1 => 'avg-sentence-length',
  2 => 'arabic-word-counter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('sentenceTextInput').value.trim();
            // تقسيم الجمل بالنقطة وعلامة الاستفهام والتعجب والفاصلة المنقوطة
            const sentences = text ? text.split(/[.!?؟؛\n]+/).filter(s => s.trim().length > 3) : [];
            const words = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
            const avgWordsPerSentence = sentences.length > 0 ? (words / sentences.length) : 0;

            setPrimaryResult(sentences.length + ' جملة', 'إجمالي عدد الجمل في النص');
            showResultArea();

            setDetailStats([
                { label: 'عدد الجمل المكتشفة', value: sentences.length + ' جملة', color: '#10b981' },
                { label: 'إجمالي كلمات النص', value: words + ' كلمة', color: '#3b82f6' },
                { label: 'متوسط الكلمات في كل جملة', value: avgWordsPerSentence.toFixed(1) + ' كلمة / جملة', color: '#f59e0b' },
                { label: 'علامات الترقيم المستخدمة', value: (text.match(/[.!?؟؛]/g) || []).length + ' علامة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>${sentences.length} جملة</strong> بمتوسط <strong>${avgWordsPerSentence.toFixed(1)} كلمة لكل جملة</strong>. الجمل ذات الطول بين 12 إلى 18 كلمة هي الأكثر وضوحاً وسلاسة للقارئ.</p>
            `);
        
        saveLastInputs('sentence-counter');
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
    restoreLastInputs('sentence-counter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>