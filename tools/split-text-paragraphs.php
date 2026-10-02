<?php
/**
 * أداة: تقسيم النص الطويل إلى فقرات متناسقة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'split-text-paragraphs';
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
        <label class="form-label" for="longTextInputSplit">ألصق النص الطويل المراد تقسيمه</label>
        <textarea id="longTextInputSplit" class="form-control" rows="8" placeholder="ضع النص هنا..." oninput="calculateTool()">النجاح ليس وليد الصدفة بل هو نتاج عمل دؤوب وتخطيط محكم. كل خطوة تخطوها في سبيل تحقيق أهدافك تقربك من القمة. لا تيأس عند مواجهة العقبات فالفشل مجرد تجربة تثقل مهاراتك. اجعل شغفك هو الوقود الذي يدفعك للأمام واحرص دائماً على مساعدة الآخرين في طريقك نحو النجاح. إن التعاون والعمل الجماعي هما أساس كل إنجاز عظيم في هذا العصر الحديث.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="splitCondition">طريقة التقسيم</label>
        <select id="splitCondition" class="form-control" onchange="calculateTool()">
            <option value="sentences_2" selected>فقرة كل جملتين (2 جمل)</option>
            <option value="sentences_3" >فقرة كل 3 جمل (المعيار الموصى به)</option>
            <option value="words_50" >فقرة كل 40 إلى 50 كلمة تقريباً</option>
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
  0 => 'يقسم الكتل النصية الصعبة إلى فقرات صغيرة مريحة للعين لتحسين تجربة القراءة والـ UX على الهواتف.',
  1 => 'يعتمد على علامات الترقيم لضمان اكتمال المعنى قبل الانتقال لسطر جديد.',
),
        'يفترض وجود علامات ترقيم لتقسيم الجمل بدقة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الحجم المثالي للفقرة في مقالات الويب؟',
    'a' => 'الفقرة المثالية في المقالات الرقمية تتراوح بين جملتين إلى 4 جمل (حوالي 40 إلى 70 كلمة) لتسهيل القراءة السريعة على شاشات الموبايل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'paragraph-counter',
  1 => 'sentence-counter',
  2 => 'clean-arabic-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('longTextInputSplit').value.trim();
            const cond = document.getElementById('splitCondition').value;

            let resultParas = [];

            if (cond.startsWith('sentences')) {
                const count = cond === 'sentences_2' ? 2 : 3;
                const sentences = text.match(/[^.!?؟؛]+[.!?؟؛]+/g) || [text];
                for (let i = 0; i < sentences.length; i += count) {
                    resultParas.push(sentences.slice(i, i + count).join(' ').trim());
                }
            } else {
                const words = text.split(/\s+/);
                for (let i = 0; i < words.length; i += 45) {
                    resultParas.push(words.slice(i, i + 45).join(' ').trim());
                }
            }

            const formatted = resultParas.join('\n\n');

            setPrimaryResult('تم تقسيم النص إلى ' + resultParas.length + ' فقرات متوازنة', 'نتيجة التقسيم');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات الناتجة', value: resultParas.length + ' فقرة', color: '#10b981' },
                { label: 'إجمالي كلمات النص', value: text.split(/\s+/).length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص مقسماً إلى فقرات سهلة القراءة:</label>
                    <textarea class="form-control" rows="8" style="direction:rtl;line-height:1.8" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('split-text-paragraphs');
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
    restoreLastInputs('split-text-paragraphs');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>