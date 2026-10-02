<?php
/**
 * أداة: حاسبة تكلفة تشطيب منزل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'house-finishing-cost-calculator';
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
        <label class="form-label" for="builtArea">إجمالي مسطح البناء المراد تشطيبه (م²)</label>
        <input type="number" id="builtArea" class="form-control" value="250" min="20"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="finishQuality">مستوى جودة التشطيب</label>
        <select id="finishQuality" class="form-control" onchange="calculateTool()">
            <option value="economy" >اقتصادي (خامات قياسية مناسبة للإيجار)</option>
            <option value="medium" selected>متوسط / سوبر ديلوكس (جودة ممتازة وسيراميك فرز أول)</option>
            <option value="vip" >فاخر جداً / ألترا VIP (رخام، جبس بورد معلق، سمارت هوم)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="airConditioning">نوع التكييف المطلوب</label>
        <select id="airConditioning" class="form-control" onchange="calculateTool()">
            <option value="split" selected>مكيفات سبليت جدارية عادية</option>
            <option value="concealed" >تكييف كونسيلد مخفي (Concealed Ducted)</option>
            <option value="central" >تكييف مركزي متكامل (Package)</option>
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
  0 => 'السباكة والكهرباء تمثل حوالي 25% إلى 30% من تكلفة التشطيب.',
  1 => 'الأرضيات (سيراميك/بورسلان/رخام) والدهانات تمثل حوالي 40% إلى 45%.',
  2 => 'الأبواب والنوافذ والتكييف والإنارات تمثل النسبة المتبقية.',
),
        'التشطيب يفترض استلام المبنى عظم بلياسة أو بدونها.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أين تكمن أكبر بنود هدر الميزانية في التشطيب؟',
    'a' => 'في التعديل على مخططات السباكة والكهرباء بعد تأسيسها، واختيار بورسلان مستورد ذي مقاسات غير قياسية ينتج عنها هالك قص كبير.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'house-building-cost-calculator',
  1 => 'floor-tiles-calculator',
  2 => 'paint-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(20, parseFloat(document.getElementById('builtArea').value) || 250);
            const quality = document.getElementById('finishQuality').value;
            const ac = document.getElementById('airConditioning').value;
            const curr = getSelectedCurrency();

            let baseMeter = 600;
            if (quality === 'medium') baseMeter = 950;
            if (quality === 'vip') baseMeter = 1600;

            let acExtraPerMeter = 50;
            if (ac === 'concealed') acExtraPerMeter = 120;
            if (ac === 'central') acExtraPerMeter = 200;

            const totalMeterRate = baseMeter + acExtraPerMeter;
            const total = area * totalMeterRate;

            setPrimaryResult(formatMoney(total, curr), 'إجمالي ميزانية التشطيب المتوقعة');
            showResultArea();

            setDetailStats([
                { label: 'سعر متر التشطيب شامل التكييف', value: formatMoney(totalMeterRate, curr) + ' / م²', color: '#3b82f6' },
                { label: 'بند التأسيس والكهرباء والسباكة (30%)', value: formatMoney(total * 0.3, curr), color: '#f59e0b' },
                { label: 'بند الأرضيات والسيراميك والدهانات (45%)', value: formatMoney(total * 0.45, curr), color: '#10b981' },
                { label: 'بند الأبواب والنوافذ والإنارة (25%)', value: formatMoney(total * 0.25, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تشطيب مسطح <strong>${area} م²</strong> بمستوى <strong>${quality}</strong> وتكييف <strong>${ac}</strong> يكلف حوالي <strong>${formatMoney(total, curr)}</strong> بمعدل <strong>${formatMoney(totalMeterRate, curr)}</strong> لكل متر مربع.</p>
            `);
        
        saveLastInputs('house-finishing-cost-calculator');
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
    restoreLastInputs('house-finishing-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>