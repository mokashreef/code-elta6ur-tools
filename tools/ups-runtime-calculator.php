<?php
/**
 * أداة: حاسبة مدة تشغيل UPS والبطاريات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ups-runtime-calculator';
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
        <label class="form-label" for="batteryAh">سعة البطارية الإجمالية (أمبير-ساعة Ah)</label>
        <input type="number" id="batteryAh" class="form-control" value="100" min="7"  step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="systemVoltage">جهد بنك البطاريات (فولت V)</label>
        <select id="systemVoltage" class="form-control" onchange="calculateTool()">
            <option value="12" selected>12 فولت (بطارية واحدة)</option>
            <option value="24" >24 فولت (بطاريتين على التوالي)</option>
            <option value="48" >48 فولت (4 بطاريات على التوالي)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="connectedLoadWatts">الحمل الفعلي المتصل (بالواط Watt)</label>
        <input type="number" id="connectedLoadWatts" class="form-control" value="250" min="10"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="inverterEfficiency">كفاءة محول الـ UPS (%)- المعتاد 85%</label>
        <input type="number" id="inverterEfficiency" class="form-control" value="85" min="70" max="95" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="batteryTypeDod">نوع البطارية وعمق التفريغ المسموح (DoD)</label>
        <select id="batteryTypeDod" class="form-control" onchange="calculateTool()">
            <option value="lead_50" selected>بطارية رصاص / جيل / AGM عادية (تفريغ 50% لحمايتها)</option>
            <option value="lead_70" >بطارية جيل ديب سايكل عالية الجودة (تفريغ 70%)</option>
            <option value="lithium_90" >بطارية ليثيوم LiFePO4 حديثة (تفريغ 90%)</option>
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
  0 => 'الطاقة الكلية للبطارية (واط-ساعة Wh) = سعة البطارية (Ah) × جهد البطارية (V).',
  1 => 'مدة التشغيل = (الطاقة الكلية × نسبة عمق التفريغ DoD × كفاءة الانفرتر) ÷ قدرة الحمل بالواط.',
  2 => 'تفريغ بطاريات الرصاص لأكثر من 50% يقلل عدد دورات حياتها بشكل حاد ويؤدي إلى تلفها السريع.',
),
        'يفترض بطارية جديدة بحالة ممتازة وكفاءة تحويل 85% لدارات الـ UPS.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تدوم بطاريات الليثيوم مدة أطول في التشغيل؟',
    'a' => 'لأن بطاريات الليثيوم (LiFePO4) تسمح بتفريغ 90% من طاقتها بأمان دون ضرر، بينما بطاريات الجيل والرصاص تفرغ 50% فقط، إضافة إلى عدم تأثر الليثيوم بانخفاض الجهد السريع (Peukert Effect).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ups-size-calculator',
  1 => 'battery-count-calculator',
  2 => 'battery-charging-time-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const ah = Math.max(7, parseFloat(document.getElementById('batteryAh').value) || 100);
            const volt = parseFloat(document.getElementById('systemVoltage').value) || 12;
            const load = Math.max(10, parseFloat(document.getElementById('connectedLoadWatts').value) || 250);
            const eff = Math.max(70, parseFloat(document.getElementById('inverterEfficiency').value) || 85) / 100;
            const dodType = document.getElementById('batteryTypeDod').value;

            let dod = 0.50;
            if (dodType === 'lead_70') dod = 0.70;
            if (dodType === 'lithium_90') dod = 0.90;

            const totalWattHours = ah * volt;
            const usableWattHours = totalWattHours * dod * eff;
            const runtimeHours = usableWattHours / load;

            const hoursInt = Math.floor(runtimeHours);
            const minutesInt = Math.round((runtimeHours - hoursInt) * 60);

            let timeStr = '';
            if (hoursInt > 0) timeStr += hoursInt + ' ساعة ';
            if (minutesInt > 0) timeStr += 'و ' + minutesInt + ' دقيقة';
            if (timeStr === '') timeStr = 'أقل من دقيقة';

            setPrimaryResult(timeStr, 'مدة التشغيل المتوقعة للأجهزة');
            showResultArea();

            setDetailStats([
                { label: 'سعة التخزين الكلية للبطارية', value: totalWattHours + ' واط-ساعة (Wh)', color: '#3b82f6' },
                { label: 'الطاقة الفعلية القابلة للاستخدام', value: Math.round(usableWattHours) + ' Wh', color: '#10b981' },
                { label: 'سحب التيار من البطارية', value: ((load / eff) / volt).toFixed(1) + ' أمبير DC', color: '#f59e0b' },
                { label: 'عمق التفريغ المعتمد (DoD)', value: (dod * 100) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بنك بطاريات بسعة <strong>${ah} Ah</strong> عند جهد <strong>${volt}V</strong>، يشغل حملاً قدره <strong>${load} واط</strong> لمدة <strong>${timeStr}</strong> متواصلة مع الحفاظ على عمر البطارية من التلف.</p>
            `);
        
        saveLastInputs('ups-runtime-calculator');
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
    restoreLastInputs('ups-runtime-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>