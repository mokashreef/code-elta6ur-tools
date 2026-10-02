<?php
/**
 * أداة: حساب متوسط طول الجملة وسهولة القراءة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'avg-sentence-length';
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
        <label class="form-label" for="avgSentenceTextInput">أدخل النص لقياس سهولة قراءته ومتوسط طول الجمل</label>
        <textarea id="avgSentenceTextInput" class="form-control" rows="7" placeholder="اكتب النص هنا..." oninput="calculateTool()">التخطيط المالي السليم هو سر راحة البال. عندما تضع ميزانية واضحة، تستطيع التحكم في مصاريفك وتفادي الديون. ابدأ اليوم بتسجيل نفقاتك اليومية الصغيرة.</textarea>
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
  0 => 'متوسط طول الجملة = إجمالي الكلمات ÷ إجمالي الجمل.',
  1 => 'المعيار الصحفي العالمي للكتابة الواضحة يوصي بمتوسط 14 إلى 17 كلمة للجملة الواحدة لضمان أقصى قدر من الاستيعاب.',
),
        'التقييم مبني على مؤشرات سهولة القراءة المعيارية للنصوص العربية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أختصر الجمل الطويلة؟',
    'a' => 'احذف حشو الكلمات الزائدة، واستبدل واوات العطف المتتالية بنقاط لتقسيم الفكرة الكبيرة إلى جملتين أو ثلاث جمل قصيرة مستقلة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sentence-counter',
  1 => 'reading-time-calculator',
  2 => 'speech-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('avgSentenceTextInput').value.trim();
            const words = text ? text.split(/\s+/).filter(w => w.length > 0) : [];
            const sentences = text ? text.split(/[.!?؟؛\n]+/).filter(s => s.trim().length > 3) : [];

            const totalWords = words.length;
            const totalSentences = Math.max(1, sentences.length);
            const avgLength = totalWords / totalSentences;

            let readability = 'سلس وسهل الفهم جداً للجمهور العام 🌟';
            let color = '#10b981';
            if (avgLength > 25) { readability = 'معقد وطويل جداً ويحتاج للتبسيط وتقصير الجمل ⚠️'; color = '#ef4444'; }
            else if (avgLength > 18) { readability = 'متوسط التعقيد (مناسب للمقالات الأكاديمية والرسمية)'; color = '#f59e0b'; }

            setPrimaryResult(avgLength.toFixed(1) + ' كلمة لكل جملة', 'متوسط طول الجملة');
            showResultArea();

            setDetailStats([
                { label: 'مستوى سهولة القراءة (Readability)', value: readability, color: color },
                { label: 'عدد الجمل الكلي', value: totalSentences + ' جملة', color: '#3b82f6' },
                { label: 'عدد الكلمات الكلي', value: totalWords + ' كلمة', color: '#10b981' },
                { label: 'المعدل المثالي الموصى به', value: '12 إلى 18 كلمة / جملة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متوسط طول الجمل في النص هو <strong>${avgLength.toFixed(1)} كلمة</strong>. تقييم السلاسة: <strong>${readability}</strong>.</p>
            `);
        
        saveLastInputs('avg-sentence-length');
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
    restoreLastInputs('avg-sentence-length');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>