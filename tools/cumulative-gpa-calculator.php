<?php
/**
 * أداة: حاسبة المعدل التراكمي الشاملة (GPA Calculator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'cumulative-gpa-calculator';
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
        <label class="form-label" for="previousGpa">المعدل التراكمي السابق (اتركه 0 إذا كان الفصل الأول)</label>
        <input type="number" id="previousGpa" class="form-control" value="3.25" min="0" max="5.0" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="previousHours">عدد الساعات المكتسبة السابقة</label>
        <input type="number" id="previousHours" class="form-control" value="45" min="0" max="250" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentSemGpa">المعدل الفصلي للفصل الحالي</label>
        <input type="number" id="currentSemGpa" class="form-control" value="3.80" min="0" max="5.0" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="currentSemHours">عدد ساعات الفصل الحالي</label>
        <input type="number" id="currentSemHours" class="form-control" value="15" min="1" max="25" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gpaMaxSystem">النظام المعتمد لمعدل جامعتك</label>
        <select id="gpaMaxSystem" class="form-control" onchange="calculateTool()">
            <option value="4" selected>من 4.0 نقاط</option>
            <option value="5" >من 5.0 نقاط</option>
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
  0 => 'المعدل التراكمي الجديد = (مجموع النقاط السابقة + نقاط الفصل الحالي) ÷ إجمالي الساعات.',
  1 => 'نقاط الفصل = المعدل الفصلي × عدد ساعات الفصل.',
),
        'يفترض عدم إعادة مواد سابقة كانت محسوبة مسبقاً في الساعات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف تؤثر المادة المعادة على المعدل التراكمي؟',
    'a' => 'عند إعادة مادة، يُحذف التقدير القديم من احتساب المعدل في معظم الجامعات ويحل محله التقدير الجديد، مما يعطي قفزة إيجابية وسريعة للمعدل التراكمي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'target-gpa-calculator',
  1 => 'next-semester-gpa-calculator',
  2 => 'grade-percentage-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const prevGpa = Math.max(0, parseFloat(document.getElementById('previousGpa').value) || 0);
            const prevHours = Math.max(0, parseFloat(document.getElementById('previousHours').value) || 0);
            const semGpa = Math.max(0, parseFloat(document.getElementById('currentSemGpa').value) || 3.80);
            const semHours = Math.max(1, parseFloat(document.getElementById('currentSemHours').value) || 15);
            const maxScale = parseFloat(document.getElementById('gpaMaxSystem').value) || 4;

            const totalHours = prevHours + semHours;
            const prevPoints = prevGpa * prevHours;
            const semPoints = semGpa * semHours;
            const newCgpa = (prevPoints + semPoints) / totalHours;
            const gpaDiff = newCgpa - prevGpa;

            let changeText = 'ثابت';
            let changeColor = '#3b82f6';
            if (gpaDiff > 0) { changeText = 'ارتفاع بمقدار +' + gpaDiff.toFixed(2) + ' 📈'; changeColor = '#10b981'; }
            if (gpaDiff < 0) { changeText = 'انخفاض بمقدار ' + gpaDiff.toFixed(2) + ' 📉'; changeColor = '#ef4444'; }

            setPrimaryResult(newCgpa.toFixed(2) + ' من ' + maxScale, 'المعدل التراكمي الجديد (New CGPA)');
            showResultArea();

            setDetailStats([
                { label: 'المعدل التراكمي الجديد', value: newCgpa.toFixed(2) + ' / ' + maxScale, color: '#10b981' },
                { label: 'حركة وتغير المعدل', value: changeText, color: changeColor },
                { label: 'إجمالي الساعات المكتسبة الكلية', value: totalHours + ' ساعة معتمدة', color: '#3b82f6' },
                { label: 'النسبة المئوية التقديرية المكافئة', value: ((newCgpa / maxScale) * 100).toFixed(1) + '%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بعد إنجاز <strong>${semHours} ساعات</strong> بمعدل فصلي <strong>${semGpa}</strong>، أصبح معدلك التراكمي <strong>${newCgpa.toFixed(2)} من ${maxScale}</strong> بإجمالي <strong>${totalHours} ساعة مكتسبة</strong> (${changeText}).</p>
            `);
        
        saveLastInputs('cumulative-gpa-calculator');
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
    restoreLastInputs('cumulative-gpa-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>