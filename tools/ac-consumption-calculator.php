<?php
/**
 * أداة: حاسبة استهلاك المكيف للكهرباء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ac-consumption-calculator';
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
        <label class="form-label" for="acCapacity">سعة المكيف التبريدية</label>
        <select id="acCapacity" class="form-control" onchange="calculateTool()">
            <option value="12000" >1 طن (12,000 وحدة BTU) - للغرف الصغيرة حتى 14 م²</option>
            <option value="18000" selected>1.5 طن (18,000 وحدة BTU) - للغرف المتوسطة حتى 22 م²</option>
            <option value="24000" >2 طن (24,000 وحدة BTU) - للصالات والغرف الكبيرة حتى 32 م²</option>
            <option value="30000" >2.5 طن (30,000 وحدة BTU) - للمجالس والصالات المفتوحة</option>
            <option value="36000" >3 طن (36,000 وحدة BTU) - وحدات دولابية أو كونسيلد كبيرة</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="acTechnology">تقنية التكييف</label>
        <select id="acTechnology" class="form-control" onchange="calculateTool()">
            <option value="inverter" selected>إنفرتر موفر للطاقة (Inverter) - يوفر 35% إلى 50%</option>
            <option value="conventional" >عادي بدون إنفرتر (نظام On/Off التقليدي)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="acDailyHours">متوسط ساعات التشغيل يومياً</label>
        <input type="number" id="acDailyHours" class="form-control" value="10" min="1" max="24" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="acKwhPrice">سعر الكيلوواط ساعة (kWh)</label>
        <input type="number" id="acKwhPrice" class="form-control" value="0.18" min="0.01"  step="0.01"  oninput="calculateTool()">
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
  0 => 'المكيف يمثل ما بين 60% إلى 70% من فاتورة الكهرباء المنزلية في أشهر الصيف.',
  1 => 'ضبط درجة الحرارة على 24° مئوية بدلاً من 18° يوفر حوالي 25% من استهلاك المكيف للطاقة دون المساس بالراحة.',
),
        'يفترض غرفة معزولة حرارياً بأبواب ونوافذ محكمة الإغلاق.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل مكيف الإنفرتر يستحق فارق السعر عند الشراء؟',
    'a' => 'نعم بكل تأكيد؛ فإذا كنت تشغل المكيف أكثر من 8 ساعات يومياً في الصيف، فإن الوفر في فاتورة الكهرباء يسترد فارق سعر جهاز الإنفرتر خلال سنة ونصف إلى سنتين فقط.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'electricity-consumption-calculator',
  1 => 'device-monthly-cost-calculator',
  2 => 'solar-panels-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const btu = parseInt(document.getElementById('acCapacity').value) || 18000;
            const tech = document.getElementById('acTechnology').value;
            const hours = Math.max(1, Math.min(24, parseFloat(document.getElementById('acDailyHours').value) || 10));
            const price = Math.max(0.01, parseFloat(document.getElementById('acKwhPrice').value) || 0.18);
            const curr = getSelectedCurrency();

            // القدرة القصوى بالواط تقريباً: 18000 btu ~ 1600-1800 واط
            let fullPowerWatts = (btu / 12000) * 1200;
            // متوسط معامل التحميل الفعلي (الكمبروسر يفصل بعد الوصول للحرارة)
            let loadFactor = tech === 'inverter' ? 0.55 : 0.75;
            const actualHourlyWatt = fullPowerWatts * loadFactor;

            const dailyKwh = (actualHourlyWatt * hours) / 1000;
            const monthlyKwh = dailyKwh * 30;
            const monthlyCost = monthlyKwh * price;

            setPrimaryResult(formatMoney(monthlyCost, curr) + ' شهرياً', 'تكلفة تشغيل المكيف في الشهر');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك الشهري للكهرباء', value: monthlyKwh.toFixed(1) + ' kWh', color: '#3b82f6' },
                { label: 'الاستهلاك اليومي', value: dailyKwh.toFixed(2) + ' kWh', color: '#10b981' },
                { label: 'متوسط سحب الطاقة أثناء التشغيل', value: actualHourlyWatt.toFixed(0) + ' واط', color: '#f59e0b' },
                { label: 'التكلفة لموسم الصيف (5 أشهر)', value: formatMoney(monthlyCost * 5, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مكيف بسعة <strong>${btu.toLocaleString()} BTU</strong> بتقنية <strong>${tech === 'inverter' ? 'إنفرتر موفر' : 'تقليدي'}</strong> يعمل <strong>${hours} ساعات يومياً</strong>، يستهلك حوالي <strong>${monthlyKwh.toFixed(1)} kWh شهرياً</strong> وتكلفته <strong>${formatMoney(monthlyCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('ac-consumption-calculator');
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
    restoreLastInputs('ac-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>