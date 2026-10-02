<?php
/**
 * أداة: حاسبة تسعير خدمات البرمجة وتطوير المواقع
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'dev-pricing-calculator';
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
        <label class="form-label" for="projectCategory">نوع المشروع البرمجي</label>
        <select id="projectCategory" class="form-control" onchange="calculateTool()">
            <option value="landing" >صفحة هبوط تسويقية سريعة (Landing Page)</option>
            <option value="corporate" selected>موقع تعريفي للشركات (5-10 صفحات)</option>
            <option value="ecommerce" >متجر إلكتروني متكامل مع بوابات دفع وشحن</option>
            <option value="custom_web" >تطبيق ويب مخصص (Custom Web App / SaaS)</option>
            <option value="mobile_app" >تطبيق هاتف ذكي (iOS & Android)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="integrationsCount">عدد عمليات الربط الخارجي مع APIs وبوابات الدفع</label>
        <input type="number" id="integrationsCount" class="form-control" value="2" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="supportMonths">أشهر الدعم الفني والصيانة المشمولة مجاناً</label>
        <input type="number" id="supportMonths" class="form-control" value="2" min="0" max="12" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="urgencySpeed">سرعة التسليم المطلوبة</label>
        <select id="urgencySpeed" class="form-control" onchange="calculateTool()">
            <option value="normal" selected>وقت تسليم طبيعي ومعتاد</option>
            <option value="urgent" >مستعجل جداً (+30% تكلفة ضغط عمل)</option>
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
  0 => 'التسعير يعتمد على نوع وحجم النظام وقواعد البيانات المطلوبة.',
  1 => 'عمليات الربط مع أنظمة خارجية (APIs) وبوابات الدفع تتطلب وقتاً واختبارات أمنية إضافية.',
  2 => 'فترة الضمان والصيانة بعد الإطلاق تحمي العميل وتزيد من موثوقية المطور.',
),
        'التسعير يفترض تنفيذاً برمجياً عالي الجودة وموثقاً (Clean Code) متوافقاً مع الهواتف الذكية ومحركات البحث.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف يتم تقسيم دفعات المشروع البرمجي عادة؟',
    'a' => 'المعتاد هو: 40% دفعة مقدمة عند توقيع العقد، 40% عند الانتهاء من مرحلة التطوير والمعاينة على سيرفر تجريبي، و 20% عند التسليم النهائي ونقل الموقع لسيرفر العميل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'design-pricing-calculator',
  1 => 'freelancer-project-price-calculator',
  2 => 'project-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cat = document.getElementById('projectCategory').value;
            const apis = Math.max(0, parseInt(document.getElementById('integrationsCount').value) || 0);
            const support = Math.max(0, parseInt(document.getElementById('supportMonths').value) || 0);
            const speed = document.getElementById('urgencySpeed').value;
            const curr = getSelectedCurrency();

            let baseCost = 400;
            let estDays = 7;
            if (cat === 'corporate') { baseCost = 900; estDays = 14; }
            if (cat === 'ecommerce') { baseCost = 1800; estDays = 25; }
            if (cat === 'custom_web') { baseCost = 3500; estDays = 45; }
            if (cat === 'mobile_app') { baseCost = 4500; estDays = 60; }

            const apiCost = apis * 150;
            const supportCost = support * 100;
            let total = baseCost + apiCost + supportCost;
            if (speed === 'urgent') total *= 1.3;

            setPrimaryResult(formatMoney(total, curr), 'سعر التكلفة المقترح لتطوير المشروع');
            showResultArea();

            setDetailStats([
                { label: 'المدة الزمنية المتوقعة للتسليم', value: estDays + ' يوم عمل تقريباً', color: '#3b82f6' },
                { label: 'تكلفة ربط الخدمات و APIs', value: formatMoney(apiCost, curr), color: '#10b981' },
                { label: 'قيمة الدعم الفني والصيانة', value: formatMoney(supportCost, curr), color: '#8b5cf6' },
                { label: 'دفعة البداية (40%)', value: formatMoney(total * 0.4, curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>السعر التقديري المتوازن لتنفيذ هذا المشروع هو <strong>${formatMoney(total, curr)}</strong> بمدة تسليم متوقعة <strong>${estDays} يوم عمل</strong> شاملة <strong>${support} أشهر</strong> صيانة وضمان للأخطاء البرمجية.</p>
            `);
        
        saveLastInputs('dev-pricing-calculator');
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
    restoreLastInputs('dev-pricing-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>