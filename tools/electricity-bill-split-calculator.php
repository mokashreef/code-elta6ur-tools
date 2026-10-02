<?php
/**
 * أداة: حاسبة تقسيم فاتورة الكهرباء بين الساكنين
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'electricity-bill-split-calculator';
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
        <label class="form-label" for="totalElectricBill">قيمة فاتورة الكهرباء الإجمالية</label>
        <input type="number" id="totalElectricBill" class="form-control" value="750" min="10"  step="25"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="tenantsCount">عدد الغرف أو الساكنين في الشقة</label>
        <input type="number" id="tenantsCount" class="form-control" value="3" min="2" max="15" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="heavyAcUserCount">هل هناك غرفة تشغل مكيف 24 ساعة بمفردها؟</label>
        <select id="heavyAcUserCount" class="form-control" onchange="calculateTool()">
            <option value="no" selected>لا، الاستخدام متقارب وعادل بين الجميع</option>
            <option value="yes_one" >نعم، غرفة واحدة تشغيلها دائم ومضاعف (+50% حصة إضافية)</option>
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
  0 => 'المكيفات وسخانات الماء تمثل أكثر من 70% من فاتورة الكهرباء في السكن المشترك.',
  1 => 'التراضي والاتفاق المسبق على طريقة احتساب ساعات تشغيل التكييف يمنع الخلافات بين زملاء السكن.',
),
        'يفترض عدم وجود عدادات فرعية لكل غرفة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يُنصح بتركيب عدادات كهرباء فرعية ديجيتال لكل غرفة؟',
    'a' => 'نعم؛ العدادات الفرعية (Sub-meters) رخيصة الثمن وسهلة التركيب وتفصل استهلاك كل غرفة ومكيفها بدقة تامة وتنهي أي خلاف في السكن المشترك نهائياً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'rent-split-calculator',
  1 => 'internet-bill-split-calculator',
  2 => 'ac-consumption-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const bill = Math.max(10, parseFloat(document.getElementById('totalElectricBill').value) || 750);
            const tenants = Math.max(2, parseInt(document.getElementById('tenantsCount').value) || 3);
            const hasHeavy = document.getElementById('heavyAcUserCount').value === 'yes_one';
            const curr = getSelectedCurrency();

            let normalShare = bill / tenants;
            let heavyShare = normalShare;

            if (hasHeavy) {
                // حصة الغرفة الثقيلة 1.5x وحصص البقية 1.0x
                const totalUnits = (tenants - 1) + 1.5;
                normalShare = bill / totalUnits;
                heavyShare = normalShare * 1.5;
            }

            setPrimaryResult(hasHeavy ? formatMoney(normalShare, curr) + ' (والغرفة الكثيفة: ' + formatMoney(heavyShare, curr) + ')' : formatMoney(normalShare, curr) + ' للشخص', 'حصة الفرد من فاتورة الكهرباء');
            showResultArea();

            setDetailStats([
                { label: 'حصة الغرفة العادية', value: formatMoney(normalShare, curr), color: '#10b981' },
                { label: 'حصة غرفة الاستخدام الكثيف', value: hasHeavy ? formatMoney(heavyShare, curr) : 'غير مطبق', color: '#ef4444' },
                { label: 'إجمالي الفاتورة المستحقة', value: formatMoney(bill, curr), color: '#3b82f6' },
                { label: 'عدد الشركاء بالسكن', value: tenants + ' أفراد', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>فاتورة كهرباء بمبلغ <strong>${formatMoney(bill, curr)}</strong> مقسمة بين <strong>${tenants} أفراد</strong>: تبلغ حصة الساكن العادي <strong>${formatMoney(normalShare, curr)}</strong>${hasHeavy ? '، بينما تتحمل الغرفة ذات الاستهلاك المفرط ' + formatMoney(heavyShare, curr) : ''}.</p>
            `);
        
        saveLastInputs('electricity-bill-split-calculator');
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
    restoreLastInputs('electricity-bill-split-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>