<?php
/**
 * أداة: إزالة التكرار من النص والأسطر المتشابهة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'remove-duplicate-lines';
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
        <label class="form-label" for="dupLinesInput">ألصق القائمة أو الأسطر المراد إزالة التكرار منها</label>
        <textarea id="dupLinesInput" class="form-control" rows="8" placeholder="ضع الأسطر هنا (سطر لكل عنصر)..." oninput="calculateTool()">تفاح
برتقال
موز
تفاح
عنب
برتقال
مانجو</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="caseSensitiveDup">حساسية حالة الأحرف والمسافات</label>
        <select id="caseSensitiveDup" class="form-control" onchange="calculateTool()">
            <option value="trim" selected>تجاهل المسافات البادئة والختامية (موصى به)</option>
            <option value="exact" >تطابق حرفي تام</option>
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
  0 => 'تحذف الأداة الأسطر المكررة وتحافظ على الترتيب الأصلي لأول ظهور لكل عنصر.',
  1 => 'مثالية لتنظيف قوائم البريد الإلكتروني، الكلمات المفتاحية، وأرقام الهواتف وبيانات العملاء.',
),
        'الأسطر الفارغة تُستبعد تلقائياً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تحافظ الأداة على ترتيب القائمة الأصلي؟',
    'a' => 'نعم؛ يتم الحفاظ على ترتيب الأسطر كما أدخلتها تماماً، مع حذف التكرارات اللاحقة فقط.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sort-text-lines',
  1 => 'remove-empty-lines',
  2 => 'text-to-list-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('dupLinesInput').value;
            const mode = document.getElementById('caseSensitiveDup').value;

            const lines = text.split(/\n/);
            const seen = new Set();
            const uniqueLines = [];

            lines.forEach(line => {
                const key = mode === 'trim' ? line.trim() : line;
                if (key.length > 0 && !seen.has(key)) {
                    seen.add(key);
                    uniqueLines.push(line.trim());
                }
            });

            const resultText = uniqueLines.join('\n');
            const dupesRemoved = lines.filter(l => l.trim().length > 0).length - uniqueLines.length;

            setPrimaryResult('تم حذف ' + dupesRemoved + ' عنصر مكرر بنجاح', 'إزالة التكرار');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر الفريدة المتبقية', value: uniqueLines.length + ' عنصر', color: '#10b981' },
                { label: 'عدد الأسطر المكررة المحذوفة', value: dupesRemoved + ' تكرار', color: '#ef4444' },
                { label: 'إجمالي عناصر القائمة الأصلية', value: lines.filter(l => l.trim().length > 0).length + ' أسطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">القائمة الفريدة المصفاة:</label>
                    <textarea class="form-control" rows="7" style="direction:rtl;line-height:1.7" readonly>${resultText}</textarea>
                </div>
            `);
        
        saveLastInputs('remove-duplicate-lines');
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
    restoreLastInputs('remove-duplicate-lines');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>