<?php
/**
 * أداة: توليد الـ Slug المخصص (عربي أو تعريب صوتي)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'arabic-english-slug-generator';
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
        <label class="form-label" for="titleToSlugInput">عنوان المقال أو الصفحة</label>
        <input type="text" id="titleToSlugInput" class="form-control" value="أفضل 10 نصائح لتصميم وبرمجة المواقع في 2026!"    placeholder="اكتب العنوان هنا..." oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="slugLanguageMode">صيغة الرابط المستهدفة</label>
        <select id="slugLanguageMode" class="form-control" onchange="calculateTool()">
            <option value="arabic" selected>رابط بالكلمات العربية الصافية (مثل: افضل-نصائح-تصميم-مواقع)</option>
            <option value="latin_translit" >تعريب صوتي لاتيني للمبرمجين (Franco / Transliteration)</option>
            <option value="english_simple" >إنجليزي بأحرف وأرقام مبسطة</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="slugSeparator">الفاصل بين الكلمات</label>
        <select id="slugSeparator" class="form-control" onchange="calculateTool()">
            <option value="dash" selected>شرطة عادية - (المعيار الأفضل للسيو)</option>
            <option value="underscore" >شرطة سفلية _</option>
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
  0 => 'الروابط النظيفة (Friendly URLs) تحسن ظهور الموقع في محركات البحث وتسهل مشاركتها على وسائل التواصل.',
  1 => 'تفضل محركات البحث مثل Google استخدام الشرطة العادية (-) كفاصل بين الكلمات بدلاً من الشرطة السفلية (_).',
),
        'تُحذف علامات التعجب والاستفهام والأقواس والرموز التعبيرية تلقائياً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أفضل للسيو: الرابط العربي أم الإنجليزي؟',
    'a' => 'الروابط العربية ممتازة للمواقع الموجهة حصراً للمستخدم العربي وتزيد من نسبة النقر (CTR)، بينما الروابط الإنجليزية أو المعربة صوتياً تكون أسهل في النسخ والمشاركة عبر التطبيقات دون تشويه الرابط برموز النسبة المئوية %D8%A7.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'slug-converter',
  1 => 'keyword-density-calculator',
  2 => 'clean-arabic-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const title = document.getElementById('titleToSlugInput').value.trim();
            const mode = document.getElementById('slugLanguageMode').value;
            const sep = document.getElementById('slugSeparator').value === 'underscore' ? '_' : '-';

            let slug = '';
            if (mode === 'arabic') {
                slug = title
                    .replace(/[\u064B-\u065F\u0670]/g, '') // إزالة التشكيل
                    .replace(/[أإآ]/g, 'ا')
                    .replace(/ة/g, 'ه')
                    .replace(/[^\u0600-\u06FFa-zA-Z0-9\s]/g, '') // إزالة الرموز
                    .trim()
                    .replace(/\s+/g, sep)
                    .toLowerCase();
            } else {
                // تعريب صوتي مبسط للأحرف العربية
                const map = {
                    'ا': 'a', 'أ': 'a', 'إ': 'e', 'آ': 'aa', 'ب': 'b', 'ت': 't', 'ث': 'th',
                    'ج': 'j', 'ح': 'h', 'خ': 'kh', 'د': 'd', 'ذ': 'th', 'ر': 'r', 'ز': 'z',
                    'س': 's', 'ش': 'sh', 'ص': 's', 'ض': 'd', 'ط': 't', 'ظ': 'z', 'ع': 'a',
                    'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ك': 'k', 'ل': 'l', 'م': 'm', 'ن': 'n',
                    'ه': 'h', 'ة': 'a', 'و': 'w', 'ي': 'y', 'ى': 'a', 'ء': '', 'ئ': 'e', 'ؤ': 'o'
                };
                let converted = '';
                for (let ch of title) {
                    converted += map[ch] !== undefined ? map[ch] : ch;
                }
                slug = converted
                    .replace(/[^a-zA-Z0-9\s]/g, '')
                    .trim()
                    .replace(/\s+/g, sep)
                    .toLowerCase();
            }

            setPrimaryResult(slug, 'الرابط النظيف الدائم (Slug URL)');
            showResultArea();

            setDetailStats([
                { label: 'طول الرابط الناتج', value: slug.length + ' حرف', color: '#10b981' },
                { label: 'الفاصل المعتمد', value: sep === '-' ? 'شرطة -' : 'شرطة سفلية _', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">رابط المقال الدائم المقترح:</label>
                    <input type="text" class="form-control" style="font-family:monospace;direction:ltr" value="https://example.com/blog/${slug}" readonly>
                </div>
            `);
        
        saveLastInputs('arabic-english-slug-generator');
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
    restoreLastInputs('arabic-english-slug-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>