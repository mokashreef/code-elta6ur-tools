<?php
/**
 * أداة: تنظيف النص المنسوخ من Word وبرامج المايكروسوفت
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'clean-word-text';
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
        <label class="form-label" for="wordTextInput">ألصق النص المنسوخ من مستند Word هنا</label>
        <textarea id="wordTextInput" class="form-control" rows="8" placeholder="ضع النص المنسوخ من وورد هنا..." oninput="calculateTool()">هذا نص منسوخ من ملف Word يحتوي على رموز تحكم خفية مثل:
- خطوط منكسرة
- واقتباسات مايكروسوفت “المنحنية”
- ومسافات ناعمة &nbsp; وعلامات فقرات غريبة.</textarea>
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
  0 => 'برنامج Microsoft Word يضيف رموز تحكم وتنسيقات خفية (مثل مسافات الصفر Zero-Width Spaces) تتسبب في انهيار البرمجيات وتشوه قواعد البيانات.',
  1 => 'هذه الأداة تجرد النص من كافة شفرات وورد المشوهة وتبقيه نصاً برمجياً نقياً UTF-8.',
),
        'يحافظ على الفقرات والكلمات الأصلية كما هي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تظهر مربعات غريبة أحياناً عند نسخ نصوص وورد إلى المواقع؟',
    'a' => 'بسبب وجود محارف خاصة غير مرئية بخط وورد لا يتعرف عليها متصفح الويب، وتعمل هذه الأداة على تنظيفها بالكامل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'clean-html-text',
  1 => 'clean-arabic-text',
  2 => 'remove-extra-spaces',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let text = document.getElementById('wordTextInput').value;

            let cleaned = text
                .replace(/[\u200B-\u200D\uFEFF]/g, '') // إزالة المسافات الصفرية الخفية Zero-Width
                .replace(/[\u2018\u2019]/g, "'") // توحيد الاقتباس المفرد
                .replace(/[\u201C\u201D]/g, '"') // توحيد الاقتباس المزدوج
                .replace(/\u2013/g, '-') // توحيد En-dash
                .replace(/\u2014/g, '--') // توحيد Em-dash
                .replace(/\u2026/g, '...') // توحيد علامة الحذف
                .replace(/&nbsp;/g, ' ')
                .replace(/\r\n/g, '\n')
                .replace(/\r/g, '\n')
                .replace(/[ ]{2,}/g, ' ')
                .trim();

            const saved = text.length - cleaned.length;

            setPrimaryResult('تم تنظيف ' + saved + ' رمز تحكم خفي من مستند Word', 'حالة التنظيف');
            showResultArea();

            setDetailStats([
                { label: 'الرموز والمحارف الخفية المزالة', value: saved + ' رمز', color: '#10b981' },
                { label: 'عدد الأحرف الصافية', value: cleaned.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص النظيف الخالي من أخطاء وتشويهات مايكروسوفت وورد:</label>
                    <textarea class="form-control" rows="7" style="direction:rtl;line-height:1.8" readonly>${cleaned}</textarea>
                </div>
            `);
        
        saveLastInputs('clean-word-text');
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
    restoreLastInputs('clean-word-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>