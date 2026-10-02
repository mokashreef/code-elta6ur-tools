<?php
/**
 * أداة: حاسبة توصيل البطاريات تسلسلي / توازي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'battery-wiring-calculator';
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
        <label class="form-label" for="individualVolt">جهد البطارية الفردية (فولت V) - عادة 12V</label>
        <input type="number" id="individualVolt" class="form-control" value="12" min="2" max="48" step="2"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="individualAh">سعة البطارية الفردية (أمبير-ساعة Ah)</label>
        <input type="number" id="individualAh" class="form-control" value="150" min="10"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="batteriesInSeries">عدد البطاريات الموصلة على التوالي (Series)</label>
        <input type="number" id="batteriesInSeries" class="form-control" value="2" min="1" max="8" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="parallelStrings">عدد السلاسل الموصلة على التوازي (Parallel Strings)</label>
        <input type="number" id="parallelStrings" class="form-control" value="2" min="1" max="8" step="1"  oninput="calculateTool()">
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
  0 => 'التوصيل على التوالي (Series: موجب بسالب): يجمع الفولتية وتبقى السعة (Ah) ثابتة.',
  1 => 'التوصيل على التوازي (Parallel: موجب بموجب وسالب بسالب): تبقى الفولتية ثابتة وتُجمع السعة (Ah).',
  2 => 'التوصيل المركب (توالي وتوازي معاً): يرفع الجهد والسعة في نفس الوقت لتغذية محولات الطاقة الكبيرة.',
),
        'يجب أن تكون جميع البطاريات المربوطة من نفس النوع والماركة والسعة والعمر الافتراضي تماماً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو خطر توصيل بطاريات بسعات أو أعمار مختلفة معاً؟',
    'a' => 'البطارية الأضعف أو الأقدم ستفرغ أسرع وتسحب شحنة البطاريات الأقوى، مما يتسبب في تلف المنظومة بالكامل وحدوث حرارة وسخونة مفرطة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'battery-count-calculator',
  1 => 'battery-charging-time-calculator',
  2 => 'inverter-size-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const v = Math.max(2, parseFloat(document.getElementById('individualVolt').value) || 12);
            const ah = Math.max(10, parseFloat(document.getElementById('individualAh').value) || 150);
            const series = Math.max(1, parseInt(document.getElementById('batteriesInSeries').value) || 2);
            const parallel = Math.max(1, parseInt(document.getElementById('parallelStrings').value) || 2);

            const totalBatteries = series * parallel;
            const finalVoltage = v * series;
            const finalAh = ah * parallel;
            const totalWh = finalVoltage * finalAh;
            const totalKwh = totalWh / 1000;

            setPrimaryResult(finalVoltage + 'V @ ' + finalAh + 'Ah (' + totalKwh.toFixed(2) + ' kWh)', 'المواصفات الإجمالية لبنك البطاريات');
            showResultArea();

            setDetailStats([
                { label: 'الجهد الإجمالي الناتج (Voltage)', value: finalVoltage + ' فولت V', color: '#3b82f6' },
                { label: 'السعة الإجمالية الناتجة (Capacity)', value: finalAh + ' أمبير-ساعة Ah', color: '#10b981' },
                { label: 'إجمالي عدد البطاريات المستخدمة', value: totalBatteries + ' بطاريات', color: '#f59e0b' },
                { label: 'إجمالي الطاقة المخزنة بالكامل', value: totalKwh.toFixed(2) + ' kWh (' + totalWh + ' Wh)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بتوصيل <strong>${totalBatteries} بطاريات</strong> (${series} توالي × ${parallel} توازي)، يرتفع الجهد إلى <strong>${finalVoltage} فولت</strong> والسعة إلى <strong>${finalAh} Ah</strong>، ما يوفر طاقة تخزينية إجمالية قدرها <strong>${totalKwh.toFixed(2)} كيلوواط ساعة</strong>.</p>
            `);
        
        saveLastInputs('battery-wiring-calculator');
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
    restoreLastInputs('battery-wiring-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>