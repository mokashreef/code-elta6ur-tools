<?php
/**
 * أداة: استخراج الهاشتاغات (#) من النص ومنشورات السوشيال ميديا
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'extract-hashtags-from-text';
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
        <label class="form-label" for="hashtagInputText">ألصق المنشور أو النص المحتوي على وسوم</label>
        <textarea id="hashtagInputText" class="form-control" rows="6" placeholder="ضع المنشور هنا..." oninput="calculateTool()">يسعدنا إطلاق منصة الأدوات الجديدة اليوم! #تقنية #برمجة_المواقع #السعودية #تطوير_الويب مع أحدث أدوات #الذكاء_الاصطناعي و #تقنية مكرر.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="removeDuplicateTags">إزالة الوسوم المكررة</label>
        <select id="removeDuplicateTags" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، وسوم فريدة فقط</option>
            <option value="no" >لا</option>
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
  0 => 'تدعم استخراج الهاشتاغات العربية المشتملة على شرطة سفلية (_) والهاشتاغات الإنجليزية والأرقام.',
  1 => 'تجمع الوسوم في سطر واحد لتسهيل نسخها ولصقها في التعليق الأول أو نهاية المنشورات.',
),
        'الهاشتاغ يجب أن يبدأ برمز # ولا يحتوي على مسافات بداخله.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم عدد الهاشتاغات الموصى به في منشورات إنستغرام وإكس؟',
    'a' => 'التوصية الحالية لخوارزميات 2025 هي استخدام من 3 إلى 5 هاشتاغات مركزة وذات صلة وثيقة بموضوع المنشور، وتجنب حشو أكثر من 15 وسماً لتفادي اعتبار الحساب كـ Spam.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'extract-keywords-from-text',
  1 => 'extract-urls-from-text',
  2 => 'social-media-pricing-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('hashtagInputText').value;
            const rmDup = document.getElementById('removeDuplicateTags').value === 'yes';

            // استخراج الهاشتاغات العربية والإنجليزية مع علامة الشرطة السفلية
            const tagRegex = /(#[\u0600-\u06FFa-zA-Z0-9_]+)/g;
            let matches = text.match(tagRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches));
            }

            const joined = matches.join(' ');

            setPrimaryResult(matches.length + ' هاشتاغ تم استخراجه', 'عدد الهاشتاغات المكتشفة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الوسوم المستخرجة', value: matches.length + ' وسم', color: '#10b981' },
                { label: 'جاهز للنشر على', value: 'إكس، إنستغرام، لينكدإن، تيك توك', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">الهاشتاغات مفصولة بمسافات (جاهزة للنسخ في المنشور):</label>
                    <textarea class="form-control" rows="4" style="direction:rtl;line-height:1.8" readonly>${joined}</textarea>
                </div>
            `);
        
        saveLastInputs('extract-hashtags-from-text');
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
    restoreLastInputs('extract-hashtags-from-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>