<?php
/**
 * أداة: ترتيب النص والأسطر أبجدياً (أ إلى ي)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sort-text-lines';
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
        <label class="form-label" for="sortLinesInput">ألصق الأسطر المراد ترتيبها</label>
        <textarea id="sortLinesInput" class="form-control" rows="7" placeholder="اكتب سطراً لكل عنصر..." oninput="calculateTool()">محمد
أحمد
خالد
إبراهيم
يوسف
عمر
بلال</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="sortDirection">اتجاه الترتيب</label>
        <select id="sortDirection" class="form-control" onchange="calculateTool()">
            <option value="asc" selected>أبجدي تصاعدي (أ -> ي / A -> Z)</option>
            <option value="desc" >أبجدي تنازلي (ي -> أ / Z -> A)</option>
            <option value="length_asc" >حسب طول السطر (من الأقصر للأطول)</option>
            <option value="length_desc" >حسب طول السطر (من الأطول للأقصر)</option>
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
  0 => 'الترتيب الأبجدي يعتمد الترتيب الهجائي العربي الرسمي مع مراعاة الحروف الخاصة والهمزات.',
  1 => 'يتوفر أيضاً خيار الترتيب حسب طول السطر وهو مفيد لتنظيم الكلمات الدلالية والشعارات.',
),
        'الأسطر الفارغة تُستثنى من الترتيب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف يعامل الترتيب همزة الوصل والقطع؟',
    'a' => 'تتبع الأداة الترتيب اللغوي القياسي لـ Unicode الخاص باللغة العربية حيث تأتي الألف بكافة أشكالها (أ، إ، آ، ا) في بداية الترتيب.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'remove-duplicate-lines',
  1 => 'text-to-list-converter',
  2 => 'clean-arabic-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('sortLinesInput').value;
            const dir = document.getElementById('sortDirection').value;

            let lines = text.split(/\n/).filter(l => l.trim().length > 0);

            if (dir === 'asc') {
                lines.sort((a, b) => a.trim().localeCompare(b.trim(), 'ar'));
            } else if (dir === 'desc') {
                lines.sort((a, b) => b.trim().localeCompare(a.trim(), 'ar'));
            } else if (dir === 'length_asc') {
                lines.sort((a, b) => a.trim().length - b.trim().length);
            } else if (dir === 'length_desc') {
                lines.sort((a, b) => b.trim().length - a.trim().length);
            }

            const sortedText = lines.join('\n');

            setPrimaryResult('تم ترتيب ' + lines.length + ' سطر بنجاح', 'حالة الترتيب');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المرتبة', value: lines.length + ' عنصر', color: '#10b981' },
                { label: 'النوع المعتمد', value: dir.includes('length') ? 'حسب الطول' : 'أبجدي عربي', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">الأسطر بعد الترتيب:</label>
                    <textarea class="form-control" rows="7" style="direction:rtl;line-height:1.7" readonly>${sortedText}</textarea>
                </div>
            `);
        
        saveLastInputs('sort-text-lines');
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
    restoreLastInputs('sort-text-lines');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>