<?php
/**
 * أداة: إزالة المسافات الزائدة وضبط التباعد
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'remove-extra-spaces';
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
        <label class="form-label" for="spacesInputText">ألصق النص الذي يحتوي على مسافات مشتتة أو فراغات زائدة</label>
        <textarea id="spacesInputText" class="form-control" rows="6" placeholder="اكتب النص هنا..." oninput="calculateTool()">هذا    نص     يحتوي   على   مسافات    فارغة     كثيرة    بين   الكلمات .  وكذلك   قبل   علامات  الترقيم  .</textarea>
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
  0 => 'يقوم المعالج بإلغاء المسافات المتعددة واستبدالها بمسافة واحدة قياسية.',
  1 => 'يصحح مواضع علامات الترقيم بحيث تلتصق بالكلمة السابقة وتتبعها مسافة واحدة تفصلها عن الكلمة اللاحقة وفقاً لقواعد الإملاء العربية السليمة.',
),
        'يحافظ على فواصل الأسطر دون حذف.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أين توضع المسافة بالنسبة لعلامات الترقيم؟',
    'a' => 'القاعدة الإملائية العربية: علامة الترقيم (كالنقطة والفاصلة) تلتصق بالكلمة التي قبلها مباشرة بدون أي مسافة، وتترك مسافة واحدة بعدها قبل الكلمة التالية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'remove-empty-lines',
  1 => 'clean-arabic-text',
  2 => 'remove-duplicate-lines',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let text = document.getElementById('spacesInputText').value;
            // إزالة المسافات المتكررة بين الكلمات
            let cleaned = text.replace(/[ ]{2,}/g, ' ');
            // إزالة المسافة قبل علامات الترقيم
            cleaned = cleaned.replace(/\s+([.,،؛:!؟?])/g, '$1');
            // التأكد من وجود مسافة بعد علامة الترقيم إذا كان بعدها حرف
            cleaned = cleaned.replace(/([.,،؛:!؟?])([^\s\d])/g, '$1 $2');
            cleaned = cleaned.trim();

            const savedChars = text.length - cleaned.length;

            setPrimaryResult('تم تقليص ' + savedChars + ' مسافة زائدة', 'حالة تنظيف المسافات');
            showResultArea();

            setDetailStats([
                { label: 'الأحرف المحذوفة الزائدة', value: savedChars + ' مسافة', color: '#10b981' },
                { label: 'طول النص الأصلي', value: text.length + ' حرف', color: '#ef4444' },
                { label: 'طول النص بعد الضبط', value: cleaned.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص المضبوط باحترافية:</label>
                    <textarea class="form-control" rows="6" style="direction:rtl;line-height:1.8" readonly>${cleaned}</textarea>
                </div>
            `);
        
        saveLastInputs('remove-extra-spaces');
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
    restoreLastInputs('remove-extra-spaces');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>