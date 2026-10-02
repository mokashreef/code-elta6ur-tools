<?php
/**
 * أداة: حاسبة العلامة المطلوبة للوصول لمعدل معين (GPA)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'target-gpa-calculator';
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
        <label class="form-label" for="currentCgpa">المعدل التراكمي الحالي (CGPA)</label>
        <input type="number" id="currentCgpa" class="form-control" value="3.10" min="0" max="5.0" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="completedHours">عدد الساعات المكتسبة السابقة المنجزة</label>
        <input type="number" id="completedHours" class="form-control" value="60" min="1" max="250" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="targetCgpa">المعدل التراكمي المستهدف المطلوب تحقيقه</label>
        <input type="number" id="targetCgpa" class="form-control" value="3.40" min="1" max="5.0" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="nextSemesterHours">عدد الساعات المسجلة في الفصل القادم</label>
        <input type="number" id="nextSemesterHours" class="form-control" value="15" min="3" max="25" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gpaScaleType">نظام المعدل بالجامعة</label>
        <select id="gpaScaleType" class="form-control" onchange="calculateTool()">
            <option value="4" selected>نظام 4.0 نقاط</option>
            <option value="5" >نظام 5.0 نقاط (النظام السعودي المعتمد)</option>
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
  0 => 'المعدل الفصلي المطلوب = ((المعدل المستهدف × إجمالي الساعات الجديدة) - (المعدل الحالي × الساعات السابقة)) ÷ ساعات الفصل القادم.',
  1 => 'كلما زادت الساعات المنجزة مسبقاً، كلما تطلب رفع المعدل مجهوداً أكبر وأصبح تحركه أبطأ.',
),
        'يفترض عدم حذف أو رسوب في أي من مواد الفصل المسجلة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما العمل إذا كان المعدل المطلوب أكبر من الحد الأقصى (مثلاً أكبر من 4.0)؟',
    'a' => 'هذا يعني أن عدد الساعات المسجلة في الفصل غير كافٍ لرفع المعدل لهذه الدرجة، والحل هو تقسيم الهدف على فصلين أو ثلاثة فصول دراسية قادمة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'cumulative-gpa-calculator',
  1 => 'next-semester-gpa-calculator',
  2 => 'grade-percentage-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const currentGpa = Math.max(0, parseFloat(document.getElementById('currentCgpa').value) || 3.10);
            const prevHours = Math.max(1, parseFloat(document.getElementById('completedHours').value) || 60);
            const targetGpa = Math.max(1, parseFloat(document.getElementById('targetCgpa').value) || 3.40);
            const nextHours = Math.max(3, parseFloat(document.getElementById('nextSemesterHours').value) || 15);
            const scale = parseFloat(document.getElementById('gpaScaleType').value) || 4;

            const totalHoursAfter = prevHours + nextHours;
            const targetTotalPoints = targetGpa * totalHoursAfter;
            const currentTotalPoints = currentGpa * prevHours;
            const neededSemesterPoints = targetTotalPoints - currentTotalPoints;
            const requiredSemesterGpa = neededSemesterPoints / nextHours;

            let status = 'هدف ممكن ومتاح بالاجتهاد ✅';
            let color = '#10b981';
            if (requiredSemesterGpa > scale) {
                status = 'مستحيل في فصل واحد! تحتاج لفصول إضافية لرفع المعدل ❌';
                color = '#ef4444';
            } else if (requiredSemesterGpa >= (scale * 0.9)) {
                status = 'يتطلب معدل امتياز مرتفع A+ في جميع مواد الفصل 🌟';
                color = '#f59e0b';
            }

            setPrimaryResult(requiredSemesterGpa > scale ? 'غير ممكن في فصل واحد' : requiredSemesterGpa.toFixed(2) + ' / ' + scale, 'المعدل الفصلي المطلوب في الفصل القادم');
            showResultArea();

            setDetailStats([
                { label: 'المعدل الفصلي المطلوب بالضبط', value: requiredSemesterGpa.toFixed(2) + ' من ' + scale, color: '#3b82f6' },
                { label: 'إمكانية التحقيق في هذا الفصل', value: status, color: color },
                { label: 'إجمالي الساعات الكلية بعد الفصل', value: totalHoursAfter + ' ساعة', color: '#8b5cf6' },
                { label: 'النقاط التراكمية الإضافية المطلوبة', value: neededSemesterPoints.toFixed(1) + ' نقطة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لرفع معدلك التراكمي من <strong>${currentGpa}</strong> إلى <strong>${targetGpa}</strong> خلال <strong>${nextHours} ساعة</strong>، يجب أن تحقق معدلاً فصلياً لا يقل عن <strong>${requiredSemesterGpa.toFixed(2)} من ${scale}</strong>.</p>
            `);
        
        saveLastInputs('target-gpa-calculator');
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
    restoreLastInputs('target-gpa-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>