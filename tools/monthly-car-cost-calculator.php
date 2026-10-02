<?php
/**
 * أداة: حاسبة تكلفة السيارة الشهرية الشاملة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'monthly-car-cost-calculator';
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
        <label class="form-label" for="monthlyLoanPayment">قسط التمويل أو الإيجار المنتهي بالتمليك شهرياً (0 إن كانت مسددة)</label>
        <input type="number" id="monthlyLoanPayment" class="form-control" value="1200" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyFuelCost">تكلفة البنزين الشهرية التقديرية</label>
        <input type="number" id="monthlyFuelCost" class="form-control" value="600" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="annualInsuranceCost">التأمين السنوي (شامل أو ضد الغير)</label>
        <input type="number" id="annualInsuranceCost" class="form-control" value="2400" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="annualMaintenanceRepairs">الصيانة الدورية والإطارات والزيوت سنوياً</label>
        <input type="number" id="annualMaintenanceRepairs" class="form-control" value="2000" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="parkingAndWash">غسيل السيارة، المواقف، ورسوم الطرق شهرياً</label>
        <input type="number" id="parkingAndWash" class="form-control" value="150" min="0"  step="25"  oninput="calculateTool()">
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
  0 => 'تكلفة السيارة لا تنتهي عند دفع ثمنها أو قسطها؛ بل تمتد لتشمل التأمين الإلزامي والصيانة الدورية ورسوم الطرق وتجديد الرخص.',
  1 => 'تخصيص صندوق شهري للصيانة الدورية يحميك من الصدمات المالية عند الحاجة لتغيير الإطارات أو البطارية.',
),
        'يفترض عدم وقوع حوادث كبرى غير مغطاة بالتأمين.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما النسبة الآمنة لمصاريف السيارة من الراتب الشهري؟',
    'a' => 'القاعدة المالية الموصى بها هي ألا تتجاوز التكاليف الشاملة للسيارة (القسط + الوقود + التأمين + الصيانة) نسبة 15% إلى 20% كحد أقصى من صافي دخلك الشهري.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'monthly-gas-cost-calculator',
  1 => 'real-car-cost-calculator',
  2 => 'buy-car-vs-transport-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const loan = Math.max(0, parseFloat(document.getElementById('monthlyLoanPayment').value) || 0);
            const fuel = Math.max(0, parseFloat(document.getElementById('monthlyFuelCost').value) || 0);
            const insAnnual = Math.max(0, parseFloat(document.getElementById('annualInsuranceCost').value) || 0);
            const maintAnnual = Math.max(0, parseFloat(document.getElementById('annualMaintenanceRepairs').value) || 0);
            const wash = Math.max(0, parseFloat(document.getElementById('parkingAndWash').value) || 0);
            const curr = getSelectedCurrency();

            const monthlyInsurance = insAnnual / 12;
            const monthlyMaint = maintAnnual / 12;
            const totalMonthly = loan + fuel + monthlyInsurance + monthlyMaint + wash;
            const totalYearly = totalMonthly * 12;

            setPrimaryResult(formatMoney(totalMonthly, curr) + ' شهرياً', 'التكلفة الإجمالية لامتلاك وتشغيل السيارة');
            showResultArea();

            setDetailStats([
                { label: 'قسط السيارة والوقود المباشر', value: formatMoney(loan + fuel, curr), color: '#3b82f6' },
                { label: 'التأمين الموزع شهرياً', value: formatMoney(monthlyInsurance, curr), color: '#10b981' },
                { label: 'مخصص الصيانة والإطارات شهرياً', value: formatMoney(monthlyMaint, curr), color: '#f59e0b' },
                { label: 'إجمالي ما تنفقه سنوياً على السيارة', value: formatMoney(totalYearly, curr), color: '#ef4444' }
            ]);

            setResultContent(`
                <p>تكلفك السيارة فعلياً <strong>${formatMoney(totalMonthly, curr)} شهرياً</strong> (ما يعادل <strong>${formatMoney(totalYearly, curr)} سنوياً</strong>). التكاليف المخفية كالتأمين والصيانة الدورية تمثل حوالي <strong>${totalMonthly > 0 ? (((monthlyInsurance + monthlyMaint)/totalMonthly)*100).toFixed(0) : 0}%</strong> من المصروف الشهري.</p>
            `);
        
        saveLastInputs('monthly-car-cost-calculator');
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
    restoreLastInputs('monthly-car-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>