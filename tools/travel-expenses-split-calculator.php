<?php
/**
 * أداة: حاسبة تقسيم مصاريف السفر بين الأصدقاء
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'travel-expenses-split-calculator';
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
        <label class="form-label" for="totalGroupExpense">إجمالي الفواتير والمصاريف المشتركة للرحلة</label>
        <input type="number" id="totalGroupExpense" class="form-control" value="8400" min="10"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="travelersCountSplit">عدد أفراد الرحلة أو الأصدقاء</label>
        <input type="number" id="travelersCountSplit" class="form-control" value="4" min="2" max="30" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="userPaidAlready">المبلغ الذي دفعته أنت من جيبك حتى الآن للمجموعة</label>
        <input type="number" id="userPaidAlready" class="form-control" value="3000" min="0"  step="50"  oninput="calculateTool()">
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
  0 => 'حصة الشخص الواحد = إجمالي المصاريف المشتركة ÷ عدد الأفراد.',
  1 => 'صافي التسوية = المبلغ المدفوع من الشخص - حصته المستحقة.',
  2 => 'إذا كان الناتج موجباً فالمستخدم يسترد الفرق من أصدقائه، وإذا كان سالباً فيجب عليه سداد المبلغ المتبقي.',
),
        'يفترض تقسيم التكاليف المشتركة بالتساوي دون بنود فردية خاصة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف نتعامل مع المشتريات الشخصية المنفصلة أثناء السفر؟',
    'a' => 'المشتريات الفردية كالهدايا التذكارية وملابس التسوق الشخصية يجب أن يدفعها صاحبها مباشرة ببطاقته ولا تضاف إلى الفواتير المشتركة للمجموعة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'travel-cost-calculator',
  1 => 'restaurant-bill-split-calculator',
  2 => 'rent-split-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const total = Math.max(10, parseFloat(document.getElementById('totalGroupExpense').value) || 8400);
            const count = Math.max(2, parseInt(document.getElementById('travelersCountSplit').value) || 4);
            const paid = Math.max(0, parseFloat(document.getElementById('userPaidAlready').value) || 0);
            const curr = getSelectedCurrency();

            const perPersonShare = total / count;
            const balance = paid - perPersonShare;

            let resultText = '';
            let statusColor = '#10b981';
            if (balance > 0) {
                resultText = 'تستحق استرداد ' + formatMoney(balance, curr) + ' من أصدقائك ✅';
                statusColor = '#10b981';
            } else if (balance < 0) {
                resultText = 'عليك دفع ' + formatMoney(Math.abs(balance), curr) + ' لتسوية حسابك ⚠️';
                statusColor = '#ef4444';
            } else {
                resultText = 'حسابك مسوى بالكامل ومتوازن 0 ✅';
            }

            setPrimaryResult(formatMoney(perPersonShare, curr) + ' للشخص', 'حصة الفرد العادلة من الرحلة');
            showResultArea();

            setDetailStats([
                { label: 'حصة الفرد العادلة بالتساوي', value: formatMoney(perPersonShare, curr), color: '#3b82f6' },
                { label: 'المبلغ الذي دفعته أنت', value: formatMoney(paid, curr), color: '#10b981' },
                { label: 'حالة تسوية حسابك الشخصي', value: resultText, color: statusColor },
                { label: 'إجمالي مصاريف المجموعة', value: formatMoney(total, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي مصاريف الرحلة <strong>${formatMoney(total, curr)}</strong> مقسمة بالتساوي على <strong>${count} أشخاص</strong> تعطي حصة <strong>${formatMoney(perPersonShare, curr)}</strong> لكل فرد. بما أنك دفعت <strong>${formatMoney(paid, curr)}</strong>، فإن وضعك المالي: <strong>${resultText}</strong>.</p>
            `);
        
        saveLastInputs('travel-expenses-split-calculator');
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
    restoreLastInputs('travel-expenses-split-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>