<?php
/**
 * أداة: حاسبة الدخل المطلوب للعيش في مدينة ومستوى الرفاهية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'city-income-requirement-calculator';
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
        <label class="form-label" for="targetCityTier">مستوى غلاء المدينة</label>
        <select id="targetCityTier" class="form-control" onchange="calculateTool()">
            <option value="capital_prime" selected>عاصمة رئيسية كبرى (الرياض، دبي، الدوحة) - تكاليف مرتفعة</option>
            <option value="large_city" >مدينة رئيسية كبيرة (جدة، أبوظبي، القاهرة الجديدة، عمّان)</option>
            <option value="medium_city" >مدينة متوسطة / محافظة هادئة - تكاليف معتدلة اقتصادية</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="lifestylePreference">مستوى ونمط المعيشة المطلوب</label>
        <select id="lifestylePreference" class="form-control" onchange="calculateTool()">
            <option value="basic_economy" >اقتصادي بسيط (سكن متواضع، طهي منزلي، مواصلات اقتصادية)</option>
            <option value="comfortable" selected>متوسط ومريح (شقة ممتازة في حي جيد، ترفيه أسبوعي، سيارة جيدة)</option>
            <option value="luxury_plus" >مرفه وفاخر (فيلا/شقة فخمة، مدارس دولية، سفر سنوي، مطاعم راقية)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="familyMembersCountCity">عدد أفراد الأسرة المقيمين في المدينة</label>
        <input type="number" id="familyMembersCountCity" class="form-control" value="3" min="1" max="10" step="1"  oninput="calculateTool()">
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
  0 => 'السكن يمثل العنصر الأكثر تبايناً في تكلفة المعيشة بين العواصم والمدن الأخرى.',
  1 => 'مع كل فرد إضافي في الأسرة تزيد التكلفة المعيشية بمعدل 40% إلى 50% من تكلفة الفرد البالغ.',
),
        'التقديرات مبنية على دراسات تكلفة المعيشة ومؤشرات غلاء المدن في المنطقة العربية لعام 2025/2026.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أضمن عدم استنزاف راتبي في المدن الكبرى؟',
    'a' => 'احرص على ألا يتجاوز إيجار السكن ثلث دخلك الشهري حتى لو اضطررت للسكن في حي أبعد قليلاً عن مركز المدينة بالقرب من خطوط المترو أو الطرق السريعة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cost-of-living-calculator',
  1 => 'is-salary-enough-calculator',
  2 => 'salary-division-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const tier = document.getElementById('targetCityTier').value;
            const lifestyle = document.getElementById('lifestylePreference').value;
            const members = Math.max(1, parseInt(document.getElementById('familyMembersCountCity').value) || 3);
            const curr = getSelectedCurrency();

            // دخل أساسي للفرد حسب تصنيف المدينة
            let baseSingleIncome = 4500;
            if (tier === 'large_city') baseSingleIncome = 3500;
            if (tier === 'medium_city') baseSingleIncome = 2500;

            // مضاعف نمط المعيشة
            let lifeMult = 1.6; // مريح
            if (lifestyle === 'basic_economy') lifeMult = 1.0;
            if (lifestyle === 'luxury_plus') lifeMult = 2.8;

            // أثر أفراد الأسرة
            const familyFactor = 1 + ((members - 1) * 0.45);
            const requiredMonthlyIncome = baseSingleIncome * lifeMult * familyFactor;
            const requiredAnnualIncome = requiredMonthlyIncome * 12;

            setPrimaryResult(formatMoney(requiredMonthlyIncome, curr) + ' شهرياً', 'الدخل الصافي الموصى به شهرياً');
            showResultArea();

            setDetailStats([
                { label: 'الدخل السنوي المطلوب', value: formatMoney(requiredAnnualIncome, curr), color: '#3b82f6' },
                { label: 'مخصص السكن التقريبي (30%)', value: formatMoney(requiredMonthlyIncome * 0.30, curr), color: '#10b981' },
                { label: 'مخصص الادخار والأمان المالي (20%)', value: formatMoney(requiredMonthlyIncome * 0.20, curr), color: '#f59e0b' },
                { label: 'مخصص المعيشة والخدمات والترفيه (50%)', value: formatMoney(requiredMonthlyIncome * 0.50, curr), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>للعيش بنمط <strong>${lifestyle === 'comfortable' ? 'مريح ومستقر' : (lifestyle === 'luxury_plus' ? 'مرفه وفاخر' : 'اقتصادي')}</strong> لأسرة من <strong>${members} أفراد</strong> في <strong>${tier === 'capital_prime' ? 'عاصمة كبرى' : 'مدينة رئيسية'}</strong>، يوصى بدخل صافٍ لا يقل عن <strong>${formatMoney(requiredMonthlyIncome, curr)} شهرياً</strong> لتغطية السكن والالتزامات مع ادخار 20% للأمان المالي.</p>
            `);
        
        saveLastInputs('city-income-requirement-calculator');
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
    restoreLastInputs('city-income-requirement-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>