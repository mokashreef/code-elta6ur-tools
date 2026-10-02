<?php
/**
 * أداة: تنظيف وتوحيد النص العربي وإزالة التشكيل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'clean-arabic-text';
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
        <label class="form-label" for="rawArabicText">أدخل النص العربي المطلوب تنظيفه</label>
        <textarea id="rawArabicText" class="form-control" rows="6" placeholder="ضع النص العربي هنا..." oninput="calculateTool()">هَـٰذَا نَصٌّ عَرَبِـــــيّ مُشَكَّلٌ وَيَحْتَوِي عَلَى هَمَزَاتٍ مُخْتَلِفَةٍ كَمَا فِي (إِنَّمَا وَأَنْتُمْ وَهَؤُلَاءِ).</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="removeTashkeelOpt">إزالة حركات التشكيل والتنوين</label>
        <select id="removeTashkeelOpt" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، إزالة التشكيل كاملاً</option>
            <option value="no" >لا، الإبقاء على التشكيل</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="removeTatweelOpt">إزالة التطويل والكشيدة (ـ)</label>
        <select id="removeTatweelOpt" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، إزالة الكشيدة والتطويل</option>
            <option value="no" >لا</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="normalizeAlefOpt">توحيد الهمزات (أ، إ، آ -> ا)</label>
        <select id="normalizeAlefOpt" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، توحيد الألف</option>
            <option value="no" >لا، ترك الهمزات كما هي</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="normalizeYaaHaaOpt">توحيد الياء والتاء المربوطة (ى -> ي / ة -> ه)</label>
        <select id="normalizeYaaHaaOpt" class="form-control" onchange="calculateTool()">
            <option value="none" >لا تقم بالتعديل</option>
            <option value="yaa_only" selected>توحيد الألف المقصورة فقط (ى -> ي)</option>
            <option value="all" >توحيد الياء والتاء المربوطة معاً</option>
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
  0 => 'تنظيف النصوص العربية ضروري جداً لتدريب نماذج الذكاء الاصطناعي ومعالجة اللغات الطبيعية (NLP) وبناء محركات البحث.',
  1 => 'إزالة الكشيدة وعلامات التشكيل تضمن تطابق الكلمات أثناء البحث والفهرسة.',
),
        'يحافظ التحويل على الأرقام والكلمات الإنجليزية المدمجة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى يجب توحيد الهمزات؟',
    'a' => 'عند معالجة البيانات وبناء فهارس البحث SEO، لأن المستخدمين يبحثون غالباً بالألف المجردة (ا) بدلاً من (أ) أو (إ).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'remove-extra-spaces',
  1 => 'arabic-word-counter',
  2 => 'arabic-char-counter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let text = document.getElementById('rawArabicText').value;
            const rmTashkeel = document.getElementById('removeTashkeelOpt').value === 'yes';
            const rmTatweel = document.getElementById('removeTatweelOpt').value === 'yes';
            const normAlef = document.getElementById('normalizeAlefOpt').value === 'yes';
            const normYaaHaa = document.getElementById('normalizeYaaHaaOpt').value;

            if (rmTashkeel) {
                text = text.replace(/[\u064B-\u065F\u0670]/g, '');
            }
            if (rmTatweel) {
                text = text.replace(/\u0640/g, ''); // إزالة الكشيدة ـ
            }
            if (normAlef) {
                text = text.replace(/[أإآ]/g, 'ا');
            }
            if (normYaaHaa === 'yaa_only' || normYaaHaa === 'all') {
                text = text.replace(/ى/g, 'ي');
            }
            if (normYaaHaa === 'all') {
                text = text.replace(/ة/g, 'ه');
            }

            text = text.replace(/[ ]{2,}/g, ' ').trim();

            setPrimaryResult('تم تنظيف وتوحيد النص بنجاح (' + text.length + ' حرف)', 'حالة النص');
            showResultArea();

            setDetailStats([
                { label: 'عدد أحرف النص النظيف', value: text.length + ' حرف', color: '#10b981' },
                { label: 'عدد الكلمات الصافية', value: (text ? text.split(/\s+/).length : 0) + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص المنظف الجاهز للنسخ:</label>
                    <textarea class="form-control" rows="6" style="direction:rtl;line-height:1.8" readonly>${text}</textarea>
                </div>
            `);
        
        saveLastInputs('clean-arabic-text');
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
    restoreLastInputs('clean-arabic-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>