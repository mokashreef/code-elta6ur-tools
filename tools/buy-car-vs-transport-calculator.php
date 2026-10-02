<?php
/**
 * أداة: حاسبة شراء سيارة أم المواصلات وتطبيقات النقل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'buy-car-vs-transport-calculator';
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
        <label class="form-label" for="uberRideCostAvg">متوسط تكلفة المشوار الواحد بتطبيقات النقل (أوبر/كريم/تاكسي)</label>
        <input type="number" id="uberRideCostAvg" class="form-control" value="35" min="5"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ridesPerDayAvg">متوسط عدد المشاوير اليومية</label>
        <input type="number" id="ridesPerDayAvg" class="form-control" value="2" min="1" max="10" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="carTotalMonthlyOwnership">التكلفة الشهرية المتوقعة لشراء وامتلاك سيارة (قسط + وقود + صيانة)</label>
        <input type="number" id="carTotalMonthlyOwnership" class="form-control" value="2200" min="300"  step="100"  oninput="calculateTool()">
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
  0 => 'تطبيقات النقل تعفيك من تكاليف التأمين، فحص المركبة، ركن السيارات، والصيانة، وتوفر عليك التوتر أثناء القيادة في الازدحام.',
  1 => 'السيارة الخاصة تمنح حرية حركة وراحة مطلقة وملاءمة أفضل للعائلات والتنقل المتكرر.',
),
        'المقارنة مالية بحتة دون احتساب الجوانب النفسية والراحة الشخصية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى يكون امتلاك سيارة خياراً حتمياً؟',
    'a' => 'عند وجود أطفال ومسؤوليات مدرسية يومية، أو عندما يتطلب عملك التنقل بين عدة مواقع متباعدة يصعب تغطيتها بالتطبيقات دون تكلفة باهظة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'monthly-car-cost-calculator',
  1 => 'real-car-cost-calculator',
  2 => 'monthly-gas-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rideCost = Math.max(5, parseFloat(document.getElementById('uberRideCostAvg').value) || 35);
            const ridesDay = Math.max(1, parseInt(document.getElementById('ridesPerDayAvg').value) || 2);
            const carMonthly = Math.max(300, parseFloat(document.getElementById('carTotalMonthlyOwnership').value) || 2200);
            const curr = getSelectedCurrency();

            const monthlyTransport = rideCost * ridesDay * 30;
            const diff = carMonthly - monthlyTransport;

            let winner = '';
            let statusColor = '#10b981';
            if (monthlyTransport < carMonthly) {
                winner = 'تطبيقات النقل والمواصلات أوفر بـ ' + formatMoney(Math.abs(diff), curr) + ' شهرياً 🚕';
                statusColor = '#10b981';
            } else {
                winner = 'امتلاك سيارة خاصة أوفر بـ ' + formatMoney(diff, curr) + ' شهرياً 🚗';
                statusColor = '#3b82f6';
            }

            setPrimaryResult(winner, 'القرار المالي الأوفر شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة تطبيقات النقل شهرياً', value: formatMoney(monthlyTransport, curr), color: '#3b82f6' },
                { label: 'تكلفة امتلاك السيارة شهرياً', value: formatMoney(carMonthly, curr), color: '#ef4444' },
                { label: 'الوفر السنوي للخيار الأفضل', value: formatMoney(Math.abs(diff) * 12, curr), color: '#10b981' },
                { label: 'عدد المشاوير الشهرية', value: (ridesDay * 30) + ' مشوار', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بمعدل <strong>${ridesDay} مشاوير يومياً</strong>، تدفع لتطبيقات النقل <strong>${formatMoney(monthlyTransport, curr)} شهرياً</strong>، مقارنة بـ <strong>${formatMoney(carMonthly, curr)}</strong> لامتلاك سيارة. النتيجة: <strong>${winner}</strong>.</p>
            `);
        
        saveLastInputs('buy-car-vs-transport-calculator');
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
    restoreLastInputs('buy-car-vs-transport-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>