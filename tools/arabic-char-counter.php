<?php
/**
 * أداة: عداد الأحرف والمسافات العربي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'arabic-char-counter';
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
        <label class="form-label" for="charCounterInput">أدخل النص لحساب الأحرف والرموز بدقة</label>
        <textarea id="charCounterInput" class="form-control" rows="6" placeholder="اكتب النص هنا..." oninput="calculateTool()">اللُّغَةُ العَرَبِيَّةُ هِيَ إِحْدَى أَكْثَرِ اللُّغَاتِ انْتِشَاراً فِي العَالَمِ.</textarea>
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
  0 => 'يفصل العداد حركات التشكيل (الفتحة، الضمة، الكسرة، الشدة، التنوين) عن الحروف الأصلية.',
  1 => 'مفيد جداً لكتابة نصوص مقيدة بطول معين مثل إعلانات Google ومشاركات منصة X (تويتر سابقاً - 280 حرف).',
),
        'حركات التشكيل تحسب كرموز Unicode مستقلة في البرمجيات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم حرفاً يسمح به في تغريدة X (تويتر)؟',
    'a' => 'الحد الأقصى هو 280 حرفاً للحسابات العادية، وتعتبر الحروف العربية مساوية لحرف واحد لكل حرف ومسافة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'arabic-word-counter',
  1 => 'sentence-counter',
  2 => 'clean-arabic-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('charCounterInput').value;
            const totalChars = text.length;
            const noSpaces = text.replace(/\s/g, '').length;
            const spacesCount = (text.match(/\s/g) || []).length;
            const tashkeelCount = (text.match(/[\u064B-\u065F\u0670]/g) || []).length;
            const punctuationCount = (text.match(/[.,،؛:!؟?"'()\[\]\-]/g) || []).length;
            const digitsCount = (text.match(/[0-9\u0660-\u0669]/g) || []).length;

            setPrimaryResult(totalChars.toLocaleString() + ' حرف مع المسافات (' + noSpaces.toLocaleString() + ' بدونها)', 'إجمالي عدد الأحرف');
            showResultArea();

            setDetailStats([
                { label: 'الأحرف بدون مسافات', value: noSpaces.toLocaleString() + ' حرف', color: '#10b981' },
                { label: 'عدد المسافات الفارغة', value: spacesCount.toLocaleString() + ' مسافة', color: '#3b82f6' },
                { label: 'حركات التشكيل والتنوين', value: tashkeelCount.toLocaleString() + ' حركة', color: '#f59e0b' },
                { label: 'علامات الترقيم والأرقام', value: (punctuationCount + digitsCount) + ' رمز', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي الأحرف: <strong>${totalChars}</strong> (منها <strong>${tashkeelCount} حركة تشكيل</strong> و <strong>${spacesCount} مسافة</strong> و <strong>${punctuationCount} علامة ترقيم</strong>).</p>
            `);
        
        saveLastInputs('arabic-char-counter');
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
    restoreLastInputs('arabic-char-counter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>