<?php
/**
 * أداة: حاسبة تكلفة السيارة الحقيقية (التكلفة الإجمالية للملكية TCO)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'real-car-cost-calculator';
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
        <label class="form-label" for="carPurchasePrice">سعر شراء السيارة</label>
        <input type="number" id="carPurchasePrice" class="form-control" value="90000" min="5000"  step="5000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ownershipYears">مدة الاحتفاظ بالسيارة قبل البيع (بالسنوات)</label>
        <select id="ownershipYears" class="form-control" onchange="calculateTool()">
            <option value="3" >3 سنوات</option>
            <option value="5" selected>5 سنوات (المعيار الأكثر شيوعاً)</option>
            <option value="7" >7 سنوات</option>
            <option value="10" >10 سنوات</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="resaleValuePercent">القيمة المتبقية التقديرية للسيارة عند بيعها (%)</label>
        <input type="number" id="resaleValuePercent" class="form-control" value="45" min="10" max="80" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="yearlyFuelCostInput">تكلفة البنزين السنوية المتوقعة</label>
        <input type="number" id="yearlyFuelCostInput" class="form-control" value="7200" min="1000"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="yearlyInsuranceAndMaint">التأمين والصيانة الدورية والتراخيص سنوياً</label>
        <input type="number" id="yearlyInsuranceAndMaint" class="form-control" value="4500" min="500"  step="500"  oninput="calculateTool()">
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
  0 => 'انخفاض القيمة السوقية (Depreciation) هو أكبر تكلفة خفية لامتلاك أي سيارة جديدة، حيث تفقد السيارة بين 15% إلى 25% من قيمتها في السنة الأولى وحدها.',
  1 => 'التكلفة الإجمالية للملكية (TCO) = هبوط القيمة + الوقود + التأمين + الصيانة + الفوائد.',
),
        'السيارة تحافظ على حوالي 40% إلى 50% من قيمتها الأصلية بعد 5 سنوات من الاستخدام العادي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أشتري سيارة بأقل خسارة في انخفاض القيمة؟',
    'a' => 'شراء سيارة مستعملة نظيفة بعمر سنتين إلى 3 سنوات يجعل المالك الأول يتحمل قمة هبوط القيمة (Depreciation)، وتشتريها أنت بسعر منخفض وتبيعها لاحقاً بخسارة طفيفة جداً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'monthly-car-cost-calculator',
  1 => 'buy-car-vs-transport-calculator',
  2 => 'gas-vs-ev-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const price = Math.max(5000, parseFloat(document.getElementById('carPurchasePrice').value) || 90000);
            const years = parseInt(document.getElementById('ownershipYears').value) || 5;
            const resalePct = Math.max(10, Math.min(80, parseFloat(document.getElementById('resaleValuePercent').value) || 45)) / 100;
            const fuelYear = Math.max(1000, parseFloat(document.getElementById('yearlyFuelCostInput').value) || 7200);
            const maintYear = Math.max(500, parseFloat(document.getElementById('yearlyInsuranceAndMaint').value) || 4500);
            const curr = getSelectedCurrency();

            const resaleValue = price * resalePct;
            const totalDepreciation = price - resaleValue;
            const totalFuel = fuelYear * years;
            const totalMaint = maintYear * years;
            const totalCostOfOwnership = totalDepreciation + totalFuel + totalMaint;
            const monthlyTco = totalCostOfOwnership / (years * 12);

            setPrimaryResult(formatMoney(monthlyTco, curr) + ' شهرياً', 'التكلفة الحقيقية الكاملة لامتلاك السيارة (TCO)');
            showResultArea();

            setDetailStats([
                { label: 'انخفاض قيمة السيارة (Depreciation)', value: formatMoney(totalDepreciation, curr), color: '#ef4444' },
                { label: 'إجمالي تكاليف الوقود طوال ' + years + ' سنوات', value: formatMoney(totalFuel, curr), color: '#f59e0b' },
                { label: 'إجمالي الصيانة والتأمين والتراخيص', value: formatMoney(totalMaint, curr), color: '#3b82f6' },
                { label: 'القيمة المستردة عند بيع السيارة مستقبلاً', value: formatMoney(resaleValue, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>خلال <strong>${years} سنوات</strong>، تكلفك هذه السيارة فعلياً <strong>${formatMoney(totalCostOfOwnership, curr)}</strong> (بمعدل <strong>${formatMoney(monthlyTco, curr)} شهرياً</strong>). انخفاض قيمة السيارة وحده يمثل <strong>${formatMoney(totalDepreciation, curr)}</strong> وهي خسارة غير مرئية حتى لحظة البيع.</p>
            `);
        
        saveLastInputs('real-car-cost-calculator');
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
    restoreLastInputs('real-car-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>