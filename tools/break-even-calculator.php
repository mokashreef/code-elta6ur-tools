<?php
/**
 * أداة: حاسبة نقطة التعادل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'break-even-calculator';
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
        <label class="form-label" for="fixedCosts">التكاليف الثابتة شهرياً (إيجار، رواتب، اشتراكات)</label>
        <input type="number" id="fixedCosts" class="form-control" value="15000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="unitPrice">سعر بيع الوحدة أو المنتج</label>
        <input type="number" id="unitPrice" class="form-control" value="200" min="1"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="unitVariableCost">التكلفة المتغيرة للوحدة (شراء، تغليف، عمولة بيع)</label>
        <input type="number" id="unitVariableCost" class="form-control" value="80" min="0"  step="5" oninput="calculateTool()">
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
  0 => 'هامش المساهمة للوحدة = سعر البيع - التكلفة المتغيرة.',
  1 => 'نقطة التعادل بالوحدات = التكاليف الثابتة ÷ هامش المساهمة للوحدة.',
  2 => 'نقطة التعادل بالقيمة النقدية = عدد وحدات التعادل × سعر البيع.',
),
        'التكاليف الثابتة لا تتغير مع حجم الإنتاج والمبيعات ضمن النطاق التشغيلي الحالي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا أفعل إذا كانت نقطة التعادل صعبة التحقيق؟',
    'a' => 'يمكنك إما خفض التكاليف الثابتة (مثل مساحة مكتب أصغر)، أو خفض التكلفة المتغيرة بالتفاوض مع الموردين، أو رفع سعر بيع الوحدة مع تحسين قيمتها للعميل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'product-selling-price-calculator',
  1 => 'profit-margin-calculator',
  2 => 'net-profit-margin-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const fixed = Math.max(0, parseFloat(document.getElementById('fixedCosts').value) || 0);
            const price = Math.max(1, parseFloat(document.getElementById('unitPrice').value) || 1);
            const varCost = Math.max(0, parseFloat(document.getElementById('unitVariableCost').value) || 0);
            const curr = getSelectedCurrency();

            const contributionMargin = price - varCost;
            if (contributionMargin <= 0) {
                alert('سعر بيع الوحدة يجب أن يكون أكبر من تكلفتها المتغيرة لتحقيق نقطة التعادل!');
                return;
            }

            const breakEvenUnits = Math.ceil(fixed / contributionMargin);
            const breakEvenRevenue = breakEvenUnits * price;
            const cmRatio = (contributionMargin / price) * 100;

            setPrimaryResult(formatNumber(breakEvenUnits, 0) + ' قطعة / وحدة', 'كمية التعادل المطلوبة شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'قيمة مبيعات التعادل النقدية', value: formatMoney(breakEvenRevenue, curr), color: '#3b82f6' },
                { label: 'هامش المساهمة لكل وحدة (CM)', value: formatMoney(contributionMargin, curr), color: '#10b981' },
                { label: 'نسبة هامش المساهمة', value: cmRatio.toFixed(1) + '%', color: '#8b5cf6' },
                { label: 'التكاليف الثابتة المغطاة', value: formatMoney(fixed, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تحتاج لبيع <strong>${breakEvenUnits} وحدة</strong> شهرياً بمبيعات إجمالية <strong>${formatMoney(breakEvenRevenue, curr)}</strong> لتغطية كافة مصاريفك دون ربح أو خسارة. أي مبيعات إضافية فوق هذا الرقم تمثل أرباحاً صافية.</p>
            `);
        
        saveLastInputs('break-even-calculator');
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
    restoreLastInputs('break-even-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>