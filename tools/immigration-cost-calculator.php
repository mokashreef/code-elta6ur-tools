<?php
/**
 * أداة: حاسبة تكلفة الهجرة والسفر الدولي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'immigration-cost-calculator';
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
        <label class="form-label" for="immigrationCountry">دولة الهجرة المستهدفة</label>
        <select id="immigrationCountry" class="form-control" onchange="calculateTool()">
            <option value="canada" selected>كندا (Express Entry / PNP) ~ كشف حساب PoF مشروط</option>
            <option value="australia" >أستراليا (Skilled Independent 189/190)</option>
            <option value="germany" >ألمانيا (Chancenkarte / بطاقة الفرصة والفيزا المهنية)</option>
            <option value="uk" >بريطانيا (Skilled Worker Visa)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="familySizeImm">عدد أفراد الأسرة المهاجرين</label>
        <input type="number" id="familySizeImm" class="form-control" value="2" min="1" max="8" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ieltsExamsCount">عدد امتحانات اللغة المطلوبة (IELTS/TEF/PTE)</label>
        <input type="number" id="ieltsExamsCount" class="form-control" value="2" min="1" max="6" step="1"  oninput="calculateTool()">
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
  0 => 'أموال الاستقرار (Proof of Funds) لا يتم دفعها كرسوم، بل يجب إثبات وجودها في حسابك البنكي لعدة أشهر لإثبات قدرتك على إعالة نفسك وأسرتك حتى تجد عملاً.',
  1 => 'تكاليف الهجرة المباشرة تشمل: امتحانات اللغة، تقييم الشهادات (WES)، الفحص الطبي، الرسوم الحكومية، وتذاكر الطيران.',
),
        'المبالغ تقديرية ومبنية على متطلبات برامج الهجرة الرسمية لعام 2025/2026.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحق لي استخدام أموال كشف الحساب البنكي (PoF) بعد الوصول؟',
    'a' => 'نعم؛ أموال إثبات القدرة المالية هي ملكك بالكامل ومخصصة للإنفاق منها على إيجار السكن والمعيشة في الأشهر الأولى بعد وصولك لدولة المهجر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'relocation-cost-calculator',
  1 => 'cost-of-living-calculator',
  2 => 'travel-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const country = document.getElementById('immigrationCountry').value;
            const family = Math.max(1, parseInt(document.getElementById('familySizeImm').value) || 2);
            const exams = Math.max(1, parseInt(document.getElementById('ieltsExamsCount').value) || 2);
            const curr = getSelectedCurrency();

            // تكاليف الاختبارات والتقييم والترجمة
            const examsCost = exams * 260; // سعر امتحان الآيلتس حوالي 260 دولار
            const ecaCost = 250; // معادلة الشهادات WES
            const translationsAndMedical = family * 350; // فحص طبي وترجمة أوراق
            const flightTickets = family * 900; // تذاكر الطيران للوجهة

            // إثبات القدرة المالية المشروط (Proof of Funds) ورسوم الحكومة
            let governmentFees = family * 1100;
            let settlementFundsRequired = 10000;

            if (country === 'canada') {
                governmentFees = 1000 + (family > 1 ? (family - 1) * 700 : 0);
                settlementFundsRequired = family === 1 ? 14000 : (family === 2 ? 17500 : 21500);
            } else if (country === 'germany') {
                settlementFundsRequired = family * 12000; // حساب بنكي مغلق Sperrkonto
            }

            const totalProcessCost = examsCost + ecaCost + translationsAndMedical + governmentFees + flightTickets;
            const grandTotalWithProofOfFunds = totalProcessCost + settlementFundsRequired;

            setPrimaryResult(formatMoney(grandTotalWithProofOfFunds, curr), 'إجمالي السيولة المالية المطلوبة للهجرة');
            showResultArea();

            setDetailStats([
                { label: 'كشف الحساب البنكي المشروط (Proof of Funds)', value: formatMoney(settlementFundsRequired, curr), color: '#10b981' },
                { label: 'تكاليف الإجراءات والرسوم الحكومية والتذاكر', value: formatMoney(totalProcessCost, curr), color: '#ef4444' },
                { label: 'رسوم الفحص الطبي والترجمة ومعادلة الشهادات', value: formatMoney(translationsAndMedical + ecaCost, curr), color: '#f59e0b' },
                { label: 'رسوم امتحانات اللغة (' + exams + ' امتحانات)', value: formatMoney(examsCost, curr), color: '#3b82f6' }
            ]);

            setResultContent(`
                <p>للهجرة إلى <strong>${country === 'canada' ? 'كندا' : (country === 'australia' ? 'أستراليا' : 'ألمانيا')}</strong> لأسرة من <strong>${family} أفراد</strong>، تحتاج إلى سيولة إجمالية قدرها <strong>${formatMoney(grandTotalWithProofOfFunds, curr)}</strong>، تتضمن <strong>${formatMoney(settlementFundsRequired, curr)}</strong> في كشف الحساب البنكي المشروط كأموال استقرار، و <strong>${formatMoney(totalProcessCost, curr)}</strong> كرسوم إجراءات واختبارات وتذاكر طيران.</p>
            `);
        
        saveLastInputs('immigration-cost-calculator');
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
    restoreLastInputs('immigration-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>