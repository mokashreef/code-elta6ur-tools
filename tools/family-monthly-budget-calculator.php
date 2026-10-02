<?php
/**
 * أداة: حاسبة المصاريف الشهرية للأسرة وميزانية البيت
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'family-monthly-budget-calculator';
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
        <label class="form-label" for="familyTotalIncome">إجمالي دخل الأسرة الشهري (الزوج والزوجة وأي دخل إضافي)</label>
        <input type="number" id="familyTotalIncome" class="form-control" value="12000" min="1000"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="housingCostFam">السكن (إيجار أو قسط تمويل عقاري)</label>
        <input type="number" id="housingCostFam" class="form-control" value="3000" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="foodGroceryFam">الطعام والتموين المنزلي ومستلزمات النظافة</label>
        <input type="number" id="foodGroceryFam" class="form-control" value="2500" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="educationFam">التعليم ومصاريف المدارس والمواصلات المدرسية</label>
        <input type="number" id="educationFam" class="form-control" value="1500" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="utilitiesFam">فواتير الكهرباء والمياه والإنترنت والجوالات</label>
        <input type="number" id="utilitiesFam" class="form-control" value="900" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="transportCarFam">وقود السيارات وصيانتها وقسط السيارة</label>
        <input type="number" id="transportCarFam" class="form-control" value="1400" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="healthcareFam">الرعاية الصحية والأدوية والتأمين</label>
        <input type="number" id="healthcareFam" class="form-control" value="500" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="entertainmentFam">الترفيه والزيارات والمطاعم والتسوق</label>
        <input type="number" id="entertainmentFam" class="form-control" value="1000" min="0"  step="50"  oninput="calculateTool()">
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
  0 => 'الميزانية الأسرية الناجحة تعتمد على الشفافية والتخطيط المالي المشترك بين الزوجين.',
  1 => 'تسجيل المصاريف اليومية في نهاية كل أسبوع يكشف مواضع الهدر المالي الخفي في البقالة والمطاعم.',
),
        'يفترض عدم وجود ديون بنكية غير مسجلة ضمن البنود.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف نلتزم بميزانية الطعام دون حرمان؟',
    'a' => 'التسوق مرة واحدة أسبوعياً بقائمة مشتريات محددة مسبقاً، وتجنب الذهاب للسوبرماركت وأنت جائع، وتجهيز الوجبات منزلياً (Meal Prep) لأيام العمل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'salary-division-calculator',
  1 => 'is-salary-enough-calculator',
  2 => 'baby-first-year-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const income = Math.max(1000, parseFloat(document.getElementById('familyTotalIncome').value) || 12000);
            const housing = Math.max(0, parseFloat(document.getElementById('housingCostFam').value) || 3000);
            const food = Math.max(0, parseFloat(document.getElementById('foodGroceryFam').value) || 2500);
            const edu = Math.max(0, parseFloat(document.getElementById('educationFam').value) || 1500);
            const util = Math.max(0, parseFloat(document.getElementById('utilitiesFam').value) || 900);
            const trans = Math.max(0, parseFloat(document.getElementById('transportCarFam').value) || 1400);
            const health = Math.max(0, parseFloat(document.getElementById('healthcareFam').value) || 500);
            const ent = Math.max(0, parseFloat(document.getElementById('entertainmentFam').value) || 1000);
            const curr = getSelectedCurrency();

            const totalExpenses = housing + food + edu + util + trans + health + ent;
            const savings = income - totalExpenses;
            const savingsRate = (savings / income) * 100;

            setPrimaryResult(formatMoney(totalExpenses, curr) + ' شهرياً', 'إجمالي مصاريف الأسرة الشهرية');
            showResultArea();

            setDetailStats([
                { label: 'المتبقي للادخار والاستثمار', value: formatMoney(savings, curr), color: savings >= 0 ? '#10b981' : '#ef4444' },
                { label: 'نسبة الادخار من الدخل', value: savingsRate.toFixed(1) + '%', color: savingsRate >= 15 ? '#10b981' : '#f59e0b' },
                { label: 'أكبر بند في الميزانية', value: housing >= food ? 'السكن (' + ((housing/totalExpenses)*100).toFixed(0) + '%)' : 'الطعام (' + ((food/totalExpenses)*100).toFixed(0) + '%)', color: '#3b82f6' },
                { label: 'إجمالي الدخل الشهري', value: formatMoney(income, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك مصاريف الأسرة <strong>${((totalExpenses/income)*100).toFixed(1)}%</strong> من الدخل الشهري. يتبقى لكم <strong>${formatMoney(savings, curr)}</strong> شهرياً كفائض مالي يوجه للادخار أو صندوق طوارئ الأسرة.</p>
            `);
        
        saveLastInputs('family-monthly-budget-calculator');
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
    restoreLastInputs('family-monthly-budget-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>