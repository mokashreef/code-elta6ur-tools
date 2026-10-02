<?php
/**
 * أداة: مقارنة النصوص واكتشاف الفروقات (Text Diff Checker)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'text-diff-checker';
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
        <label class="form-label" for="originalTextDiff">النص الأصلي (Original)</label>
        <textarea id="originalTextDiff" class="form-control" rows="6" placeholder="ضع النص الأصلي هنا..." oninput="calculateTool()">الذكاء الاصطناعي هو أحدث ثورة تقنية في القرن الحادي والعشرين.
يساعد المطورين على كتابة الأكواد بسرعة وكفاءة عالية.
التعلم المستمر هو المفتاح.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="modifiedTextDiff">النص المعدل (Modified)</label>
        <textarea id="modifiedTextDiff" class="form-control" rows="6" placeholder="ضع النص المعدل هنا..." oninput="calculateTool()">الذكاء الاصطناعي هو أعظم ثورة تكنولوجية في القرن الحالي.
يساعد المطورين على كتابة واختبار الأكواد بسرعة وكفاءة فائقة.
التعلم والتطبيق المستمر هو سر النجاح.</textarea>
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
  0 => 'يقارن الأسطر سطراً بسطر بنظام الـ Diff المتبع في Git و GitHub.',
  1 => 'يبرز الكلمات المحذوفة باللون الأحمر والكلمات الجديدة المضافة باللون الأخضر.',
),
        'يفترض مقارنة أسطر متتالية متقابلة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن مقارنة أكواد برمجية كاملة بهذه الأداة؟',
    'a' => 'نعم؛ يمكنك مقارنة ملفات JavaScript أو Python أو CSS واكتشاف التعديلات التي أجراها زملاؤك في الفريق فوراً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sql-formatter',
  1 => 'clean-arabic-text',
  2 => 'remove-duplicate-lines',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const orig = document.getElementById('originalTextDiff').value;
            const mod = document.getElementById('modifiedTextDiff').value;

            const origLines = orig.split(/\n/);
            const modLines = mod.split(/\n/);

            let diffHtml = '<div style="font-family:monospace;direction:rtl;line-height:1.8;background:var(--bg-card);padding:1rem;border-radius:var(--radius-md)">';
            const maxL = Math.max(origLines.length, modLines.length);
            let changesCount = 0;

            for (let i = 0; i < maxL; i++) {
                const l1 = origLines[i] || '';
                const l2 = modLines[i] || '';
                if (l1 === l2) {
                    diffHtml += `<div style="color:var(--text-secondary);padding:2px 0">  ${l1 || '&nbsp;'}</div>`;
                } else {
                    changesCount++;
                    if (l1) diffHtml += `<div style="background:rgba(239,68,68,0.15);color:#ef4444;padding:2px 6px;border-radius:3px;margin:2px 0">- ${l1}</div>`;
                    if (l2) diffHtml += `<div style="background:rgba(16,185,129,0.15);color:#10b981;padding:2px 6px;border-radius:3px;margin:2px 0">+ ${l2}</div>`;
                }
            }
            diffHtml += '</div>';

            setPrimaryResult('تم اكتشاف ' + changesCount + ' أسطر معدلة', 'حالة المقارنة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المتغيرة', value: changesCount + ' تعديلات', color: changesCount > 0 ? '#f59e0b' : '#10b981' },
                { label: 'أسطر النص الأصلي', value: origLines.length + ' أسطر', color: '#3b82f6' },
                { label: 'أسطر النص المعدل', value: modLines.length + ' أسطر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <div style="margin-bottom:0.5rem;font-size:0.85rem;color:var(--text-muted)">
                        <span style="color:#ef4444">(-) باللون الأحمر: محذوف</span> | 
                        <span style="color:#10b981">(+) باللون الأخضر: مضاف جديد</span>
                    </div>
                    ${diffHtml}
                </div>
            `);
        
        saveLastInputs('text-diff-checker');
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
    restoreLastInputs('text-diff-checker');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>