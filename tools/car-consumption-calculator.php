<?php
/**
 * أداة: حاسبة استهلاك السيارة (لتر/100 كم و كم/لتر)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'car-consumption-calculator';
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
    <div class="form-group">
        <label class="form-label" for="distanceDrivenKm">المسافة المقطوعة بالرحلة (كم)</label>
        <input type="number" id="distanceDrivenKm" class="form-control" value="420" min="10"  step="10"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fuelUsedLiters">كمية الوقود المستهلكة لإعادة ملء التانكي (باللتر)</label>
        <input type="number" id="fuelUsedLiters" class="form-control" value="35" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'معدل الاستهلاك (لتر/100 كم) = (اللترات المستهلكة ÷ الكيلومترات المقطوعة) × 100.',
  1 => 'المعدل المقلوب (كم/لتر) = الكيلومترات المقطوعة ÷ اللترات المستهلكة.',
  2 => 'كلما قل رقم (لتر/100 كم) كان استهلاك السيارة أفضل وأكثر توفيراً.',
),
        'يفترض قياس دقيق بإعادة تعبئة التانكي بالكامل من نفس مضخة الوقود.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الاستهلاك المعتبر اقتصادياً للسيارات السيدان؟',
    'a' => 'السيارات التي تستهلك أقل من 6.5 إلى 7.5 لتر لكل 100 كم (أو تقطع أكثر من 14 إلى 16 كم لكل لتر) تصنف كسيارات موفرة وممتازة لاستهلاك الوقود.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'fuel-by-distance-calculator',
  1 => 'distance-fuel-calculator',
  2 => 'gas-vs-ev-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const km = Math.max(10, parseFloat(document.getElementById('distanceDrivenKm').value) || 420);
            const liters = Math.max(1, parseFloat(document.getElementById('fuelUsedLiters').value) || 35);

            const litersPer100Km = (liters / km) * 100;
            const kmPerLiter = km / liters;

            let rating = 'اقتصادي وممتاز جداً  ممتاز ✅';
            let color = '#10b981';
            if (litersPer100Km > 8.5 && litersPer100Km <= 12) { rating = 'استهلاك متوسط طبيعي ⚠️'; color = '#3b82f6'; }
            if (litersPer100Km > 12) { rating = 'استهلاك مرتفع (سيارة شرهة للوقود) ❌'; color = '#ef4444'; }

            setPrimaryResult(litersPer100Km.toFixed(1) + ' لتر / 100 كم (' + kmPerLiter.toFixed(1) + ' كم / لتر)', 'معدل استهلاك السيارة الفعلي');
            showResultArea();

            setDetailStats([
                { label: 'الاستهلاك القياسي (لتر لكل 100 كم)', value: litersPer100Km.toFixed(2) + ' L/100km', color: '#3b82f6' },
                { label: 'المسافة المقطوعة لكل لتر واحد', value: kmPerLiter.toFixed(2) + ' كم / لتر', color: '#10b981' },
                { label: 'تقييم كفاءة استهلاك الوقود', value: rating, color: color },
                { label: 'المسافة المقطوعة بالتجربة', value: km + ' كم', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تستهلك سيارتك فعلياً <strong>${litersPer100Km.toFixed(1)} لتر لكل 100 كم</strong>، أي أن كل لتر وقود يقطع مسافة <strong>${kmPerLiter.toFixed(1)} كم</strong> - تقييم الأداء: <strong>${rating}</strong>.</p>
            `);
        
        saveLastInputs('car-consumption-calculator');
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
    restoreLastInputs('car-consumption-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>