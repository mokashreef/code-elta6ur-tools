<?php
/**
 * أداة: حاسبة فترة استرداد الطاقة الشمسية (ROI)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-payback-calculator';
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
        <label class="form-label" for="solarTotalCost">تكلفة المنظومة الشمسية الإجمالية</label>
        <input type="number" id="solarTotalCost" class="form-control" value="25000" min="1000"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="monthlyBillSavings">التوفير الشهري المتوقع في فاتورة الكهرباء</label>
        <input type="number" id="monthlyBillSavings" class="form-control" value="650" min="50"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tariffInflationRate">الزيادة السنوية المتوقعة في أسعار الكهرباء الحكومية (%)</label>
        <input type="number" id="tariffInflationRate" class="form-control" value="3" min="0" max="15" step="1"  oninput="calculateTool()">
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
  0 => 'فترة الاسترداد = تكلفة المنظومة ÷ التوفير السنوي في الفاتورة.',
  1 => 'عائد استثمار الطاقة الشمسية (15% إلى 25% سنوياً) يتفوق على معظم عوائد الودائع البنكية وصناديق الاستثمار التقليدية.',
),
        'الألواح تعمل بكفاءة تفوق 80% حتى بعد مرور 25 سنة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يحدث بعد انتهاء فترة استرداد تكلفة المنظومة؟',
    'a' => 'تصبح الكهرباء المولدة مجانية بالكامل بنسبة 100%، وتستمر الألواح في إنتاج الطاقة لعشرين سنة إضافية دون أي تكاليف سوى الصيانة البسيطة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-system-cost-calculator',
  1 => 'grid-vs-solar-calculator',
  2 => 'solar-yield-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cost = Math.max(1000, parseFloat(document.getElementById('solarTotalCost').value) || 25000);
            const savings = Math.max(50, parseFloat(document.getElementById('monthlyBillSavings').value) || 650);
            const inflation = Math.max(0, parseFloat(document.getElementById('tariffInflationRate').value) || 3) / 100;
            const curr = getSelectedCurrency();

            const annualSavings = savings * 12;
            const paybackYears = cost / annualSavings;
            const years25TotalSavings = annualSavings * 25 * (1 + inflation);
            const netProfit25Years = years25TotalSavings - cost;

            setPrimaryResult(paybackYears.toFixed(1) + ' سنوات استرداد', 'فترة استرداد رأس مال المنظومة');
            showResultArea();

            setDetailStats([
                { label: 'التوفير السنوي في السنة الأولى', value: formatMoney(annualSavings, curr), color: '#3b82f6' },
                { label: 'صافي الوفر المالي على مدار 25 سنة', value: formatMoney(netProfit25Years, curr), color: '#10b981' },
                { label: 'العائد السنوي على الاستثمار (ROI)', value: ((annualSavings / cost) * 100).toFixed(1) + '% سنوياً', color: '#f59e0b' },
                { label: 'تاريخ بداية الأرباح المجانية', value: 'بعد ' + Math.ceil(paybackYears) + ' سنوات', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تسترد كامل تكلفة المنظومة البالغة <strong>${formatMoney(cost, curr)}</strong> خلال <strong>${paybackYears.toFixed(1)} سنوات</strong>، وتحقق بعدها كهرباء مجانية بالكامل وصافي وفر تراكمي يتجاوز <strong>${formatMoney(netProfit25Years, curr)}</strong> على مدار العمر الافتراضي للمنظومة (25 سنة).</p>
            `);
        
        saveLastInputs('solar-payback-calculator');
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
    restoreLastInputs('solar-payback-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>