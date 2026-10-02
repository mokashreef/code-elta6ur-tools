<?php
/**
 * أداة: حاسبة القدرة المطلوبة للمولد (KVA)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'generator-capacity-calculator';
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
        <label class="form-label" for="runningWattsApp">مجموع قدرات الأجهزة العادية (إنارة، شاشات، كمبيوتر) بالواط</label>
        <input type="number" id="runningWattsApp" class="form-control" value="1200" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="motorsWattsApp">مجموع قدرات المحركات والمكيفات والثلاجات بالواط</label>
        <input type="number" id="motorsWattsApp" class="form-control" value="2200" min="0"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="motorSurgeFactor">معامل بدء التشغيل للمحرك الأكبر (Starting Surge)</label>
        <input type="number" id="motorSurgeFactor" class="form-control" value="2.5" min="1.5" max="4.0" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="futureExpansion">هامش التوسع الاحتياطي (%)- عادة 20%</label>
        <input type="number" id="futureExpansion" class="form-control" value="20" min="10" max="50" step="5"  oninput="calculateTool()">
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
  0 => 'القدرة بالـ KVA = القدرة بالكيلوواط (kW) ÷ 0.8 (معامل القدرة القياسي للمولدات).',
  1 => 'مولدات الكهرباء لا يجب أن تعمل باستمرار عند 100% من طاقتها؛ بل بنسبة 75% إلى 80% لضمان عمر أطول وتفادي الانطفاء عند بدء تشغيل الأجهزة.',
),
        'يفترض تشغيل محرك تكييف أو ثلاجة واحدة في لحظة الإقلاع ذاتها.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا ينطفئ المولد فجأة عند تشغيل الثلاجة أو الغطاس؟',
    'a' => 'لأن تيار إقلاع المحرك يسحب للحظة طاقة تعادل 3 أضعاف طاقة المولد، وإذا لم تكن قدرة المولد كافية يهبط الجهد الكهربائي ويفصل قاطع الحماية (Overload).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'generator-fuel-calculator',
  1 => 'generator-cost-calculator',
  2 => 'ups-size-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const running = Math.max(50, parseFloat(document.getElementById('runningWattsApp').value) || 1200);
            const motors = Math.max(0, parseFloat(document.getElementById('motorsWattsApp').value) || 2200);
            const surge = Math.max(1.5, parseFloat(document.getElementById('motorSurgeFactor').value) || 2.5);
            const margin = Math.max(10, parseFloat(document.getElementById('futureExpansion').value) || 20) / 100;

            const totalContinuousWatts = (running + motors) * (1 + margin);
            // القدرة القصوى المطلوبة لحظة إقلاع المحركات
            const peakSurgeWatts = running + (motors * surge);
            // تحويل الواط إلى KVA (Power Factor للمولدات عادة 0.8)
            const kvaContinuous = (totalContinuousWatts / 0.8) / 1000;
            const kvaPeak = (peakSurgeWatts / 0.8) / 1000;

            const requiredKva = Math.max(kvaContinuous, kvaPeak * 0.8);
            const standardKvaSizes = [3.5, 5, 7.5, 10, 12.5, 15, 20, 25, 30, 45, 60, 100];
            const recommendedKva = standardKvaSizes.find(s => s >= requiredKva) || Math.ceil(requiredKva);

            setPrimaryResult(recommendedKva + ' KVA', 'قدرة المولد القياسي الموصى به');
            showResultArea();

            setDetailStats([
                { label: 'القدرة بالواط المستمر (kW)', value: (recommendedKva * 0.8).toFixed(1) + ' kW (' + (recommendedKva * 800) + ' واط)', color: '#3b82f6' },
                { label: 'أقصى حمل لحظي عند الإقلاع', value: Math.ceil(peakSurgeWatts) + ' واط', color: '#ef4444' },
                { label: 'الأحمال المستمرة المطلوبة', value: Math.ceil(running + motors) + ' واط', color: '#10b981' },
                { label: 'معامل القدرة المعتمد (PF)', value: '0.8 Cosφ', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتشغيل هذه الأحمال مع تيار إقلاع المحركات وأجهزة التكييف بأمان، تحتاج إلى مولد كهربائي بقدرة لا تقل عن <strong>${recommendedKva} KVA</strong> (ما يعادل <strong>${(recommendedKva * 0.8).toFixed(1)} كيلوواط صافي</strong>).</p>
            `);
        
        saveLastInputs('generator-capacity-calculator');
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
    restoreLastInputs('generator-capacity-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>