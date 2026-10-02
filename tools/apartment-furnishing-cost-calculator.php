<?php
/**
 * أداة: حاسبة تكلفة تجهيز شقة للزواج
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'apartment-furnishing-cost-calculator';
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
        <label class="form-label" for="furnishLevel">مستوى الفرش والأجهزة</label>
        <select id="furnishLevel" class="form-control" onchange="calculateTool()">
            <option value="budget" >اقتصادي وعملي (ماركات موثوقة بسعر مناسب)</option>
            <option value="medium" selected>متوسط وفاخر (جودة عالية وتصاميم حديثة)</option>
            <option value="luxury" >فاخر ومميز (أعلى المواصفات وماركات عالمية)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="roomsCount">عدد الغرف (نوم + صالة + مجالس)</label>
        <input type="number" id="roomsCount" class="form-control" value="3" min="1" max="10" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="appliancesIncluded">هل التجهيز يشمل الأجهزة الكهربائية الكبرى (مكيفات، ثلاجة، غسالة، شاشات)؟</label>
        <select id="appliancesIncluded" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، يشمل كافة الأجهزة والمطبخ</option>
            <option value="no" >أثاث ومفروشات وديكور فقط</option>
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
  0 => 'تشمل التكلفة الأثاث الخشبي والمفروشات والمطابخ والأجهزة الكهربائية المنزلية.',
  1 => 'الأجهزة والمكيفات تمثل عادة بين 30% إلى 45% من إجمالي ميزانية تجهيز السكن الجديد.',
),
        'المبالغ مبنية على متوسط أسعار التجزئة في معارض الأثاث والأجهزة المنزلية العربية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي أولويات شراء أثاث الشقة عند محدودية الميزانية؟',
    'a' => 'ابدأ بغرفة النوم الرئيسية والمطبخ والثلاجة والغسالة ومكيفات الغرف الأساسية، ويمكن تأجيل الصالون الإضافي والكماليات لاحقاً.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'marriage-cost-calculator',
  1 => 'furniture-quantity-calculator',
  2 => 'room-furniture-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const level = document.getElementById('furnishLevel').value;
            const rooms = Math.max(1, parseInt(document.getElementById('roomsCount').value) || 3);
            const hasAppliances = document.getElementById('appliancesIncluded').value;
            const curr = getSelectedCurrency();

            let roomCost = 6000;
            let applianceCost = 15000;
            if (level === 'budget') { roomCost = 3500; applianceCost = 9000; }
            if (level === 'luxury') { roomCost = 12000; applianceCost = 30000; }

            const furnitureTotal = rooms * roomCost;
            const appliancesTotal = hasAppliances === 'yes' ? applianceCost : 0;
            const kitchenSupplies = level === 'budget' ? 2000 : (level === 'luxury' ? 8000 : 4000);
            const total = furnitureTotal + appliancesTotal + kitchenSupplies;

            setPrimaryResult(formatMoney(total, curr), 'التكلفة الإجمالية لتجهيز الشقة');
            showResultArea();

            setDetailStats([
                { label: 'أثاث الغرف والمجالس', value: formatMoney(furnitureTotal, curr), color: '#3b82f6' },
                { label: 'الأجهزة الكهربائية والمكيفات', value: formatMoney(appliancesTotal, curr), color: '#10b981' },
                { label: 'مستلزمات المطبخ والحمام والديكور', value: formatMoney(kitchenSupplies, curr), color: '#8b5cf6' },
                { label: 'متوسط تكلفة الغرفة الواحدة', value: formatMoney(roomCost, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>تكلفة تجهيز شقة مكونة من <strong>${rooms} غرف</strong> بمستوى <strong>${level === 'luxury' ? 'فاخر' : (level === 'budget' ? 'اقتصادي' : 'متوسط')}</strong> تقدر بـ <strong>${formatMoney(total, curr)}</strong> تشمل الأثاث والأجهزة والمطبخ.</p>
            `);
        
        saveLastInputs('apartment-furnishing-cost-calculator');
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
    restoreLastInputs('apartment-furnishing-cost-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>