<?php
/**
 * أداة: تحويل اتجاه النص بين RTL و LTR وعلامات التوجيه
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'rtl-ltr-text-converter';
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
        <label class="form-label" for="directionTextInput">ألصق النص المطلوب تغيير اتجاهه</label>
        <textarea id="directionTextInput" class="form-control" rows="6" placeholder="ضع النص هنا..." oninput="calculateTool()">const title = 'منصة أدوات المطورين';
// مرحباً بكم في الكود العربي</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="targetDirectionOption">الاتجاه المطلوب</label>
        <select id="targetDirectionOption" class="form-control" onchange="calculateTool()">
            <option value="rtl" >اتجاه من اليمين لليسار (RTL - مناسب للعربية)</option>
            <option value="ltr" selected>اتجاه من اليسار لليمين (LTR - مناسب للأكواد والإنجليزية)</option>
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
  0 => 'مفيد للمبرمجين عند كتابة تعليقات عربية داخل الأكواد البرمجية (Inline Comments) لمنع ارتباك ترتيب الأقواس.',
  1 => 'يساعد في تصحيح النصوص ثنائية اللغة (Bi-directional Text) التي تحتوي على مصطلحات لاتينية مدمجة.',
),
        'يغير خاصية direction و text-align البرمجية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تنقلب علامات الترقيم والأقواس في بعض النصوص العربية؟',
    'a' => 'يحدث هذا عندما يكون اتجاه الصندوق الحاوي (Container) مضبوطاً على LTR، فتعتبر خوارزمية اليونيكود أن القوس يتبع سياقاً لاتينياً فتقلبه، ويتم حل ذلك فوراً بضبط الاتجاه على RTL.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'clean-arabic-text',
  1 => 'clean-word-text',
  2 => 'text-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('directionTextInput').value;
            const dir = document.getElementById('targetDirectionOption').value;

            setPrimaryResult('تم تغيير اتجاه العرض إلى ' + dir.toUpperCase(), 'اتجاه النص');
            showResultArea();

            setDetailStats([
                { label: 'الاتجاه المعتمد', value: dir === 'rtl' ? 'Right-To-Left (يمين)' : 'Left-To-Right (يسار)', color: '#10b981' },
                { label: 'عدد الأسطر', value: text.split(/\n/).length + ' أسطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص بالاتجاه المحدد:</label>
                    <textarea class="form-control" rows="6" style="direction:${dir};text-align:${dir === 'rtl' ? 'right' : 'left'};font-family:monospace;line-height:1.8">${text}</textarea>
                </div>
            `);
        
        saveLastInputs('rtl-ltr-text-converter');
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
    restoreLastInputs('rtl-ltr-text-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>