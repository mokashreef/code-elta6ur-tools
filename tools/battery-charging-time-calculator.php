<?php
/**
 * أداة: حاسبة مدة شحن البطارية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'battery-charging-time-calculator';
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
    <div class="form-group">
        <label class="form-label" for="chargeBatteryAh">سعة البطارية (أمبير-ساعة Ah)</label>
        <input type="number" id="chargeBatteryAh" class="form-control" value="150" min="7"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="chargerCurrentAmps">تيار الشاحن أو منظم الشحن الشمسي (أمبير A)</label>
        <input type="number" id="chargerCurrentAmps" class="form-control" value="20" min="1" max="150" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="remainingChargePercent">نسبة الشحن الحالية في البطارية (%)</label>
        <input type="number" id="remainingChargePercent" class="form-control" value="30" min="0" max="95" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="chargingEfficiency">كفاءة الشحن (عادة 80% للرصاص و 95% لليثيوم)</label>
        <input type="number" id="chargingEfficiency" class="form-control" value="85" min="70" max="98" step="1"  oninput="calculateTool()">
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
  0 => 'التيار المثالي لشحن بطاريات الجيل والرصاص هو 10% إلى 15% من سعتها (قاعدة C/10).',
  1 => 'الشحن بتيار مرتفع جداً يقلل وقت الشحن ولكنه يرفع حرارة البطارية ويتلف ألواح الرصاص الداخلية.',
),
        'يفترض شاحناً ذكياً متعدد المراحل (Bulk, Absorption, Float).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن شحن بطارية 100 Ah بشاحن 40 أمبير؟',
    'a' => 'لا يُنصح بذلك لبطاريات الرصاص لأن تيار 40A يمثل 40% من السعة وهو تيار مفرط يتلفها، بينما بطاريات الليثيوم تقبل تيارات شحن عالية تصل إلى 0.5C بأمان تام.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ups-runtime-calculator',
  1 => 'battery-count-calculator',
  2 => 'solar-batteries-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const ah = Math.max(7, parseFloat(document.getElementById('chargeBatteryAh').value) || 150);
            const amps = Math.max(1, parseFloat(document.getElementById('chargerCurrentAmps').value) || 20);
            const remaining = Math.max(0, Math.min(95, parseFloat(document.getElementById('remainingChargePercent').value) || 30)) / 100;
            const eff = Math.max(70, Math.min(98, parseFloat(document.getElementById('chargingEfficiency').value) || 85)) / 100;

            const neededAh = ah * (1 - remaining);
            // وقت الشحن = الأمبير-ساعة المطلوبة / (تيار الشحن * الكفاءة)
            const chargeHours = neededAh / (amps * eff);
            const hoursInt = Math.floor(chargeHours);
            const minutesInt = Math.round((chargeHours - hoursInt) * 60);

            // تيار الشحن الموصى به لسلامة البطارية (0.1C إلى 0.2C)
            const minSafeAmps = ah * 0.10;
            const maxSafeAmps = ah * 0.20;

            setPrimaryResult(hoursInt + ' ساعات و ' + minutesInt + ' دقيقة', 'الوقت المتوقع لاكتمال الشحن 100%');
            showResultArea();

            setDetailStats([
                { label: 'الأمبير المطلوب تعويضه', value: neededAh.toFixed(1) + ' Ah', color: '#3b82f6' },
                { label: 'التيار الآمن الموصى به للبطارية (0.1C-0.2C)', value: minSafeAmps.toFixed(0) + ' إلى ' + maxSafeAmps.toFixed(0) + ' أمبير', color: '#10b981' },
                { label: 'نسبة الشحن المفقودة المعوضة', value: ((1 - remaining) * 100).toFixed(0) + '%', color: '#f59e0b' },
                { label: 'إجمالي الساعات العشرية', value: chargeHours.toFixed(1) + ' ساعة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لشحن بطارية سعة <strong>${ah} Ah</strong> من نسبة <strong>${(remaining*100).toFixed(0)}%</strong> حتى الامتلاء بتيار <strong>${amps} أمبير</strong>، يستغرق الشحن حوالي <strong>${hoursInt} ساعات و ${minutesInt} دقيقة</strong>.</p>
            `);
        
        saveLastInputs('battery-charging-time-calculator');
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
    restoreLastInputs('battery-charging-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>