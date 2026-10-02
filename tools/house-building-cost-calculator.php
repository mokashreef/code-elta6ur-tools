<?php
/**
 * أداة: حاسبة تكلفة بناء منزل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'house-building-cost-calculator';
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
        <label class="form-label" for="landArea">مساحة الأرض (متر مربع)</label>
        <input type="number" id="landArea" class="form-control" value="400" min="50"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="floorsCount">عدد الطوابق / الأدوار</label>
        <input type="number" id="floorsCount" class="form-control" value="2" min="1" max="10" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="buildCoverageRate">نسبة البناء من مساحة الأرض (%) - عادة 60%</label>
        <input type="number" id="buildCoverageRate" class="form-control" value="60" min="30" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="buildScope">مرحلة البناء المطلوبة</label>
        <select id="buildScope" class="form-control" onchange="calculateTool()">
            <option value="bone" >بناء عظم فقط مع المواد (الهيكل الخرساني والمباني)</option>
            <option value="turnkey_standard" selected>تسليم مفتاح - تشطيب قياسي متوازن</option>
            <option value="turnkey_deluxe" >تسليم مفتاح - تشطيب ديلوكس فاخر</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="meterPriceOverride">سعر المتر المربع التقديري (اتركه 0 لاستخدام السعر القياسي)</label>
        <input type="number" id="meterPriceOverride" class="form-control" value="0" min="0"  step="50"  oninput="calculateTool()">
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
  0 => 'مسطح الدور = مساحة الأرض × نسبة البناء النظامية.',
  1 => 'إجمالي مسطحات البناء = مسطح الدور × عدد الأدوار (مع الملاحق والأسوار).',
  2 => 'التكلفة الإجمالية = إجمالي مسطحات البناء × سعر المتر المربع للمرحلة.',
),
        'الأسعار تقديرية وتتأثر بأسعار الحديد والخرسانة وتضاريس الأرض.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفرق بين بناء عظم وتسليم مفتاح؟',
    'a' => 'العظم يشمل الحفر والخرسانات المسلحة وبناء البلوك وعزل القواعد فقط، بينما تسليم المفتاح يشمل التشطيب الكامل من سباكة وكهرباء وأرضيات ودهانات وأبواب ونوافذ.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'house-finishing-cost-calculator',
  1 => 'cement-calculator',
  2 => 'block-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const land = Math.max(50, parseFloat(document.getElementById('landArea').value) || 400);
            const floors = Math.max(1, parseInt(document.getElementById('floorsCount').value) || 2);
            const coverage = Math.max(30, Math.min(100, parseFloat(document.getElementById('buildCoverageRate').value) || 60)) / 100;
            const scope = document.getElementById('buildScope').value;
            const override = parseFloat(document.getElementById('meterPriceOverride').value) || 0;
            const curr = getSelectedCurrency();

            const floorArea = land * coverage;
            const totalBuiltArea = floorArea * floors;

            let meterRate = 600; // عظم
            if (scope === 'turnkey_standard') meterRate = 1400;
            if (scope === 'turnkey_deluxe') meterRate = 2200;
            if (override > 0) meterRate = override;

            const totalCost = totalBuiltArea * meterRate;
            const engineeringFees = totalCost * 0.04; // 4% رخص وإشراف هندسي

            setPrimaryResult(formatMoney(totalCost, curr), 'التكلفة التقديرية لإجمالي مسطحات البناء');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي مسطحات البناء (م²)', value: formatNumber(totalBuiltArea, 1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الدور الواحد (م²)', value: formatNumber(floorArea, 1) + ' م²', color: '#10b981' },
                { label: 'سعر المتر المعتمد', value: formatMoney(meterRate, curr) + ' / م²', color: '#f59e0b' },
                { label: 'أتعاب المخططات والإشراف الهندسي المقدرة', value: formatMoney(engineeringFees, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لبناء مسطح إجمالي <strong>${formatNumber(totalBuiltArea, 1)} متر مربع</strong> على <strong>${floors} أدوار</strong> بمستوى <strong>${scope === 'bone' ? 'عظم' : 'تسليم مفتاح'}</strong>، تقدر التكلفة بـ <strong>${formatMoney(totalCost, curr)}</strong>.</p>
            `);
        
        saveLastInputs('house-building-cost-calculator');
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
    restoreLastInputs('house-building-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>