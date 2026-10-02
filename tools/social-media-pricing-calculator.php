<?php
/**
 * أداة: حاسبة تسعير إدارة حسابات السوشيال ميديا
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'social-media-pricing-calculator';
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
        <label class="form-label" for="postsPerMonth">عدد المنشورات والتصاميم الثابتة شهرياً</label>
        <input type="number" id="postsPerMonth" class="form-control" value="16" min="0"  step="2" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="reelsPerMonth">عدد الفيديوهات القصيرة (Reels / TikTok) شهرياً</label>
        <input type="number" id="reelsPerMonth" class="form-control" value="8" min="0"  step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="platformsCount">عدد المنصات المدارة (إنستغرام، إكس، تيك توك، إلخ)</label>
        <input type="number" id="platformsCount" class="form-control" value="2" min="1" max="6" step="1" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="adManagement">هل الخدمة تشمل إدارة وإطلاق الحملات الإعلانية الممولة؟</label>
        <select id="adManagement" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، إدارة وتحسين الحملات الإعلانية</option>
            <option value="no" >لا، نشر وإدارة محتوى فقط</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="communityManagement">الرد على التعليقات والرسائل الخاصة (خدمة العملاء)</label>
        <select id="communityManagement" class="form-control" onchange="calculateTool()">
            <option value="yes" >نعم، ردود يومية ومتابعة التفاعل</option>
            <option value="no" selected>لا، المحتوى والنشر فقط</option>
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
  0 => 'فيديوهات الـ Reels والمونتاج تتطلب جهداً ووقتاً أعلى من التصاميم الثابتة.',
  1 => 'نشر نفس المحتوى على منصات إضافية يترتب عليه تعديل القياسات والكلمات المفتاحية والجدولة.',
  2 => 'إدارة الإعلانات الممولة وخدمة الردود على العملاء هي خدمات قيمة مضافة تُحسب كرسوم شهرية منفصلة.',
),
        'الأسعار لا تشمل الميزانية الإعلانية التي تدفعها الشركة مباشرة للمنصات (Facebook/TikTok/Google).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل الرسوم تشمل الميزانية الإعلانية المدفوعة للمنصات؟',
    'a' => 'لا؛ الميزانية الإعلانية يدفعها العميل ببطاقته مباشرة لمنصات الإعلانات، وأتعاب المسوق تكون مقابل الاستراتيجية والإطلاق والتصاميم والتحسين المستمر.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'design-pricing-calculator',
  1 => 'freelancer-hourly-rate-calculator',
  2 => 'roas-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const posts = Math.max(0, parseInt(document.getElementById('postsPerMonth').value) || 0);
            const reels = Math.max(0, parseInt(document.getElementById('reelsPerMonth').value) || 0);
            const platforms = Math.max(1, parseInt(document.getElementById('platformsCount').value) || 1);
            const ads = document.getElementById('adManagement').value;
            const comm = document.getElementById('communityManagement').value;
            const curr = getSelectedCurrency();

            const postPrice = 20; // سعر إعداد البوست مع الكابشن
            const reelPrice = 45; // سعر كتابة ومونتاج الريل
            const contentBase = (posts * postPrice) + (reels * reelPrice);
            const platformsMultiplier = 1 + ((platforms - 1) * 0.25);
            let total = contentBase * platformsMultiplier;

            if (ads === 'yes') total += 250;
            if (comm === 'yes') total += 150;

            setPrimaryResult(formatMoney(total, curr) + ' / شهرياً', 'الاشتراك الشهري المقترح لإدارة الحسابات');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي المحتوى شهرياً', value: (posts + reels) + ' قطعة محتوى', color: '#3b82f6' },
                { label: 'عدد المنصات المدارة', value: platforms + ' منصات', color: '#10b981' },
                { label: 'تكلفة المحتوى والتصاميم', value: formatMoney(contentBase * platformsMultiplier, curr), color: '#8b5cf6' },
                { label: 'رسوم إدارة الإعلانات والتفاعل', value: formatMoney((ads==='yes'?250:0) + (comm==='yes'?150:0), curr), color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الحزمة الشهرية المقترحة تشمل إعداد ونشر <strong>${posts} بوست</strong> و <strong>${reels} ريلز</strong> على <strong>${platforms} منصات</strong> بتكلفة إجمالية <strong>${formatMoney(total, curr)}</strong> شهرياً.</p>
            `);
        
        saveLastInputs('social-media-pricing-calculator');
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
    restoreLastInputs('social-media-pricing-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>