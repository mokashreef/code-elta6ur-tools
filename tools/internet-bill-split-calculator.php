<?php
/**
 * أداة: حاسبة تقسيم اشتراك الإنترنت المنزلي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'internet-bill-split-calculator';
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
        <label class="form-label" for="monthlyInternetCost">قيمة اشتراك باقة الألياف أو الراوتر شهرياً</label>
        <input type="number" id="monthlyInternetCost" class="form-control" value="287.50" min="50"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="usersCountNet">عدد الشقق أو المستخدمين المشاركين بالشبكة</label>
        <input type="number" id="usersCountNet" class="form-control" value="3" min="2" max="20" step="1"  oninput="calculateTool()">
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
  0 => 'الاشتراك المشترك في باقة فايبر سريعة (مثل 300 أو 500 ميجابت) وتقسيم تكلفتها يوفر أكثر من 60% من تكلفة الباقات الفردية لكل ساكن.',
  1 => 'يُفضل استخدام أجهزة راوتر تدعم Mesh Wi-Fi لتوزيع الإشارة بقوة لكافة الغرف.',
),
        'يفترض اشتراكاً منزلياً ثابتاً غير محدود البيانات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف نضمن عدم سحب أحد المشتركين لكامل سرعة الإنترنت بالتحميل؟',
    'a' => 'يمكن من خلال إعدادات الراوتر تفعيل خاصية التحكم بجودة الخدمة (QoS - Quality of Service) لتوزيع السرعة بالتساوي ومنع هبوط السرعة أثناء بث الفيديوهات ومكالمات العمل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'rent-split-calculator',
  1 => 'electricity-bill-split-calculator',
  2 => 'is-salary-enough-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const bill = Math.max(50, parseFloat(document.getElementById('monthlyInternetCost').value) || 287.50);
            const users = Math.max(2, parseInt(document.getElementById('usersCountNet').value) || 3);
            const curr = getSelectedCurrency();

            const share = bill / users;
            const annualShare = share * 12;

            setPrimaryResult(formatMoney(share, curr) + ' شهرياً للشخص', 'حصة الفرد من اشتراك الإنترنت');
            showResultArea();

            setDetailStats([
                { label: 'حصة الشخص الواحد شهرياً', value: formatMoney(share, curr), color: '#10b981' },
                { label: 'إجمالي ما يدفعه الشخص سنوياً', value: formatMoney(annualShare, curr), color: '#3b82f6' },
                { label: 'قيمة الاشتراك الإجمالي شهرياً', value: formatMoney(bill, curr), color: '#f59e0b' },
                { label: 'عدد المشتركين بالخط', value: users + ' مستخدمين', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>اشتراك إنترنت فايبر بقيمة <strong>${formatMoney(bill, curr)}</strong> مقسم على <strong>${users} أفراد</strong>، يكلف كل شخص <strong>${formatMoney(share, curr)} شهرياً</strong> فقط، محققاً وفراً كبيراً بدلاً من الاشتراك الفردي المنفصل لكل شخص.</p>
            `);
        
        saveLastInputs('internet-bill-split-calculator');
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
    restoreLastInputs('internet-bill-split-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>