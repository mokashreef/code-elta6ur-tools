<?php
/**
 * أداة: حاسبة نسبة إنجاز المنهج الدراسي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'curriculum-progress-calculator';
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
        <label class="form-label" for="completedUnits">عدد الدروس / الفصول التي تم الانتهاء منها ودراستها</label>
        <input type="number" id="completedUnits" class="form-control" value="18" min="0"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="totalUnitsCurriculum">إجمالي عدد دروس أو فصول المنهج بالكامل</label>
        <input type="number" id="totalUnitsCurriculum" class="form-control" value="30" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'نسبة الإنجاز = (الدروس المنتهية ÷ إجمالي الدروس) × 100.',
  1 => 'متابعة شريط التقدم المرئي تحفز إفراز الدوبامين وتعزز الحافز النفسي للاستمرار في الإنجاز.',
),
        'يفترض تساوي الوزن النسبي بين الدروس.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أتعامل مع الشعور بالإحباط في منتصف المنهج؟',
    'a' => 'احتفل بالإنجاز الذي حققته بالفعل (نصف الكوب الممتلئ)، وقسم الدروس المتبقية إلى حزم أسبوعية صغيرة لتحقيق انتصارات سريعة تعيد لك الحماس.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'remaining-study-time-calculator',
  1 => 'syllabus-finish-time-calculator',
  2 => 'daily-study-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const done = Math.max(0, parseFloat(document.getElementById('completedUnits').value) || 0);
            const total = Math.max(1, parseFloat(document.getElementById('totalUnitsCurriculum').value) || 30);

            const percent = Math.min(100, (done / total) * 100);
            const remaining = Math.max(0, total - done);

            let status = 'بداية مشجعة، استمر!';
            let color = '#3b82f6';
            if (percent >= 100) { status = 'تم إنجاز المنهج كاملاً! مبروك 🏆'; color = '#10b981'; }
            else if (percent >= 75) { status = 'على وشك الختام، خط النهاية قريب جداً 🎯'; color = '#10b981'; }
            else if (percent >= 50) { status = 'تجاوزت نصف الطريق بنجاح ⚡'; color = '#f59e0b'; }

            setPrimaryResult(percent.toFixed(1) + '% إنجاز المنهج', 'نسبة تقدمك في المنهج');
            showResultArea();

            setDetailStats([
                { label: 'النسبة المكتملة', value: percent.toFixed(1) + '%', color: color },
                { label: 'الدروس أو الفصول المتبقية', value: remaining + ' درساً', color: '#ef4444' },
                { label: 'الدروس المنجزة بنجاح', value: done + ' درساً', color: '#10b981' },
                { label: 'تقييم مرحلة الإنجاز', value: status, color: color }
            ]);

            setResultContent(`
                <div style="background:rgba(255,255,255,0.05);border-radius:10px;padding:4px;margin:1rem 0">
                    <div style="width:${percent}%;height:18px;background:linear-gradient(90deg, #6c63ff, #00d4ff);border-radius:8px;transition:width 0.5s ease"></div>
                </div>
                <p>أنجزت <strong>${done} من أصل ${total} درساً</strong> بنسبة <strong>${percent.toFixed(1)}%</strong>، ومتبقي لك <strong>${remaining} درساً</strong> لإتمام المقرر بالكامل - ${status}.</p>
            `);
        
        saveLastInputs('curriculum-progress-calculator');
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
    restoreLastInputs('curriculum-progress-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>