<?php
/**
 * أداة: حاسبة الكهرباء الحكومية مقابل الطاقة الشمسية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grid-vs-solar-calculator';
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
        <label class="form-label" for="currentMonthlyBill">فاتورة الكهرباء الحكومية الحالية شهرياً</label>
        <input type="number" id="currentMonthlyBill" class="form-control" value="700" min="50"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="solarSystemInstallCost">تكلفة تركيب المنظومة الشمسية</label>
        <input type="number" id="solarSystemInstallCost" class="form-control" value="26000" min="1000"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="yearsHorizon">فترة المقارنة الزمنية (بالسنوات)</label>
        <select id="yearsHorizon" class="form-control" onchange="calculateTool()">
            <option value="10" >10 سنوات</option>
            <option value="15" >15 سنة</option>
            <option value="20" selected>20 سنة (المعيار طويل الأمد)</option>
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
  0 => 'الكهرباء الحكومية عبارة عن مصروف مستمر متصاعد مدى الحياة لا يبني أي أصل مالي للمستهلك.',
  1 => 'الطاقة الشمسية تحول الفاتورة الشهرية إلى استثمار في أصل إنتاجي مملوك بالكامل يرفع القيمة العقارية لمنزلك.',
),
        'يفترض منظومة تغطي 85% إلى 95% من استهلاك المنزل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل ترتفع قيمة العقار عند تركيب طاقة شمسية؟',
    'a' => 'نعم؛ تؤكد الدراسات العقارية أن المنازل المزودة بمنظومات طاقة شمسية موثقة تُباع أسرع وبسعر أعلى بنسبة 4% إلى 6% بسبب انخفاض تكاليف تشغيلها السنوية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-payback-calculator',
  1 => 'solar-system-cost-calculator',
  2 => 'electricity-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const bill = Math.max(50, parseFloat(document.getElementById('currentMonthlyBill').value) || 700);
            const solarCost = Math.max(1000, parseFloat(document.getElementById('solarSystemInstallCost').value) || 26000);
            const years = parseInt(document.getElementById('yearsHorizon').value) || 20;
            const curr = getSelectedCurrency();

            // بافتراض زيادة طفيفة سنوية في تعرفة الشبكة 2%
            let gridTotal = 0;
            let currentYearBill = bill * 12;
            for (let i = 0; i < years; i++) {
                gridTotal += currentYearBill;
                currentYearBill *= 1.02;
            }

            // صيانة شمسية وتغيير انفرتر مرة كل 10 سنوات
            const solarMaintenance = (years / 10) * 1500;
            const totalSolarExpenditure = solarCost + solarMaintenance;
            const totalSavings = gridTotal - totalSolarExpenditure;

            setPrimaryResult(formatMoney(totalSavings, curr), 'صافي الوفر المالي مع الطاقة الشمسية');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي ما ستدفعه لشركة الكهرباء (' + years + ' سنة)', value: formatMoney(gridTotal, curr), color: '#ef4444' },
                { label: 'إجمالي تكاليف الطاقة الشمسية والصيانة', value: formatMoney(totalSolarExpenditure, curr), color: '#10b981' },
                { label: 'نسبة التوفير المالي الإجمالية', value: ((totalSavings / gridTotal) * 100).toFixed(0) + '% وفر', color: '#3b82f6' },
                { label: 'المتوسط الشهري للوفر المالي', value: formatMoney(totalSavings / (years * 12), curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>خلال <strong>${years} سنة</strong>، ستدفع لشركة الكهرباء حوالي <strong>${formatMoney(gridTotal, curr)}</strong>، بينما تكلفك الطاقة الشمسية <strong>${formatMoney(totalSolarExpenditure, curr)}</strong> فقط شاملة الصيانة، محققاً وفراً هائلاً قدره <strong>${formatMoney(totalSavings, curr)}</strong>.</p>
            `);
        
        saveLastInputs('grid-vs-solar-calculator');
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
    restoreLastInputs('grid-vs-solar-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>