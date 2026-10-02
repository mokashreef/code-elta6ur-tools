<?php
/**
 * أداة: حاسبة تكلفة الزواج الشاملة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'marriage-cost-calculator';
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
        <label class="form-label" for="dowryAmount">المهر والشبكة والهدايا المبدئية</label>
        <input type="number" id="dowryAmount" class="form-control" value="40000" min="0"  step="1000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hallCost">تكلفة قاعة الأفراح والضيافة والعشاء</label>
        <input type="number" id="hallCost" class="form-control" value="25000" min="0"  step="1000"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="clothesAndPrep">فستان الزفاف، البدلة، وتجهيزات العروسين</label>
        <input type="number" id="clothesAndPrep" class="form-control" value="10000" min="0"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="honeymoonCost">رحلة شهر العسل (تذاكر وإقامة)</label>
        <input type="number" id="honeymoonCost" class="form-control" value="15000" min="0"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="apartmentDeposit">مقدم إيجار الشقة أو العربون</label>
        <input type="number" id="apartmentDeposit" class="form-control" value="12000" min="0"  step="500"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="otherEmergencies">مصاريف طارئة وضيافة إضافية</label>
        <input type="number" id="otherEmergencies" class="form-control" value="5000" min="0"  step="500"  oninput="calculateTool()">
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
  0 => 'إجمالي التكلفة = المهر + حفل الزفاف + التجهيزات الشخصية + شهر العسل + سكن البداية + الطوارئ.',
  1 => 'يُوصى دائماً برصد بند طوارئ بنسبة 10% إلى 15% للمصاريف غير المتوقعة أثناء التحضيرات.',
),
        'التكاليف تختلف باختلاف التقاليد الاجتماعية والدولة ومستوى الحفل المختار.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف يمكن تقليل ميزانية الزواج دون التأثير على الفرحة؟',
    'a' => 'التركيز على حفل عائلي دافئ ومختصر، والحجز المبكر لقاعات الأفراح وتذاكر شهر العسل في غير مواسم الذروة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'apartment-furnishing-cost-calculator',
  1 => 'family-monthly-budget-calculator',
  2 => 'savings-goal-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const dowry = Math.max(0, parseFloat(document.getElementById('dowryAmount').value) || 0);
            const hall = Math.max(0, parseFloat(document.getElementById('hallCost').value) || 0);
            const prep = Math.max(0, parseFloat(document.getElementById('clothesAndPrep').value) || 0);
            const honey = Math.max(0, parseFloat(document.getElementById('honeymoonCost').value) || 0);
            const rent = Math.max(0, parseFloat(document.getElementById('apartmentDeposit').value) || 0);
            const other = Math.max(0, parseFloat(document.getElementById('otherEmergencies').value) || 0);
            const curr = getSelectedCurrency();

            const total = dowry + hall + prep + honey + rent + other;
            const ceremonyTotal = hall + prep;

            setPrimaryResult(formatMoney(total, curr), 'الميزانية التقديرية الإجمالية للزواج');
            showResultArea();

            setDetailStats([
                { label: 'تكاليف الحفل والضيافة', value: formatMoney(ceremonyTotal, curr), color: '#3b82f6' },
                { label: 'المهر والشبكة', value: formatMoney(dowry, curr), color: '#10b981' },
                { label: 'شهر العسل', value: formatMoney(honey, curr), color: '#8b5cf6' },
                { label: 'تأمين السكن والبنود الطارئة', value: formatMoney(rent + other, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>إجمالي تكلفة مراسم الزواج وبداية الحياة الزوجية تقدر بـ <strong>${formatMoney(total, curr)}</strong>. تمثل حفلة الزفاف والضيافة حوالي <strong>${total > 0 ? ((ceremonyTotal/total)*100).toFixed(0) : 0}%</strong> من الميزانية.</p>
            `);
        
        saveLastInputs('marriage-cost-calculator');
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
    restoreLastInputs('marriage-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>