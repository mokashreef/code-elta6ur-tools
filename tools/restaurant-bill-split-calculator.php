<?php
/**
 * أداة: حاسبة تقسيم فاتورة المطعم والإكرامية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'restaurant-bill-split-calculator';
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
        <label class="form-label" for="billAmountTotal">قيمة الفاتورة الإجمالية للمطعم</label>
        <input type="number" id="billAmountTotal" class="form-control" value="420" min="5"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="peopleCountSplit">عدد الأشخاص المشاركين في الفاتورة</label>
        <input type="number" id="peopleCountSplit" class="form-control" value="4" min="2" max="50" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tipPercentage">نسبة الإكرامية / البقشيش (Tip) % إن رغبت</label>
        <input type="number" id="tipPercentage" class="form-control" value="0" min="0" max="30" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="alreadyIncludesVat">هل الفاتورة شاملة ضريبة القيمة المضافة والخدمة؟</label>
        <select id="alreadyIncludesVat" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، المبلغ شامل كل شيء</option>
            <option value="no" >لا، إضافة 15% ضريبة فوق الفاتورة</option>
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
  0 => 'التقسيم بالتساوي هو الأسرع والأكثر شيوعاً بين الأصدقاء في التجمعات والولائم.',
  1 => 'حساب الضريبة والإكرامية مسبقاً يمنع الإحراج والنقص المالي عند جمع الحساب.',
),
        'يفترض طلبات متقاربة القيمة بين الحاضرين.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما العمل إذا طلب أحد الأشخاص وجبة مكلفة جداً بمفرده؟',
    'a' => 'في هذه الحالة، يدفع صاحب الوجبة المميزة ثمن وجبته منفرداً، ويتم تقسيم باقي الأطباق والمقبلات والمشروبات المشتركة بالتساوي بين الجميع.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'travel-expenses-split-calculator',
  1 => 'rent-split-calculator',
  2 => 'vat-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const bill = Math.max(5, parseFloat(document.getElementById('billAmountTotal').value) || 420);
            const people = Math.max(2, parseInt(document.getElementById('peopleCountSplit').value) || 4);
            const tipRate = Math.max(0, parseFloat(document.getElementById('tipPercentage').value) || 0) / 100;
            const incVat = document.getElementById('alreadyIncludesVat').value === 'yes';
            const curr = getSelectedCurrency();

            const vatAmount = incVat ? 0 : bill * 0.15;
            const billWithVat = bill + vatAmount;
            const tipAmount = billWithVat * tipRate;
            const finalTotal = billWithVat + tipAmount;
            const sharePerPerson = finalTotal / people;

            setPrimaryResult(formatMoney(sharePerPerson, curr) + ' للشخص', 'حصة الفرد الواحد من الفاتورة');
            showResultArea();

            setDetailStats([
                { label: 'حصة كل شخص بالضبط', value: formatMoney(sharePerPerson, curr), color: '#10b981' },
                { label: 'المبلغ الإجمالي النهائي للدفع', value: formatMoney(finalTotal, curr), color: '#3b82f6' },
                { label: 'قيمة الإكرامية المضافة (Tip)', value: formatMoney(tipAmount, curr), color: '#f59e0b' },
                { label: 'عدد الحاضرين على الطاولة', value: people + ' أشخاص', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي الفاتورة <strong>${formatMoney(finalTotal, curr)}</strong> مقسمة على <strong>${people} أشخاص</strong> تعطي حصة <strong>${formatMoney(sharePerPerson, curr)}</strong> لكل فرد بالضبط.</p>
            `);
        
        saveLastInputs('restaurant-bill-split-calculator');
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
    restoreLastInputs('restaurant-bill-split-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>