<?php
/**
 * أداة: حاسبة العائد على الاستثمار (ROI)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'roi-calculator';
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
        <label class="form-label" for="investAmount">مبلغ الاستثمار المبدئي</label>
        <input type="number" id="investAmount" class="form-control" value="50000" min="1"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="returnAmount">إجمالي العائد أو القيمة النهائية</label>
        <input type="number" id="returnAmount" class="form-control" value="75000" min="0"  step="1000" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="investPeriodMonths">مدة الاستثمار (بالشهور)</label>
        <input type="number" id="investPeriodMonths" class="form-control" value="12" min="1"  step="1" oninput="calculateTool()">
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
  0 => 'صافي الربح = العائد الإجمالي - المبلغ المستثمر.',
  1 => 'عائد الاستثمار (ROI) = (صافي الربح ÷ رأس المال المستثمر) × 100.',
  2 => 'العائد السنوي (Annualized) يوضح الأداء السنوي الحقيقي للمقارنة بين الفرص الاستثمارية متفاوتة المدة.',
),
        'الحساب لا يخصم الضرائب أو الرسوم الإدارية إن وجدت ما لم تكن مدمجة في العائد النهائي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الـ ROI الممتاز للاستثمارات التجارية؟',
    'a' => 'يعتمد على المخاطر؛ الاستثمارات العقارية تتراوح عادة بين 8% و 12% سنوياً، بينما المشاريع الناشئة تستهدف 25% إلى 50% أو أكثر لتعويض المخاطرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'payback-period-calculator',
  1 => 'roas-calculator',
  2 => 'break-even-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const invest = Math.max(1, parseFloat(document.getElementById('investAmount').value) || 1);
            const returned = Math.max(0, parseFloat(document.getElementById('returnAmount').value) || 0);
            const months = Math.max(1, parseFloat(document.getElementById('investPeriodMonths').value) || 12);
            const curr = getSelectedCurrency();

            const netProfit = returned - invest;
            const roi = (netProfit / invest) * 100;
            const annualizedRoi = (Math.pow(returned / invest, 12 / months) - 1) * 100;

            setPrimaryResult(roi.toFixed(2) + '%', 'العائد على الاستثمار الإجمالي (ROI)');
            showResultArea();

            setDetailStats([
                { label: 'صافي الربح الاستثماري', value: formatMoney(netProfit, curr), color: netProfit >= 0 ? '#10b981' : '#ef4444' },
                { label: 'العائد السنوي المركب (Annualized ROI)', value: isFinite(annualizedRoi) ? annualizedRoi.toFixed(2) + '%' : 'غير متاح', color: '#3b82f6' },
                { label: 'مضاعف رأس المال', value: (returned / invest).toFixed(2) + 'x', color: '#8b5cf6' },
                { label: 'معدل الربح الشهري المتوسط', value: (roi / months).toFixed(2) + '%', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>حقق استثمارك ربحاً صافياً قدره <strong>${formatMoney(netProfit, curr)}</strong> بنسبة عائد <strong>${roi.toFixed(2)}%</strong> خلال مدة <strong>${months} شهر</strong>.</p>
            `);
        
        saveLastInputs('roi-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('roi-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>