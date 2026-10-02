<?php
/**
 * أداة: حاسبة المعدل النهائي للمادة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'final-grade-calculator';
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
        <label class="form-label" for="homeworkScore">الواجبات والمشاريع (الدرجة المحققة / الوزن)</label>
        <input type="number" id="homeworkScore" class="form-control" value="18" min="0" max="30" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="homeworkWeight">وزن الواجبات في المادة (%)</label>
        <input type="number" id="homeworkWeight" class="form-control" value="20" min="5" max="50" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="midtermScore">درجة الامتحان النصفي (Midterm)</label>
        <input type="number" id="midtermScore" class="form-control" value="26" min="0" max="40" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="midtermWeight">وزن الامتحان النصفي (%)</label>
        <input type="number" id="midtermWeight" class="form-control" value="30" min="10" max="50" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="finalScore">درجة الامتحان النهائي المتوقعة أو الفعلية</label>
        <input type="number" id="finalScore" class="form-control" value="45" min="0" max="60" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="finalWeight">وزن الامتحان النهائي (%)</label>
        <input type="number" id="finalWeight" class="form-control" value="50" min="20" max="70" step="5"  oninput="calculateTool()">
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
  0 => 'الدرجة الموزونة = (درجة الواجبات × وزنها) + (درجة النصفي × وزنه) + (درجة الفاينل × وزنه).',
  1 => 'التأكد من أن مجموع الأوزان يساوي 100% لضمان صحة النتيجة النهائية.',
),
        'يفترض توزيع درجات قياسي بإجمالي 100 درجة للمقرر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف تؤثر درجات الواجبات على التقدير العام؟',
    'a' => 'الواجبات والمشاريع تمثل عادة درجات مضمونة وسهلة التحصيل تضمن للطالب النجاح حتى لو واجه صعوبة في الامتحان النهائي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'passing-grade-calculator',
  1 => 'target-gpa-calculator',
  2 => 'grade-percentage-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const hwScore = Math.max(0, parseFloat(document.getElementById('homeworkScore').value) || 0);
            const hwWeight = Math.max(5, parseFloat(document.getElementById('homeworkWeight').value) || 20);
            const midScore = Math.max(0, parseFloat(document.getElementById('midtermScore').value) || 0);
            const midWeight = Math.max(10, parseFloat(document.getElementById('midtermWeight').value) || 30);
            const finScore = Math.max(0, parseFloat(document.getElementById('finalScore').value) || 0);
            const finWeight = Math.max(20, parseFloat(document.getElementById('finalWeight').value) || 50);

            // حساب النسبة الموزونة
            const totalScore = (hwScore) + (midScore) + (finScore);
            const totalWeights = hwWeight + midWeight + finWeight;

            let letterGrade = 'F';
            let statusColor = '#ef4444';
            if (totalScore >= 90) { letterGrade = 'A (ممتاز)'; statusColor = '#10b981'; }
            else if (totalScore >= 80) { letterGrade = 'B (جيد جداً)'; statusColor = '#3b82f6'; }
            else if (totalScore >= 70) { letterGrade = 'C (جيد)'; statusColor = '#f59e0b'; }
            else if (totalScore >= 60) { letterGrade = 'D (مقبول)'; statusColor = '#f59e0b'; }

            setPrimaryResult(totalScore.toFixed(1) + ' من 100 (' + letterGrade + ')', 'المعدل النهائي الإجمالي للمادة');
            showResultArea();

            setDetailStats([
                { label: 'التقدير الحرفي المستحق', value: letterGrade, color: statusColor },
                { label: 'مجموع الأوزان الموزونة', value: totalWeights + '%', color: '#3b82f6' },
                { label: 'نقاط أعمال السنة والنصفي', value: (hwScore + midScore).toFixed(1) + ' درجة', color: '#10b981' },
                { label: 'نقاط الامتحان النهائي', value: finScore.toFixed(1) + ' درجة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>مجموعك النهائي في هذه المادة هو <strong>${totalScore.toFixed(1)} من 100</strong> بتقدير <strong>${letterGrade}</strong>.</p>
            `);
        
        saveLastInputs('final-grade-calculator');
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
    restoreLastInputs('final-grade-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>