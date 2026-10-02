<?php
/**
 * أداة: حاسبة تكلفة اكتساب العميل (CAC)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cac-calculator';
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
        <label class="form-label" for="marketingSpend">إجمالي مصاريف التسويق والإعلانات في الفترة</label>
        <input type="number" id="marketingSpend" class="form-control" value="10000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="salesSalaries">رواتب وعمولات فريق المبيعات والتسويق</label>
        <input type="number" id="salesSalaries" class="form-control" value="8000" min="0"  step="500" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="softwareCost">اشتراكات برامج وأدوات التسويق والـ CRM</label>
        <input type="number" id="softwareCost" class="form-control" value="1500" min="0"  step="100" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="newCustomers">عدد العملاء الجدد المكتسبين في نفس الفترة</label>
        <input type="number" id="newCustomers" class="form-control" value="150" min="1"  step="5" oninput="calculateTool()">
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
  0 => 'CAC = (إجمالي مصاريف التسويق + رواتب المبيعات + تكاليف الأدوات) ÷ عدد العملاء الجدد.',
  1 => 'يجب مقارنة الـ CAC بالقيمة الدائمة للعميل (LTV) للتأكد من ربحية نموذج العمل.',
),
        'الحساب يشمل العملاء الجدد فقط المكتسبين خلال نفس الفترة الزمنية للمصاريف.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي النسبة الصحية بين LTV و CAC؟',
    'a' => 'النسبة المثالية عالمياً هي 3:1 (أن تكون قيمة العميل الدائمة تعادل 3 أضعاف تكلفة اكتسابه على الأقل).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ltv-calculator',
  1 => 'roas-calculator',
  2 => 'roi-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const mkt = Math.max(0, parseFloat(document.getElementById('marketingSpend').value) || 0);
            const sales = Math.max(0, parseFloat(document.getElementById('salesSalaries').value) || 0);
            const soft = Math.max(0, parseFloat(document.getElementById('softwareCost').value) || 0);
            const customers = Math.max(1, parseFloat(document.getElementById('newCustomers').value) || 1);
            const curr = getSelectedCurrency();

            const totalAcquisitionCost = mkt + sales + soft;
            const cac = totalAcquisitionCost / customers;

            setPrimaryResult(formatMoney(cac, curr), 'تكلفة اكتساب العميل الواحد (CAC)');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي تكاليف التسويق والمبيعات', value: formatMoney(totalAcquisitionCost, curr), color: '#3b82f6' },
                { label: 'عدد العملاء الجدد', value: customers + ' عميل', color: '#10b981' },
                { label: 'نصيب الإعلانات المباشرة من كل عميل', value: formatMoney(mkt / customers, curr), color: '#f59e0b' },
                { label: 'نصيب الرواتب والأدوات من كل عميل', value: formatMoney((sales + soft) / customers, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفك إضافة كل عميل جديد إلى نشاطك <strong>${formatMoney(cac, curr)}</strong> شاملة الإعلانات وجهود فريق المبيعات والبرمجيات المستخدمة.</p>
            `);
        
        saveLastInputs('cac-calculator');
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
    restoreLastInputs('cac-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>