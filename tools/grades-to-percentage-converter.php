<?php
/**
 * أداة: حاسبة تحويل الدرجات إلى نسبة مئوية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'grades-to-percentage-converter';
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
        <label class="form-label" for="inputGradeVal">الدرجة أو المعدل الحالي</label>
        <input type="number" id="inputGradeVal" class="form-control" value="3.45" min="0"  step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="sourceSystemScale">النظام المصدر للعلامة</label>
        <select id="sourceSystemScale" class="form-control" onchange="calculateTool()">
            <option value="gpa4" selected>معدل جامعي من 4.0 نقاط</option>
            <option value="gpa5" >معدل جامعي من 5.0 نقاط (جامعات المملكة)</option>
            <option value="scale20" >نظام من 20 درجة (دول المغرب العربي وفرنسا)</option>
            <option value="scale10" >نظام من 10 درجات</option>
            <option value="custom" >نظام مخصص بنهاية عظمى محددة</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="customMaxScale">الدرجة العظمى (إذا اخترت مخصص)</label>
        <input type="number" id="customMaxScale" class="form-control" value="100" min="1"  step="1"  oninput="calculateTool()">
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
  0 => 'النسبة المئوية المكافئة = (العلامة المحققة ÷ الحد الأقصى للنظام) × 100.',
  1 => 'تستخدم هذه المعادلة لتسهيل معادلة الشهادات والتقديم على الوظائف والمنح الدراسية بالخارج.',
),
        'يفترض تحويلاً خطياً متناسباً (Linear Conversion).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تختلف معادلة التحويل لبعض برامج الابتعاث؟',
    'a' => 'نعم؛ بعض الجامعات الغربية تستخدم جداول مطابقة غير خطية مبنية على التوزيع التكراري للدرجات (Percentile Rank)، ولكن التحويل الخطي هو المعيار المعتمد عموماً ما لم يُنص على خلاف ذلك.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'grade-percentage-calculator',
  1 => 'cumulative-gpa-calculator',
  2 => 'grade-difference-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const val = Math.max(0, parseFloat(document.getElementById('inputGradeVal').value) || 0);
            const sys = document.getElementById('sourceSystemScale').value;
            const customMax = Math.max(1, parseFloat(document.getElementById('customMaxScale').value) || 100);

            let maxVal = 4.0;
            let percent = 0;

            if (sys === 'gpa4') {
                maxVal = 4.0;
                // معادلة WES القياسية لتحويل GPA 4 إلى نسبة تقريبية
                percent = (val / 4.0) * 100;
            } else if (sys === 'gpa5') {
                maxVal = 5.0;
                // النظام السعودي: النسبة = (المعدل / 5) * 100 أو المعادلة الرسمية
                percent = (val / 5.0) * 100;
            } else if (sys === 'scale20') {
                maxVal = 20.0;
                percent = (val / 20.0) * 100;
            } else if (sys === 'scale10') {
                maxVal = 10.0;
                percent = (val / 10.0) * 100;
            } else {
                maxVal = customMax;
                percent = (val / customMax) * 100;
            }

            percent = Math.min(100, Math.max(0, percent));

            setPrimaryResult(percent.toFixed(2) + '%', 'النسبة المئوية المكافئة');
            showResultArea();

            setDetailStats([
                { label: 'النسبة المئوية الصافية', value: percent.toFixed(2) + '%', color: '#10b981' },
                { label: 'المعدل المكافئ من 4.0', value: ((percent / 100) * 4.0).toFixed(2), color: '#3b82f6' },
                { label: 'المعدل المكافئ من 5.0', value: ((percent / 100) * 5.0).toFixed(2), color: '#8b5cf6' },
                { label: 'النظام المصدر المعتمد', value: val + ' من ' + maxVal, color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>العلامة <strong>${val} من ${maxVal}</strong> تعادل بالضبط <strong>${percent.toFixed(2)}%</strong> كنسبة مئوية عامة للمقارنة والتقديم على المنح والجامعات الدولية.</p>
            `);
        
        saveLastInputs('grades-to-percentage-converter');
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
    restoreLastInputs('grades-to-percentage-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>