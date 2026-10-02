<?php
/**
 * أداة: حاسبة حجم الانفرتر (المحول)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'inverter-size-calculator';
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
        <label class="form-label" for="continuousLoad">الأحمال المستمرة العادية (إضاءة، شاشات، مراوح، كمبيوتر) بالواط</label>
        <input type="number" id="continuousLoad" class="form-control" value="800" min="50"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="inductiveLoad">أحمال محركات ومضخات وثلاجات (لها تيار إقلاع Surge) بالواط</label>
        <input type="number" id="inductiveLoad" class="form-control" value="400" min="0"  step="50"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="surgeMultiplier">معامل تيار بدء التشغيل للمحركات (Surge Multiplier)</label>
        <input type="number" id="surgeMultiplier" class="form-control" value="2.5" min="1.5" max="5.0" step="0.5"  oninput="calculateTool()">
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
  0 => 'المحركات والكمبروسرات (مثل الثلاجات والمضخات ومكيفات الهواء) تسحب عند الإقلاع تياراً يعادل 2.5 إلى 4 أضعاف قدرتها الاسمية لجزء من الثانية.',
  1 => 'يجب دائماً اختيار انفرتر بموجة جيبية نقية (Pure Sine Wave) لحماية الأجهزة الإلكترونية والمحركات من الاحتراق والضجيج.',
),
        'يفترض تشغيل محرك واحد كبير في نفس اللحظة مع بقية الأجهزة المستمرة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفرق بين الموجة الجيبية النقية (Pure Sine Wave) والموجة المعدلة (Modified Sine Wave)؟',
    'a' => 'الموجة النقية تطابق كهرباء الدولة تماماً وتشغل جميع الأجهزة بأمان، بينما الموجة المعدلة تتسبب في سخونة المحركات وضجيج المراوح وتلف الشواحن الذكية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'ups-size-calculator',
  1 => 'battery-count-calculator',
  2 => 'solar-panels-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const cont = Math.max(50, parseFloat(document.getElementById('continuousLoad').value) || 800);
            const ind = Math.max(0, parseFloat(document.getElementById('inductiveLoad').value) || 400);
            const mult = Math.max(1.5, parseFloat(document.getElementById('surgeMultiplier').value) || 2.5);

            const totalContinuousWatts = cont + ind;
            // القدرة القصوى اللحظية
            const surgeWatts = cont + (ind * mult);
            // الحجم المستمر المطلوب مع هامش أمان 25%
            const recommendedContinuousRating = Math.ceil((totalContinuousWatts * 1.25) / 100) * 100;
            // الحجم القياسي بالـ KVA
            const kvaRating = (recommendedContinuousRating / 0.8) / 1000;

            setPrimaryResult(recommendedContinuousRating + ' واط مستمر (' + kvaRating.toFixed(1) + ' KVA)', 'حجم الانفرتر المقترح');
            showResultArea();

            setDetailStats([
                { label: 'إجمالي الأحمال المستمرة العادية', value: totalContinuousWatts + ' واط', color: '#3b82f6' },
                { label: 'تيار الإقلاع اللحظي المتوقع (Surge)', value: Math.ceil(surgeWatts) + ' واط', color: '#ef4444' },
                { label: 'الجهد الموصى به لبنك البطاريات', value: recommendedContinuousRating > 2000 ? '48 فولت' : (recommendedContinuousRating > 1000 ? '24 فولت' : '12 فولت'), color: '#10b981' },
                { label: 'نوع الموجة الموصى به', value: 'موجة جيبية نقية (Pure Sine Wave)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تحتاج إلى انفرتر بقدرة مستمرة <strong>${recommendedContinuousRating} واط</strong> وقدرة إقلاع لحظية لا تقل عن <strong>${Math.ceil(surgeWatts)} واط</strong> لتشغيل محركات الثلاجة والمضخة دون انقطاع أو إعادة تشغيل الجهاز.</p>
            `);
        
        saveLastInputs('inverter-size-calculator');
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
    restoreLastInputs('inverter-size-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>