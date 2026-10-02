<?php
/**
 * أداة: حاسبة استهلاك الكمبيوتر للكهرباء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'pc-power-consumption-calculator';
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
        <label class="form-label" for="pcType">نوع الكمبيوتر والمواصفات</label>
        <select id="pcType" class="form-control" onchange="calculateTool()">
            <option value="laptop_office" >لابتوب مكتبي أو دراسي خفيف ~ 45 إلى 65 واط</option>
            <option value="laptop_gaming" >لابتوب ألعاب وتصميم قوي ~ 150 إلى 230 واط</option>
            <option value="desktop_office" >كمبيوتر مكتبي PC للأعمال المكتبية ~ 120 واط</option>
            <option value="desktop_gaming_mid" selected>كمبيوتر ألعاب متوسط (RTX 4060) ~ 300 واط</option>
            <option value="desktop_workstation" >كمبيوتر ألعاب ورندر فائق (RTX 4080/4090) ~ 550 إلى 750 واط</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="monitorsCount">عدد الشاشات المتصلة</label>
        <input type="number" id="monitorsCount" class="form-control" value="1" min="0" max="4" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="pcDailyHours">ساعات الاستخدام اليومية</label>
        <input type="number" id="pcDailyHours" class="form-control" value="8" min="1" max="24" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="pcKwhPrice">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="pcKwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'باور سبلاي الكمبيوتر (مثلاً 750W) لا يسحب 750 واط طوال الوقت، بل يسحب فقط ما تطلبه القطع بناءً على ضغط المعالج وكرت الشاشة.',
  1 => 'اللابتوبات تستهلك طاقة أقل بنسبة 70% إلى 80% مقارنة بأجهزة الكمبيوتر المكتبية المماثلة في الأداء.',
),
        'يفترض مزيجاً بين التصفح الخفيف وساعات تشغيل الألعاب أو برامج التصميم.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم واط تستهلك الشاشة في وضع الاستعداد (Sleep Mode)؟',
    'a' => 'في وضع السكون تستهلك الشاشة والكمبيوتر أقل من 1 إلى 2 واط فقط بفضل معايير كفاءة الطاقة الحديثة (Energy Star).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'tv-power-consumption-calculator',
  1 => 'electricity-consumption-calculator',
  2 => 'ups-size-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const type = document.getElementById('pcType').value;
            const monitors = Math.max(0, parseInt(document.getElementById('monitorsCount').value) || 1);
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('pcDailyHours').value) || 8));
            const price = Math.max(0.01, parseFloat(document.getElementById('pcKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            let pcWatts = 300;
            if (type === 'laptop_office') pcWatts = 50;
            if (type === 'laptop_gaming') pcWatts = 180;
            if (type === 'desktop_office') pcWatts = 120;
            if (type === 'desktop_workstation') pcWatts = 650;

            const monitorsWatts = monitors * 35; // الشاشة تستهلك 30-40 واط
            const totalWatts = pcWatts + monitorsWatts;

            const dailyKwh = (totalWatts * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل الكمبيوتر شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري الإجمالي', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'مجموع سحب الطاقة الفعلي', value: totalWatts + ' واط', color: '#f59e0b' },
                { label: 'التكلفة السنوية للتشغيل', value: formatMoney(monthlyCost * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يستهلك جهازك مع ${monitors} شاشة حوالي <strong>${totalWatts} واط</strong> في الساعة. عند تشغيله <strong>${hours} ساعات يومياً</strong>، يستهلك <strong>${monthlyKwh.toFixed(1)} kWh شهرياً</strong> بتكلفة <strong>${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('pc-power-consumption-calculator');
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
    restoreLastInputs('pc-power-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>