<?php
/**
 * أداة: دمج الأسطر في سطر واحد أو فقرة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'merge-lines-tool';
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
        <label class="form-label" for="mergeInputText">ألصق الأسطر المراد دمجها</label>
        <textarea id="mergeInputText" class="form-control" rows="6" placeholder="ضع الأسطر هنا..." oninput="calculateTool()">السطر الأول من الفكرة
تكملة السطر الثاني
الخاتمة للسطر الثالث</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="mergeSeparatorOption">الفاصل المستخدم بين الأسطر المدمجة</label>
        <select id="mergeSeparatorOption" class="form-control" onchange="calculateTool()">
            <option value="space" selected>مسافة واحدة (تحويل لفقرة متصلة)</option>
            <option value="comma_ar" >فاصلة عربية ومسافة (، )</option>
            <option value="comma_en" >فاصلة إنجليزية ومسافة (, )</option>
            <option value="dash" >شرطة مع مسافات ( - )</option>
            <option value="pipe" >رمز الفاصل الرأسي ( | )</option>
            <option value="none" >دمج مباشر بدون أي فواصل</option>
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
  0 => 'مثالية لإصلاح النصوص المنسوخة من ملفات PDF التي تحتوي على انكسارات أسطر مفاجئة ومشوهة.',
  1 => 'تتيح دمج الكلمات المفتاحية في سطر واحد مفصول بفواصل مناسبة لمحركات البحث.',
),
        'يتم حذف المسافات الزائدة من أطراف كل سطر قبل الدمج.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أصلح نصوص PDF المشوهة بالأسطر القصيرة؟',
    'a' => 'اختر خيار الدمج بمسافة واحدة؛ ستقوم الأداة بوصل الجمل المكسورة وتحويلها إلى فقرة متصلة طبيعية وسلسة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'text-to-list-converter',
  1 => 'remove-empty-lines',
  2 => 'remove-extra-spaces',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('mergeInputText').value;
            const sepOpt = document.getElementById('mergeSeparatorOption').value;

            let sep = ' ';
            if (sepOpt === 'comma_ar') sep = '، ';
            if (sepOpt === 'comma_en') sep = ', ';
            if (sepOpt === 'dash') sep = ' - ';
            if (sepOpt === 'pipe') sep = ' | ';
            if (sepOpt === 'none') sep = '';

            const lines = text.split(/\n/).map(l => l.trim()).filter(l => l.length > 0);
            const merged = lines.join(sep);

            setPrimaryResult('تم دمج ' + lines.length + ' أسطر في نص واحد', 'حالة الدمج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المدمجة', value: lines.length + ' أسطر', color: '#10b981' },
                { label: 'طول النص الناتج', value: merged.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص بعد الدمج:</label>
                    <textarea class="form-control" rows="6" style="direction:rtl;line-height:1.8" readonly>${merged}</textarea>
                </div>
            `);
        
        saveLastInputs('merge-lines-tool');
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
    restoreLastInputs('merge-lines-tool');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>