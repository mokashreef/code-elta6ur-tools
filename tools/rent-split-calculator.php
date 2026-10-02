<?php
/**
 * أداة: حاسبة تقسيم الإيجار العادل بين الشركاء في السكن
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'rent-split-calculator';
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
        <label class="form-label" for="totalApartmentRent">إجمالي إيجار الشقة بالكامل شهرياً</label>
        <input type="number" id="totalApartmentRent" class="form-control" value="3600" min="500"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="roomTypeOption">مواصفات غرفتك مقارنة بالشقة</label>
        <select id="roomTypeOption" class="form-control" onchange="calculateTool()">
            <option value="equal" >جميع الغرف متطابقة تماماً في المساحة والمزايا (تقسيم بالتساوي)</option>
            <option value="master_ensuite" selected>غرفة ماستر كبيرة مع حمام خاص وشرفة (+35% نسبة إضافية)</option>
            <option value="standard_room" >غرفة فردية عادية بحمام مشترك</option>
            <option value="shared_room" >سرير في غرفة مشتركة مع شريك آخر (-35% خصم)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="roommatesCount">إجمالي عدد الساكنين في الشقة</label>
        <input type="number" id="roommatesCount" class="form-control" value="3" min="2" max="10" step="1"  oninput="calculateTool()">
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
  0 => 'التقسيم العادل للإيجار يعتمد على مساحة الغرفة ووجود حمام داخلي خاص أو شرفة خاصة.',
  1 => 'الغرفة الماستر ذات الحمام الخاص تتحمل عادة بين 25% إلى 35% زيادة عن الغرفة العادية ذات الحمام المشترك.',
),
        'يفترض تقاسم فواتير الإنترنت والكهرباء بالتساوي بين جميع الأفراد.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف نقسم فواتير الخدمات (الكهرباء والإنترنت)؟',
    'a' => 'فواتير الخدمات والمياه والإنترنت تُقسم بالتساوي دائماً على عدد الأشخاص، لأن استخدام الأجهزة والمرافق المشتركة يكون متقارباً للجميع.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'electricity-bill-split-calculator',
  1 => 'internet-bill-split-calculator',
  2 => 'independence-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const rent = Math.max(500, parseFloat(document.getElementById('totalApartmentRent').value) || 3600);
            const type = document.getElementById('roomTypeOption').value;
            const count = Math.max(2, parseInt(document.getElementById('roommatesCount').value) || 3);
            const curr = getSelectedCurrency();

            const equalShare = rent / count;
            let myShare = equalShare;

            if (type === 'master_ensuite') {
                myShare = equalShare * 1.30;
            } else if (type === 'shared_room') {
                myShare = equalShare * 0.70;
            } else {
                myShare = equalShare;
            }

            setPrimaryResult(formatMoney(myShare, curr) + ' شهرياً', 'حصتك العادلة من الإيجار');
            showResultArea();

            setDetailStats([
                { label: 'حصة غرفتك المقترحة', value: formatMoney(myShare, curr), color: '#10b981' },
                { label: 'الحصة المتساوية الافتراضية', value: formatMoney(equalShare, curr), color: '#3b82f6' },
                { label: 'إجمالي إيجار الشقة بالكامل', value: formatMoney(rent, curr), color: '#ef4444' },
                { label: 'عدد الشركاء بالسكن', value: count + ' شركاء', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>بناءً على مواصفات غرفتك ${type === 'master_ensuite' ? '(ماستر بحمام خاص)' : (type === 'shared_room' ? '(مشتركة)' : '(عادية)')}، تبلغ حصتك العادلة <strong>${formatMoney(myShare, curr)} شهرياً</strong> بدلاً من التقسيم المتساوي البالغ <strong>${formatMoney(equalShare, curr)}</strong>.</p>
            `);
        
        saveLastInputs('rent-split-calculator');
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
    restoreLastInputs('rent-split-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>