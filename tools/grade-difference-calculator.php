<?php
/**
 * أداة: حاسبة الفرق بين علامتين ونسبة التطور
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grade-difference-calculator';
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
        <label class="form-label" for="previousScoreInput">العلامة السابقة (الامتحان الأول أو الفصل الماضي)</label>
        <input type="number" id="previousScoreInput" class="form-control" value="72" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentScoreInput">العلامة الحالية (الامتحان الثاني أو الفصل الحالي)</label>
        <input type="number" id="currentScoreInput" class="form-control" value="86" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="examTotalPoints">الدرجة العظمى للامتحان</label>
        <input type="number" id="examTotalPoints" class="form-control" value="100" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'فارق الدرجات = الدرجة الحالية - الدرجة السابقة.',
  1 => 'نسبة التطور = ((الدرجة الحالية - السابقة) ÷ السابقة) × 100.',
),
        'يفترض أن الامتحانين من نفس الدرجة العظمى والمستوى التقييمي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحلل سبب تراجع الدرجات في مادة معينة؟',
    'a' => 'راجع ورقة الإجابة لتحديد نوع الأخطاء: هل هي ناتجة عن سوء فهم المفاهيم، أم عدم حفظ القوانين، أم التسرع في الحسابات وقراءة الأسئلة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'grade-percentage-calculator',
  1 => 'target-gpa-calculator',
  2 => 'grades-to-percentage-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const prev = Math.max(0, parseFloat(document.getElementById('previousScoreInput').value) || 0);
            const curr = Math.max(0, parseFloat(document.getElementById('currentScoreInput').value) || 0);
            const max = Math.max(1, parseFloat(document.getElementById('examTotalPoints').value) || 100);

            const pointsDiff = curr - prev;
            const percentDiffOnExam = ((curr - prev) / max) * 100;
            const growthRate = prev > 0 ? ((curr - prev) / prev) * 100 : 0;

            let status = 'تحسن وتطور ممتاز ملحوظ 📈';
            let color = '#10b981';
            if (pointsDiff < 0) { status = 'تراجع في الدرجات 📉'; color = '#ef4444'; }
            if (pointsDiff === 0) { status = 'ثبات تام في المستوى ⚖️'; color = '#3b82f6'; }

            setPrimaryResult((pointsDiff >= 0 ? '+' : '') + pointsDiff.toFixed(1) + ' درجة (' + (growthRate >= 0 ? '+' : '') + growthRate.toFixed(1) + '%)', 'فارق الدرجات ونسبة التغير');
            showResultArea();

            setDetailStats([
                { label: 'فارق الدرجات النقطي', value: (pointsDiff >= 0 ? '+' : '') + pointsDiff.toFixed(1) + ' درجة', color: color },
                { label: 'نسبة التطور مقارنة بالسابقة', value: (growthRate >= 0 ? '+' : '') + growthRate.toFixed(1) + '%', color: color },
                { label: 'الفارق المئوي من مجموع الامتحان', value: (percentDiffOnExam >= 0 ? '+' : '') + percentDiffOnExam.toFixed(1) + '%', color: '#3b82f6' },
                { label: 'تقييم الحركة الأكاديمية', value: status, color: color }
            ]);

            setResultContent(`
                <p>تغيرت درجتك من <strong>${prev}</strong> إلى <strong>${curr}</strong> بفارق <strong>${pointsDiff >= 0 ? '+' : ''}${pointsDiff.toFixed(1)} درجة</strong>، بنسبة تطور <strong>${growthRate.toFixed(1)}%</strong> - ${status}.</p>
            `);
        
        saveLastInputs('grade-difference-calculator');
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
    restoreLastInputs('grade-difference-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>