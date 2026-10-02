<?php
/**
 * أداة: حاسبة استهلاك الكهرباء الشاملة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'electricity-consumption-calculator';
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
        <label class="form-label" for="deviceWatt">قدرة الجهاز الإجمالية (بالواط Watt)</label>
        <input type="number" id="deviceWatt" class="form-control" value="1500" min="1"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="dailyHours">ساعات التشغيل اليومية</label>
        <input type="number" id="dailyHours" class="form-control" value="6" min="0.1" max="24" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="devicesCount">عدد الأجهزة المماثلة</label>
        <input type="number" id="devicesCount" class="form-control" value="1" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="kwhPrice">سعر الكيلوواط ساعة (kWh) في منطقتك</label>
        <input type="number" id="kwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'الاستهلاك بالكيلوواط ساعة (kWh) = (القدرة بالواط × ساعات التشغيل) ÷ 1000.',
  1 => 'التكلفة الشهرية = الاستهلاك الشهري بالكيلوواط ساعة × سعر الكيلوواط في شريحتك.',
  2 => 'سعر الكيلوواط ساعة يختلف حسب شرائح الاستهلاك السكني في كل دولة.',
),
        'سعر الكيلوواط الافتراضي 0.18 ر.س في السعودية للشريحة الأولى (حتى 6000 كيلوواط). يمكنك تغييره حسب فاتورتك.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أعرف قدرة جهازي بالواط؟',
    'a' => 'تجد ملصقاً خلف الجهاز أو أسفله مكتوباً عليه القوة برمز (W) مثل 2000W، أو مكتوباً عليه الفولتية (220V) والتيار بالأمبير (A)، وحاصل ضربهما يعطي الواط.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ac-consumption-calculator',
  1 => 'fridge-consumption-calculator',
  2 => 'device-monthly-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const watt = Math.max(1, parseFloat(document.getElementById('deviceWatt').value) || 1500);
            const hours = Math.max(0.1, Math.min(24, parseFloat(document.getElementById('dailyHours').value) || 6));
            const count = Math.max(1, parseInt(document.getElementById('devicesCount').value) || 1);
            const price = Math.max(0.01, parseFloat(document.getElementById('kwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            const dailyKwh = (watt * hours * count) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;
            const yearlyCost = monthlyCost * 12;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة الاستهلاك الشهري');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك اليومي بالكيلوواط ساعة', value: dailyKwh.toFixed(2) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك الشهري (30 يوماً)', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'التكلفة السنوية التقديرية', value: formatMoney(yearlyCost, curr), color: '#8b5cf6' },
                { label: 'سعر الكيلوواط المعتمد', value: formatMoney(price, curr) + ' / kWh', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تشغيل ${count > 1 ? count + ' أجهزة' : 'جهاز'} بقدرة إجمالية <strong>${watt * count} واط</strong> لمدة <strong>${hours} ساعات يومياً</strong> يستهلك <strong>${monthlyKwh.toFixed(1)} كيلوواط ساعة شهرياً</strong> وتكلفته <strong>${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('electricity-consumption-calculator');
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
    restoreLastInputs('electricity-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>