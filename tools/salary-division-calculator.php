<?php
/**
 * أداة: حاسبة تقسيم الراتب (قاعدة 50/30/20 المالية العالمية)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'salary-division-calculator';
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
        <label class="form-label" for="monthlyNetSalaryInput">صافي الراتب الشهري</label>
        <input type="number" id="monthlyNetSalaryInput" class="form-control" value="8000" min="500"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="customSplitRatio">نمط التقسيم المالي المفضل</label>
        <select id="customSplitRatio" class="form-control" onchange="calculateTool()">
            <option value="standard_50_30_20" selected>قاعدة 50/30/20 القياسية (50% احتياجات | 30% رغبات | 20% ادخار)</option>
            <option value="conservative_50_20_30" >نمط الادخار المكثف (50% احتياجات | 20% رغبات | 30% استثمار وادخار)</option>
            <option value="strict_70_20_10" >نمط الالتزامات المرتفعة (70% معيشة وإيجار | 20% ادخار | 10% ترفيه)</option>
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
  0 => 'الاحتياجات (Needs 50%): الإيجار، فواتير الخدمات، البقالة الأساسية، أقساط الديون الإلزامية.',
  1 => 'الرغبات (Wants 30%): ارتياد المقاهي، وجبات المطاعم، التسوق الترفيهي، اشتراكات البث والنوادي.',
  2 => 'الادخار (Savings 20%): صندوق الطوارئ، الاستثمار في صناديق المؤشرات أو الذهب، وسداد أصل الديون.',
),
        'القاعدة المالية تفترض التزاماً بتحويل الادخار أولاً (Pay Yourself First) وليس ادخار ما يتبقى في نهاية الشهر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا أفعل إذا كانت احتياجاتي الأساسية تتجاوز 50% من راتبي؟',
    'a' => 'هذا شائع في بداية المسار المهني أو في المدن الكبرى؛ استخدم النمط البديل (70/20/10) مع التركيز على زيادة دخلك ومهاراتك لتقليل نسبة السكن من الراتب تدريجياً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'is-salary-enough-calculator',
  1 => 'savings-goal-calculator',
  2 => 'family-monthly-budget-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const salary = Math.max(500, parseFloat(document.getElementById('monthlyNetSalaryInput').value) || 8000);
            const rule = document.getElementById('customSplitRatio').value;
            const curr = getSelectedCurrency();

            let needsPct = 0.50;
            let wantsPct = 0.30;
            let savingsPct = 0.20;

            if (rule === 'conservative_50_20_30') { needsPct = 0.50; wantsPct = 0.20; savingsPct = 0.30; }
            if (rule === 'strict_70_20_10') { needsPct = 0.70; wantsPct = 0.10; savingsPct = 0.20; }

            const needsAmt = salary * needsPct;
            const wantsAmt = salary * wantsPct;
            const savingsAmt = salary * savingsPct;

            setPrimaryResult(formatMoney(savingsAmt, curr) + ' ادخار شهرياً (' + (savingsPct*100) + '%)', 'المبلغ الموجه للادخار والاستثمار');
            showResultArea();

            setDetailStats([
                { label: 'الاحتياجات الأساسية (' + (needsPct*100) + '%) - سكن وطعام وفواتير', value: formatMoney(needsAmt, curr), color: '#3b82f6' },
                { label: 'الرغبات ونمط الحياة (' + (wantsPct*100) + '%) - ترفيه وسفر وتسوق', value: formatMoney(wantsAmt, curr), color: '#f59e0b' },
                { label: 'الادخار وبناء الثروة (' + (savingsPct*100) + '%) - أسهم وذهب وطوارئ', value: formatMoney(savingsAmt, curr), color: '#10b981' },
                { label: 'الادخار المتراكم في سنة واحدة', value: formatMoney(savingsAmt * 12, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>وفق قاعدة التقسيم المالي، يوزع راتبك البالغ <strong>${formatMoney(salary, curr)}</strong> على النحو التالي:<br>
                - <strong>${formatMoney(needsAmt, curr)}</strong> للمصاريف الحتمية التي لا يمكن العيش بدونها.<br>
                - <strong>${formatMoney(wantsAmt, curr)}</strong> للأنشطة الترفيهية والمطاعم والهوايات.<br>
                - <strong>${formatMoney(savingsAmt, curr)}</strong> تُحوّل فوراً إلى حساب ادخاري استثماري في يوم نزول الراتب.</p>
            `);
        
        saveLastInputs('salary-division-calculator');
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
    restoreLastInputs('salary-division-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>