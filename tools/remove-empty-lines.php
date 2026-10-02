<?php
/**
 * أداة: إزالة الأسطر الفارغة الزائدة من النص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'remove-empty-lines';
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
        <label class="form-label" for="emptyLinesInput">ألصق النص المحتوي على أسطر فارغة</label>
        <textarea id="emptyLinesInput" class="form-control" rows="8" placeholder="ضع النص هنا..." oninput="calculateTool()">السطر الأول



السطر الثاني



السطر الثالث</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="emptyLineMode">طريقة التنظيف المطلوبة</label>
        <select id="emptyLineMode" class="form-control" onchange="calculateTool()">
            <option value="all" selected>حذف جميع الأسطر الفارغة نهائياً (دمج مباشر)</option>
            <option value="single" >الإبقاء على سطر فارغ واحد فقط بين الفقرات (تنظيف التكرار الزائد)</option>
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
  0 => 'خيار حذف جميع الأسطر مفيد لضغط القوائم والبيانات والأكواد.',
  1 => 'خيار الإبقاء على سطر واحد يحافظ على تنسيق الفقرات للمقالات مع حذف المسافات الرأسية الشاذة.',
),
        'الأسطر التي تحتوي على مسافات بيضاء فقط تُعامل كأسطر فارغة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تتأثر الكلمات والمسافات الأفقية؟',
    'a' => 'لا؛ الأداة تعمل فقط على الفواصل الرأسية (Line breaks) دون المساس بالنصوص الأفقية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'remove-extra-spaces',
  1 => 'remove-duplicate-lines',
  2 => 'merge-lines-tool',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('emptyLinesInput').value;
            const mode = document.getElementById('emptyLineMode').value;

            let cleaned = '';
            if (mode === 'all') {
                cleaned = text.split(/\n/).filter(line => line.trim().length > 0).join('\n');
            } else {
                cleaned = text.replace(/\n\s*\n\s*\n+/g, '\n\n').trim();
            }

            const origLines = text.split(/\n/).length;
            const newLines = cleaned.split(/\n/).length;
            const removed = origLines - newLines;

            setPrimaryResult('تم حذف ' + removed + ' سطر فارغ', 'نتيجة المعالجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المحذوفة', value: removed + ' سطر', color: '#10b981' },
                { label: 'عدد الأسطر الأصلية', value: origLines + ' سطر', color: '#ef4444' },
                { label: 'عدد الأسطر المتبقية', value: newLines + ' سطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص بعد إزالة الأسطر الفارغة:</label>
                    <textarea class="form-control" rows="7" style="direction:rtl;line-height:1.7" readonly>${cleaned}</textarea>
                </div>
            `);
        
        saveLastInputs('remove-empty-lines');
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
    restoreLastInputs('remove-empty-lines');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>