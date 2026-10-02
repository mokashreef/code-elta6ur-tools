<?php
/**
 * أداة: حاسبة الخصم والضريبة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'discount-tax-calculator';
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
    <?php renderCurrencySelector('calcCurrency', 'SAR', 'العملة المفضلة للنتائج'); ?>
    <div class="form-group">
        <label class="form-label" for="originalPrice">السعر الأصلي</label>
        <input type="number" id="originalPrice" class="form-control" value="500" min="0"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="discountPercent">نسبة الخصم (%)</label>
        <input type="number" id="discountPercent" class="form-control" value="20" min="0" max="100" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="taxPercent">نسبة الضريبة (%) إن وجدت</label>
        <input type="number" id="taxPercent" class="form-control" value="15" min="0" max="50" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="taxOrder">ترتيب احتساب الضريبة</label>
        <select id="taxOrder" class="form-control" onchange="calculateTool()">
            <option value="after_discount" selected>تطبيق الضريبة بعد الخصم (المعيار التجاري الصحيح)</option>
            <option value="before_discount" >تطبيق الضريبة على السعر الأصلي قبل الخصم</option>
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
  0 => 'الخصم يُحسب أولاً من السعر الأساسي.',
  1 => 'ضريبة القيمة المضافة تُطبق قانونياً على القيمة الفعلية المدفوعة بعد الخصم.',
  2 => 'السعر النهائي = (السعر الأصلي - الخصم) + الضريبة.',
),
        'الأنظمة الضريبية توجب احتساب الضريبة على السعر النهائي المخفض وليس على السعر قبل الخصم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الضريبة تحسب قبل الخصم أم بعده؟',
    'a' => 'حسب لوائح هيئات الزكاة والضرائب العربية، تُفرض ضريبة القيمة المضافة على السعر الفعلي بعد تطبيق أي خصم تجاري ممنوح للعميل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'vat-calculator',
  1 => 'profit-margin-calculator',
  2 => 'product-selling-price-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const orig = Math.max(0, parseFloat(document.getElementById('originalPrice').value) || 0);
            const discRate = Math.max(0, Math.min(100, parseFloat(document.getElementById('discountPercent').value) || 0)) / 100;
            const taxRate = Math.max(0, parseFloat(document.getElementById('taxPercent').value) || 0) / 100;
            const order = document.getElementById('taxOrder').value;
            const curr = getSelectedCurrency();

            const discountAmount = orig * discRate;
            const priceAfterDiscount = orig - discountAmount;
            let taxAmount = 0;
            let finalPrice = 0;

            if (order === 'after_discount') {
                taxAmount = priceAfterDiscount * taxRate;
                finalPrice = priceAfterDiscount + taxAmount;
            } else {
                taxAmount = orig * taxRate;
                finalPrice = priceAfterDiscount + taxAmount;
            }

            setPrimaryResult(formatMoney(finalPrice, curr), 'السعر النهائي للدفع');
            showResultArea();

            setDetailStats([
                { label: 'مبلغ الخصم (التوفير)', value: formatMoney(discountAmount, curr), color: '#10b981' },
                { label: 'السعر بعد الخصم قبل الضريبة', value: formatMoney(priceAfterDiscount, curr), color: '#3b82f6' },
                { label: 'مبلغ ضريبة القيمة المضافة', value: formatMoney(taxAmount, curr), color: '#f59e0b' },
                { label: 'السعر الأصلي الابتدائي', value: formatMoney(orig, curr), color: '#6b7280' }
            ]);

            setResultContent(`
                <p>وفرت <strong>${formatMoney(discountAmount, curr)}</strong> بفضل الخصم، والسعر المطلوب سداده نهائياً شامل الضريبة هو <strong>${formatMoney(finalPrice, curr)}</strong>.</p>
            `);
        
        saveLastInputs('discount-tax-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('discount-tax-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>