<?php
/**
 * أداة: حاسبة هل راتبي يكفيني؟ ومؤشر الأمان المالي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'is-salary-enough-calculator';
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
        <label class="form-label" for="netMonthlySalaryInput">صافي الدخل الشهري المستلم</label>
        <input type="number" id="netMonthlySalaryInput" class="form-control" value="7500" min="500"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="housingRentCost">تكلفة السكن (الإيجار أو قسط التمويل العقاري شهرياً)</label>
        <input type="number" id="housingRentCost" class="form-control" value="2200" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="groceriesFoodCost">مصاريف الطعام والتموين المنزلي شهرياً</label>
        <input type="number" id="groceriesFoodCost" class="form-control" value="1800" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="billsUtilitiesCost">الفواتير الأساسية (كهرباء، ماء، إنترنت، جوال)</label>
        <input type="number" id="billsUtilitiesCost" class="form-control" value="600" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="transportCostLife">المواصلات والبنزين أو قسط السيارة</label>
        <input type="number" id="transportCostLife" class="form-control" value="800" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="debtInstallments">أقساط قروض أو ديون وبطاقات ائتمانية شهرية</label>
        <input type="number" id="debtInstallments" class="form-control" value="500" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="entertainmentPersonal">المصاريف الترفيهية والمطاعم والشراء الشخصي</label>
        <input type="number" id="entertainmentPersonal" class="form-control" value="600" min="0"  step="50"  oninput="calculateTool()">
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
  0 => 'المؤشر المالي السليم يتطلب ألا يتجاوز السكن 30% من صافي الراتب، وألا تتجاوز الديون 33% كحد أقصى.',
  1 => 'تحقيق فائض ادخار شهري لا يقل عن 15% إلى 20% هو الضمانة الحقيقية لبناء الثروة وتأمين المستقبل.',
),
        'يفترض عدم وجود مصاريف طارئة غير مدونة في المدخلات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي الخطوة الأولى إذا كان الراتب لا يكفي وهناك عجز؟',
    'a' => 'ابدأ بإلغاء الاشتراكات غير المستخدمة فوراً، وتقليص مصاريف المطاعم والمقاهي الخارجية بنسبة 50%، واستبدال الماركات التجارية ببدائل محلية ذات سعر اقتصادي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'salary-division-calculator',
  1 => 'family-monthly-budget-calculator',
  2 => 'savings-goal-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(500, parseFloat(document.getElementById('netMonthlySalaryInput').value) || 7500);
            const rent = Math.max(0, parseFloat(document.getElementById('housingRentCost').value) || 2200);
            const food = Math.max(0, parseFloat(document.getElementById('groceriesFoodCost').value) || 1800);
            const bills = Math.max(0, parseFloat(document.getElementById('billsUtilitiesCost').value) || 600);
            const transport = Math.max(0, parseFloat(document.getElementById('transportCostLife').value) || 800);
            const debt = Math.max(0, parseFloat(document.getElementById('debtInstallments').value) || 500);
            const fun = Math.max(0, parseFloat(document.getElementById('entertainmentPersonal').value) || 600);
            const curr = getSelectedCurrency();

            const totalExpenses = rent + food + bills + transport + debt + fun;
            const netBalance = salary - totalExpenses;
            const savingsRate = (netBalance / salary) * 100;
            const rentRatio = (rent / salary) * 100;

            let status = 'وضع مالي ممتاز مع قدرة على الادخار والاستثمار 🌟';
            let color = '#10b981';
            if (netBalance < 0) {
                status = 'عجز مالي شهري يتطلب إعادة هيكلة المصاريف فوراً ❌';
                color = '#ef4444';
            } else if (savingsRate < 10) {
                status = 'الراتب يكفي بالكاد على الحافة دون هامش أمان مالي ⚠️';
                color = '#f59e0b';
            }

            setPrimaryResult(netBalance >= 0 ? 'فائض ادخار: ' + formatMoney(netBalance, curr) : 'عجز مالي: -' + formatMoney(Math.abs(netBalance), curr), 'المحصلة المالية الصافية شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'نسبة الادخار الصافي من الراتب', value: savingsRate.toFixed(1) + '%', color: color },
                { label: 'إجمالي المصاريف والالتزامات', value: formatMoney(totalExpenses, curr), color: '#ef4444' },
                { label: 'نسبة السكن من الراتب (الموصى به < 30%)', value: rentRatio.toFixed(1) + '%', color: rentRatio <= 30 ? '#10b981' : '#f59e0b' },
                { label: 'تقييم الأمان المالي', value: status, color: color }
            ]);

            setResultContent(`
                <p>من إجمالي راتب <strong>${formatMoney(salary, curr)}</strong>، تنفق شهرياً <strong>${formatMoney(totalExpenses, curr)}</strong>، ويتبقى لك <strong>${formatMoney(netBalance, curr)}</strong> بنسبة ادخار <strong>${savingsRate.toFixed(1)}%</strong> - ${status}.</p>
            `);
        
        saveLastInputs('is-salary-enough-calculator');
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
    restoreLastInputs('is-salary-enough-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>