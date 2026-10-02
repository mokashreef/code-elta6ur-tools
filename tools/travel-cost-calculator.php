<?php
/**
 * أداة: حاسبة تكلفة السفر الشاملة للرحلات السياحية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'travel-cost-calculator';
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
        <label class="form-label" for="travelersCount">عدد المسافرين</label>
        <input type="number" id="travelersCount" class="form-control" value="2" min="1" max="20" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tripDaysCount">مدة الرحلة (عدد الأيام)</label>
        <input type="number" id="tripDaysCount" class="form-control" value="7" min="1" max="90" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="flightTicketPerPerson">سعر تذكرة الطيران ذهاب وعودة للشخص</label>
        <input type="number" id="flightTicketPerPerson" class="form-control" value="1800" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hotelNightCost">سعر الغرفة الفندقية في الليلة</label>
        <input type="number" id="hotelNightCost" class="form-control" value="450" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailySpendPerPerson">المصروف اليومي التقديري للشخص (طعام، تذاكر فعاليات، مواصلات)</label>
        <input type="number" id="dailySpendPerPerson" class="form-control" value="200" min="20"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="visaInsurancePerPerson">رسوم التأشيرة والتأمين الطبي لكل مسافر</label>
        <input type="number" id="visaInsurancePerPerson" class="form-control" value="250" min="0"  step="50"  oninput="calculateTool()">
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
  0 => 'إجمالي تكلفة الرحلة = تذاكر الطيران + الفنادق + (المصروف اليومي × الأيام × الأفراد) + التأشيرات.',
  1 => 'حجز الطيران والفنادق قبل السفر بشهرين إلى 3 أشهر يوفر ما بين 25% إلى 40% من تكلفة التذاكر والإقامة.',
),
        'يفترض غرفة فندقية مزدوجة مشتركة لكل شخصين.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم تبلغ ميزانية الطوارئ الموصى بها في السفر الدولي؟',
    'a' => 'يُوصى دائماً بتخصيص بطاقة ائتمانية باحتياطي لا يقل عن 20% إلى 25% من ميزانية السفر لمواجهة أي ظروف طبية أو تغيير في مواعيد الطيران.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'travel-expenses-split-calculator',
  1 => 'road-trip-cost-calculator',
  2 => 'cost-of-living-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const people = Math.max(1, parseInt(document.getElementById('travelersCount').value) || 2);
            const days = Math.max(1, parseInt(document.getElementById('tripDaysCount').value) || 7);
            const flight = Math.max(0, parseFloat(document.getElementById('flightTicketPerPerson').value) || 1800);
            const hotel = Math.max(0, parseFloat(document.getElementById('hotelNightCost').value) || 450);
            const daily = Math.max(20, parseFloat(document.getElementById('dailySpendPerPerson').value) || 200);
            const visa = Math.max(0, parseFloat(document.getElementById('visaInsurancePerPerson').value) || 250);
            const curr = getSelectedCurrency();

            // عدد الغرف التقديري: غرفة لكل شخصين
            const rooms = Math.ceil(people / 2);
            const nights = Math.max(1, days - 1);

            const totalFlights = flight * people;
            const totalHotels = hotel * rooms * nights;
            const totalDaily = daily * people * days;
            const totalVisas = visa * people;
            const grandTotal = totalFlights + totalHotels + totalDaily + totalVisas;
            const costPerPerson = grandTotal / people;

            setPrimaryResult(formatMoney(grandTotal, curr), 'الميزانية التقديرية الإجمالية للرحلة');
            showResultArea();

            setDetailStats([
                { label: 'متوسط تكلفة الشخص الواحد', value: formatMoney(costPerPerson, curr), color: '#3b82f6' },
                { label: 'تذاكر الطيران لجميع المسافرين', value: formatMoney(totalFlights, curr), color: '#10b981' },
                { label: 'إجمالي الإقامة والفنادق (' + nights + ' ليالٍ)', value: formatMoney(totalHotels, curr), color: '#f59e0b' },
                { label: 'المصروف اليومي والأنشطة لجميع الأيام', value: formatMoney(totalDaily, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>ميزانية السفر لـ <strong>${people} مسافرين</strong> لمدة <strong>${days} أيام</strong> تقدر بـ <strong>${formatMoney(grandTotal, curr)}</strong> (بمتوسط <strong>${formatMoney(costPerPerson, curr)}</strong> للشخص الواحد) شاملة الطيران والإقامة والمصروف اليومي والتأشيرات.</p>
            `);
        
        saveLastInputs('travel-cost-calculator');
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
    restoreLastInputs('travel-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>