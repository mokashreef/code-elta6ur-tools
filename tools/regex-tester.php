<?php
/**
 * أداة: مختبر التعابير النمطية (Regex Tester)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'regex-tester';
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
        <label class="form-label" for="regexPatternInput">التعبير النمطي (Regular Expression)</label>
        <input type="text" id="regexPatternInput" class="form-control" value="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}"    placeholder="مثال: \d{3}-\d{4}" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="regexFlagsInput">المحددات (Flags)</label>
        <input type="text" id="regexFlagsInput" class="form-control" value="gi"    placeholder="g, i, m" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="regexTestStringInput">نص الاختبار للفحص واستخراج المطابقات</label>
        <textarea id="regexTestStringInput" class="form-control" rows="6" placeholder="ضع النص المراد فحصه هنا..." oninput="calculateTool()">تواصل معنا عبر user@test.com أو admin@elta6ur.org وسنقوم بالرد قريباً.
البريد غير الصالح: invalid-email@</textarea>
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
  0 => 'يبرز المطابقات بلون مميز فورياً أثناء الكتابة.',
  1 => 'يدعم الأعلام الشهيرة: g (شامل لكامل النص)، i (تجاهل حالة الأحرف)، m (متعدد الأسطر).',
),
        'يعتمد محرك الـ RegExp القياسي للغة JavaScript.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يعني المحدد g؟',
    'a' => 'المحدد g (Global) يبحث عن كافة المطابقات في النص بالكامل، وبدونه يتوقف البحث عند أول مطابقة يجدها فقط.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'extract-emails-from-text',
  1 => 'extract-urls-from-text',
  2 => 'case-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const pattern = document.getElementById('regexPatternInput').value;
            const flags = document.getElementById('regexFlagsInput').value;
            const testStr = document.getElementById('regexTestStringInput').value;

            if (!pattern) {
                setPrimaryResult('أدخل التعبير النمطي أولاً', 'الحالة');
                showResultArea();
                return;
            }

            try {
                const re = new RegExp(pattern, flags);
                const matches = testStr.match(re) || [];

                // إبراز المطابقات في النص
                let highlighted = testStr.replace(re, function(match) {
                    return '<mark style="background:#6c63ff;color:#fff;padding:2px 4px;border-radius:4px">' + match + '</mark>';
                });

                setPrimaryResult('تم العثور على ' + matches.length + ' مطابقة بنجاح', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'عدد التطابقات المكتشفة', value: matches.length + ' مطابقة', color: '#10b981' },
                    { label: 'صحة التعبير النمطي', value: 'صحيح وسليم نحوياً ✅', color: '#3b82f6' }
                ]);

                setResultContent(`
                    <div style="margin-top:1rem">
                        <label class="form-label">معاينة النصوص المطابقة مميزة باللون:</label>
                        <div class="form-control" style="min-height:100px;line-height:1.8;direction:ltr;background:var(--bg-card);font-family:monospace">${highlighted}</div>
                    </div>
                `);
            } catch (e) {
                setPrimaryResult('خطأ في صيغة التعبير النمطي ❌', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الـ Regex', value: 'غير صالح ❌', color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class="alert alert-danger" style="margin-top:1rem;color:#ef4444;background:rgba(239,68,68,0.1);border:1px solid #ef4444">
                        <strong>خطأ في Regex:</strong> ${e.message}
                    </div>
                `);
            }
        
        saveLastInputs('regex-tester');
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
    restoreLastInputs('regex-tester');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>