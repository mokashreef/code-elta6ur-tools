<?php
/**
 * أداة: عداد الكلمات العربي المتخصص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'arabic-word-counter';
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
        <label class="form-label" for="arabicWordInput">ألصق أو اكتب النص العربي هنا</label>
        <textarea id="arabicWordInput" class="form-control" rows="7" placeholder="اكتب النص العربي هنا لحساب كلماته بدقة..." oninput="calculateTool()">بسم الله الرحمن الرحيم. القراءة تفتح آفاق العقل، وتمنح الإنسان قدرة استثنائية على فهم العالم من حوله والتعبير عن أفكاره بوضوح وإبداع.</textarea>
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
  0 => 'يقوم المحلل بتنظيف علامات التشكيل والتنوين تلقائياً لضمان عد دقيق وحقيقي للكلمات العربية.',
  1 => 'الكلمات المفصولة بفواصل أو نقاط أو أسطر جديدة تُحتسب ككلمات مستقلة.',
),
        'معدل سرعة القراءة العربي المعتمد هو 200 كلمة في الدقيقة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تحسب واو العطف ككلمة مستقلة؟',
    'a' => 'في القواعد الإملائية العربية المتصلة، الكلمة التي تبدأ بواو العطف مثل (والتعبير) تُعد ككلمة واحدة في معظم خوارزميات النصوص لأنها متصلة بدون مسافة فاصلة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'arabic-char-counter',
  1 => 'sentence-counter',
  2 => 'reading-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('arabicWordInput').value;
            // تنظيف النص وتجاهل التشكيل عند عد الكلمات
            const cleanText = text.replace(/[\u064B-\u065F\u0670]/g, '').trim();
            const words = cleanText ? cleanText.split(/\s+/).filter(w => w.length > 0) : [];
            const charsWithSpaces = text.length;
            const charsNoSpaces = text.replace(/\s/g, '').length;
            const lettersOnly = text.replace(/[^\u0600-\u06FFa-zA-Z]/g, '').length;
            const readingMins = (words.length / 200).toFixed(1);

            setPrimaryResult(words.length.toLocaleString() + ' كلمة عربية', 'إجمالي عدد الكلمات');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأحرف بدون مسافات', value: charsNoSpaces.toLocaleString() + ' حرف', color: '#10b981' },
                { label: 'عدد الأحرف مع المسافات', value: charsWithSpaces.toLocaleString() + ' حرف', color: '#3b82f6' },
                { label: 'عدد الحروف الهجائية الصافية', value: lettersOnly.toLocaleString() + ' حرف', color: '#f59e0b' },
                { label: 'وقت القراءة المقدر', value: readingMins + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>${words.length} كلمة</strong> و <strong>${charsNoSpaces} حرفاً</strong> بدون مسافات، ويستغرق قراءته بتركيز حوالي <strong>${readingMins} دقيقة</strong>.</p>
            `);
        
        saveLastInputs('arabic-word-counter');
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
    restoreLastInputs('arabic-word-counter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>