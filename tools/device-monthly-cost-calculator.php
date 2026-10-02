<?php
/**
 * أداة: حاسبة تكلفة تشغيل جهاز شهرياً
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'device-monthly-cost-calculator';
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
        <label class="form-label" for="inputWatts">استهلاك الجهاز بالواط (Watt)</label>
        <input type="number" id="inputWatts" class="form-control" value="2000" min="1"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="inputHoursDay">ساعات التشغيل اليومية</label>
        <input type="number" id="inputHoursDay" class="form-control" value="4" min="0.1" max="24" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="inputTariff">سعر الكيلوواط ساعة في الشريحة (kWh)</label>
        <input type="number" id="inputTariff" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'تكلفة الساعة = (الواط ÷ 1000) × سعر الكيلوواط.',
  1 => 'التكلفة الشهرية = تكلفة الساعة × عدد الساعات اليومية × 30 يوماً.',
),
        'يفترض تشغيل مستمر بثبات للحمل المكتوب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي أكثر الأجهزة استهلاكاً للكهرباء في المنزل؟',
    'a' => 'المكيفات، سخانات المياه، أفران الكهرباء، والمكواة، ومجففات الملابس لأنها تعتمد على عناصر تسخين أو ضواغط تستهلك ما بين 1500 إلى 3000 واط.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'electricity-consumption-calculator',
  1 => 'ac-consumption-calculator',
  2 => 'water-heater-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const watts = Math.max(1, parseFloat(document.getElementById('inputWatts').value) || 2000);
            const hours = Math.max(0.1, Math.min(24, parseFloat(document.getElementById('inputHoursDay').value) || 4));
            const tariff = Math.max(0.01, parseFloat(document.getElementById('inputTariff').value) || 0.18);
            const curr = getSelectedCurrency();

            const dailyKwh = (watts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * tariff;
            const hourlyCost = (watts / 1000) * tariff;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الجهاز في الشهر');
            showResultArea();

            setDetailStats([
                { label: 'تكلفة تشغيل الجهاز في الساعة الواحدة', value: formatMoney(hourlyCost, curr), color: '#3b82f6' },
                { label: 'الاستهلاك الشهري (kWh)', value: monthlyKwh.toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'الاستهلاك اليومي (kWh)', value: dailyKwh.toFixed(2) + ' kWh', color: '#f59e0b' },
                { label: 'التكلفة السنوية للجهاز', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>كل ساعة تشغيل لهذا الجهاز تكلفك <strong>${formatMoney(hourlyCost, curr)}</strong>. إجمالي الفاتورة الشهرية الخاصة به <strong>${formatMoney(monthlyCost, curr)}</strong> بناءً على <strong>${hours} ساعات يومياً</strong>.</p>
            `);
        
        saveLastInputs('device-monthly-cost-calculator');
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
    restoreLastInputs('device-monthly-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>