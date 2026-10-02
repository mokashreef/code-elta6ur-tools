<?php
/**
 * أداة: حاسبة ضريبة القيمة المضافة (VAT)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'vat-calculator';
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
        <label class="form-label" for="vatAmountInput">المبلغ</label>
        <input type="number" id="vatAmountInput" class="form-control" value="1000" min="0"  step="10" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="vatRate">نسبة ضريبة القيمة المضافة (%)</label>
        <select id="vatRate" class="form-control" onchange="calculateTool()">
            <option value="15" selected>15% (المملكة العربية السعودية)</option>
            <option value="5" >5% (الإمارات، سلطنة عمان، البحرين)</option>
            <option value="14" >14% (جمهورية مصر العربية)</option>
            <option value="16" >16% (الأردن)</option>
            <option value="19" >19% (الجزائر)</option>
            <option value="20" >20% (المغرب)</option>
            <option value="other" >نسبة مخصصة أخرى</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="customVatRate">النسبة المخصصة (%) - إذا اخترت أخرى</label>
        <input type="number" id="customVatRate" class="form-control" value="15" min="0" max="50" step="0.5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="vatCalcType">نوع الحساب</label>
        <select id="vatCalcType" class="form-control" onchange="calculateTool()">
            <option value="add" selected>إضافة الضريبة (المبلغ المدخل غير شامل الضريبة)</option>
            <option value="extract" >فصل واستخراج الضريبة (المبلغ المدخل شامل الضريبة بالفعل)</option>
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
  0 => 'لإضافة الضريبة: المبلغ الصافي × (1 + نسبة الضريبة).',
  1 => 'لاستخراج الضريبة من مبلغ إجمالي: المبلغ الصافي = المبلغ الإجمالي ÷ (1 + نسبة الضريبة).',
),
        'النسب الافتراضية مطابقة للنسب الرسمية المعتمدة في كل دولة عربية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أستخرج الضريبة من فاتورة شاملة الضريبة؟',
    'a' => 'اقسم المبلغ الإجمالي على (1 + نسبة الضريبة)، فمثلاً في السعودية اقسم على 1.15 للحصول على المبلغ الأصلي ثم اطرحه من الإجمالي لمعرفة قيمة الضريبة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'discount-tax-calculator',
  1 => 'invoice-generator',
  2 => 'net-salary-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const amt = Math.max(0, parseFloat(document.getElementById('vatAmountInput').value) || 0);
            let rateVal = document.getElementById('vatRate').value;
            let rate = rateVal === 'other' ? parseFloat(document.getElementById('customVatRate').value) || 15 : parseFloat(rateVal);
            rate = Math.max(0, rate) / 100;
            const calcType = document.getElementById('vatCalcType').value;
            const curr = getSelectedCurrency();

            let baseAmount = 0;
            let vatAmount = 0;
            let totalAmount = 0;

            if (calcType === 'add') {
                baseAmount = amt;
                vatAmount = amt * rate;
                totalAmount = amt + vatAmount;
            } else {
                totalAmount = amt;
                baseAmount = amt / (1 + rate);
                vatAmount = totalAmount - baseAmount;
            }

            setPrimaryResult(formatMoney(vatAmount, curr), 'قيمة ضريبة القيمة المضافة');
            showResultArea();

            setDetailStats([
                { label: 'المبلغ الأساسي (قبل الضريبة)', value: formatMoney(baseAmount, curr), color: '#3b82f6' },
                { label: 'قيمة الضريبة المضافة (' + (rate * 100) + '%)', value: formatMoney(vatAmount, curr), color: '#f59e0b' },
                { label: 'المبلغ الإجمالي (شامل الضريبة)', value: formatMoney(totalAmount, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>عند ${calcType === 'add' ? 'إضافة ضريبة' : 'استخراج ضريبة'} بنسبة <strong>${(rate * 100).toFixed(1)}%</strong> على مبلغ <strong>${formatMoney(amt, curr)}</strong>، تكون الضريبة <strong>${formatMoney(vatAmount, curr)}</strong> والإجمالي شامل الضريبة <strong>${formatMoney(totalAmount, curr)}</strong>.</p>
            `);
        
        saveLastInputs('vat-calculator');
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
    restoreLastInputs('vat-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>