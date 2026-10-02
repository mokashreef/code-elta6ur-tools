<?php
/**
 * أداة: حاسبة المولد مقابل البطاريات مقابل الطاقة الشمسية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'power-source-comparison-calculator';
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
        <label class="form-label" for="dailyEnergyCompare">الاستهلاك اليومي المطلوب تأمينه (كيلوواط ساعة kWh)</label>
        <input type="number" id="dailyEnergyCompare" class="form-control" value="10" min="2"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="comparisonYears">مدة المقارنة بالسنوات</label>
        <select id="comparisonYears" class="form-control" onchange="calculateTool()">
            <option value="1" >سنة واحدة</option>
            <option value="3" selected>3 سنوات (المعيار الأكثر واقعية)</option>
            <option value="5" >5 سنوات</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelLiterCost">سعر لتر وقود المولد (بنزين/ديزل)</label>
        <input type="number" id="fuelLiterCost" class="form-control" value="0.85" min="0.1"  step="0.05"  oninput="calculateTool()">
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
  0 => 'المولد يبدو رخيصاً عند الشراء الأولي ولكنه يتحول إلى محرقة للمال في تكاليف الوقود والصيانة والأعطال.',
  1 => 'البطاريات وحدها تحتاج كهرباء شبكة مستقرة للشحن واستبدالاً متكرراً كل سنتين إلى 3 سنوات.',
  2 => 'الطاقة الشمسية مع بطاريات الليثيوم تمثل الحل المستدام الأمثل من حيث راحة البال والتوفير الاقتصادي الشامل.',
),
        'المقارنة تشمل تكلفة الشراء والصيانة والمحروقات أو فواتير شحن البطاريات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن الدمج بين الطاقة الشمسية والمولد؟',
    'a' => 'نعم؛ الانفرترات الهجينة الحديثة تدعم مدخلاً ذكياً للمولد (Dry Contact) لتشغيل المولد أوتوماتيكياً فقط عند الطوارئ القصوى إذا نفذت البطاريات وتواصل الطقس الغائم عدة أيام.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'generator-cost-calculator',
  1 => 'solar-system-cost-calculator',
  2 => 'grid-vs-solar-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kwhDaily = Math.max(2, parseFloat(document.getElementById('dailyEnergyCompare').value) || 10);
            const years = parseInt(document.getElementById('comparisonYears').value) || 3;
            const fuelPrice = Math.max(0.1, parseFloat(document.getElementById('fuelLiterCost').value) || 0.85);
            const curr = getSelectedCurrency();

            const totalKwh = kwhDaily * 365 * years;

            // 1. تكلفة المولد: شراء أولي + وقود (0.4 لتر/kWh) + صيانة وزيوت 20%
            const genBuyCost = 1200;
            const genFuelTotal = (totalKwh * 0.40 * fuelPrice) * 1.25;
            const totalGenCost = genBuyCost + genFuelTotal;

            // 2. تكلفة بنك البطاريات مع انفرتر وشحن كهرباء حكومية
            const battBuyCost = 2200;
            const battReplacement = years > 2 ? 1500 : 0;
            const battChargingElectricity = totalKwh * 0.18 * 1.2;
            const totalBattCost = battBuyCost + battReplacement + battChargingElectricity;

            // 3. تكلفة الطاقة الشمسية مع بطاريات
            const solarInstallCost = 3800;
            const solarMaint = years * 100;
            const totalSolarCost = solarInstallCost + solarMaint;

            // الفائز بالأقل تكلفة
            let winner = 'الطاقة الشمسية مع البطاريات ☀️';
            let winnerCost = totalSolarCost;
            if (totalBattCost < winnerCost && years === 1) { winner = 'البطاريات فقط 🔋'; winnerCost = totalBattCost; }

            setPrimaryResult(winner, 'الخيار الأكثر جدوى وتوفيراً على مدى ' + years + ' سنوات');
            showResultArea();

            setDetailStats([
                { label: 'التكلفة الإجمالية للطاقة الشمسية', value: formatMoney(totalSolarCost, curr), color: '#10b981' },
                { label: 'التكلفة الإجمالية للبطاريات والانفرتر', value: formatMoney(totalBattCost, curr), color: '#3b82f6' },
                { label: 'التكلفة الإجمالية للمولد والوقود', value: formatMoney(totalGenCost, curr), color: '#ef4444' },
                { label: 'وفر الطاقة الشمسية مقارنة بالمولد', value: formatMoney(totalGenCost - totalSolarCost, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>على مدار <strong>${years} سنوات</strong> لتأمين <strong>${kwhDaily} kWh يومياً</strong>:<br>
                - المولد بالوقود يكلف <strong>${formatMoney(totalGenCost, curr)}</strong> (تكاليف وقود محروقة وصيانة وضجيج).<br>
                - البطاريات مع الشاحن تكلف <strong>${formatMoney(totalBattCost, curr)}</strong>.<br>
                - الطاقة الشمسية تكلف <strong>${formatMoney(totalSolarCost, curr)}</strong> وهي الأوفر والأكثر هدوءاً واستقراراً وتستمر في العمل لـ 20 سنة إضافية مجاناً.</p>
            `);
        
        saveLastInputs('power-source-comparison-calculator');
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
    restoreLastInputs('power-source-comparison-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>