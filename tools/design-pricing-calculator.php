<?php
/**
 * أداة: حاسبة تسعير خدمات التصميم
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'design-pricing-calculator';
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
        <label class="form-label" for="designType">نوع خدمة التصميم</label>
        <select id="designType" class="form-control" onchange="calculateTool()">
            <option value="logo" selected>تصميم شعار وهوية بصرية (Logo & Brand Identity)</option>
            <option value="uiux_screen" >تصميم واجهات تطبيقات ومواقع (UI/UX)</option>
            <option value="social_posts" >بوستات وبنرات سوشيال ميديا</option>
            <option value="print_brochure" >مطبوعات وبروشورات وكتالوجات</option>
            <option value="presentation" >عروض تقديمية احترافية (Pitch Decks)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="itemsCount">عدد العناصر أو الشاشات أو التصاميم</label>
        <input type="number" id="itemsCount" class="form-control" value="1" min="1"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="designerLevel">مستوى خبرة المصمم</label>
        <select id="designerLevel" class="form-control" onchange="calculateTool()">
            <option value="junior" >مبتدئ / صاعد (خبرة 1-2 سنة)</option>
            <option value="mid" selected>متوسط الكفاءة (خبرة 3-5 سنوات)</option>
            <option value="senior" >محترف متقدم / خبير (أكثر من 5 سنوات)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="commercialRights">حقوق الاستخدام التجاري والتنازل عن الملفات المفتوحة</label>
        <select id="commercialRights" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، نقل كامل الحقوق والملفات المصدرية (AI/PSD/Figma)</option>
            <option value="no" >تصميم للاستخدام العادي وتسليم ملفات نهائية فقط</option>
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
  0 => 'التسعير يعتمد على نوع التصميم، المجهود الإبداعي، ومستوى المصمم في السوق.',
  1 => 'الملفات المصدرية المفتوحة (Source Files) والتنازل عن الملكية الفكرية ترفع قيمة العرض بنسبة 25% إلى 50% كمعيار عالمي.',
),
        'الأسعار مبنية على استبيانات أجور المصممين المستقلين في السوق العربي لعام 2025/2026.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحق للعميل طلب تعديلات غير محدودة؟',
    'a' => 'لا؛ يجب أن يتضمن عرض السعر عدداً محدداً من جولات المراجعة (عادة جولتين إلى 3 جولات)، وأي تعديل إضافي يُحسب بسعر ساعة منفصل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'dev-pricing-calculator',
  1 => 'social-media-pricing-calculator',
  2 => 'freelancer-project-price-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const type = document.getElementById('designType').value;
            const count = Math.max(1, parseInt(document.getElementById('itemsCount').value) || 1);
            const level = document.getElementById('designerLevel').value;
            const rights = document.getElementById('commercialRights').value;
            const curr = getSelectedCurrency();

            let basePricePerItem = 100;
            if (type === 'logo') basePricePerItem = 400;
            if (type === 'uiux_screen') basePricePerItem = 60;
            if (type === 'social_posts') basePricePerItem = 25;
            if (type === 'print_brochure') basePricePerItem = 70;
            if (type === 'presentation') basePricePerItem = 15;

            let multiplier = 1.0;
            if (level === 'junior') multiplier = 0.7;
            if (level === 'senior') multiplier = 1.8;

            let totalPrice = basePricePerItem * count * multiplier;
            if (rights === 'yes') totalPrice *= 1.25;

            setPrimaryResult(formatMoney(totalPrice, curr), 'السعر المقترح للمشروع');
            showResultArea();

            setDetailStats([
                { label: 'متوسط سعر العنصر الواحد', value: formatMoney(totalPrice / count, curr), color: '#3b82f6' },
                { label: 'مستوى الخبرة المعتمد', value: level === 'senior' ? 'خبير' : (level === 'junior' ? 'مبتدئ' : 'متوسط'), color: '#10b981' },
                { label: 'قيمة الملفات المفتوحة والحقوق التجارية', value: rights === 'yes' ? '+25%' : 'غير مشمولة', color: '#f59e0b' },
                { label: 'عدد العناصر المطلوبة', value: count + ' عنصر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>السعر العادل لتنفيذ <strong>${count} تصاميم</strong> بمستوى خبرة <strong>${level}</strong> هو <strong>${formatMoney(totalPrice, curr)}</strong> بما يضمن حقوقك والوقت المستغرق في الأفكار والتعديلات.</p>
            `);
        
        saveLastInputs('design-pricing-calculator');
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
    restoreLastInputs('design-pricing-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>