<?php
/**
 * أداة: حاسبة تكلفة المعيشة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cost-of-living-calculator';
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
        <label class="form-label" for="housingLivingCost">السكن والإيجار الشهري المتوقع</label>
        <input type="number" id="housingLivingCost" class="form-control" value="2800" min="300"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="foodGroceryLiving">الطعام والتموين والمطاعم شهرياً</label>
        <input type="number" id="foodGroceryLiving" class="form-control" value="2200" min="200"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="transportLiving">المواصلات والبنزين وتطبيقات النقل</label>
        <input type="number" id="transportLiving" class="form-control" value="900" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="servicesAndBills">الخدمات (فواتير، إنترنت، نظافة، جوال)</label>
        <input type="number" id="servicesAndBills" class="form-control" value="650" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="miscLivingCost">رعاية صحية وملابس وترفيه شخصي</label>
        <input type="number" id="miscLivingCost" class="form-control" value="950" min="50"  step="50"  oninput="calculateTool()">
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
  0 => 'تكلفة المعيشة تتفاوت بشكل هائل بين العواصم والمدن الكبرى وبين المدن الهادئة والمحافظات.',
  1 => 'السكن والطعام يمثلان عادة بين 65% إلى 75% من تكلفة المعيشة الإجمالية في أي مدينة.',
),
        'يفترض نمط حياة متوازن لفرد أو أسرة صغيرة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أتحقق من تكلفة المعيشة لمدينة قبل الانتقال إليها؟',
    'a' => 'تصفح مواقع تأجير العقارات المحلية لمعرفة إيجار الأحياء الجيدة، واطلع على أسعار السوبرماركت والمواصلات وتطبيقات توصيل الطعام في تلك المدينة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'city-income-requirement-calculator',
  1 => 'is-salary-enough-calculator',
  2 => 'family-monthly-budget-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rent = Math.max(300, parseFloat(document.getElementById('housingLivingCost').value) || 2800);
            const food = Math.max(200, parseFloat(document.getElementById('foodGroceryLiving').value) || 2200);
            const trans = Math.max(50, parseFloat(document.getElementById('transportLiving').value) || 900);
            const bills = Math.max(50, parseFloat(document.getElementById('servicesAndBills').value) || 650);
            const misc = Math.max(50, parseFloat(document.getElementById('miscLivingCost').value) || 950);
            const curr = getSelectedCurrency();

            const totalMonthly = rent + food + trans + bills + misc;
            const totalAnnual = totalMonthly * 12;
            const requiredSalaryToSave20 = totalMonthly / 0.8;

            setPrimaryResult(formatMoney(totalMonthly, curr) + ' شهرياً', 'تكلفة المعيشة الشهرية الأساسية');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة المعيشة السنوية الإجمالية', value: formatMoney(totalAnnual, curr), color: '#3b82f6' },
                { label: 'الراتب الموصى به للعيش بأمان وادخار 20%', value: formatMoney(requiredSalaryToSave20, curr) + ' / شهرياً', color: '#10b981' },
                { label: 'نسبة بند السكن من المعيشة', value: ((rent / totalMonthly) * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'نسبة الطعام والخدمات', value: (((food + bills) / totalMonthly) * 100).toFixed(0) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكلفك المعيشة في هذا النمط حوالي <strong>${formatMoney(totalMonthly, curr)} شهرياً</strong> (ما يعادل <strong>${formatMoney(totalAnnual, curr)} سنوياً</strong>). للحفاظ على حياة مريحة وادخار 20%، يجب أن يكون راتبك لا يقل عن <strong>${formatMoney(requiredSalaryToSave20, curr)}</strong>.</p>
            `);
        
        saveLastInputs('cost-of-living-calculator');
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
    restoreLastInputs('cost-of-living-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>