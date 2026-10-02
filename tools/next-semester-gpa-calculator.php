<?php
/**
 * أداة: حاسبة المعدل المطلوب في الفصل القادم (رفع الإنذار الأكاديمي)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'next-semester-gpa-calculator';
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
        <label class="form-label" for="currentWarningGpa">المعدل التراكمي الحالي (تحت الإنذار)</label>
        <input type="number" id="currentWarningGpa" class="form-control" value="1.85" min="0" max="4.0" step="0.01"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="clearedHoursWarning">الساعات المنجزة حتى الآن</label>
        <input type="number" id="clearedHoursWarning" class="form-control" value="32" min="1" max="200" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="requiredClearGpa">الحد الأدنى لرفع الإنذار في جامعتك (المعتاد 2.00 من 4.0)</label>
        <input type="number" id="requiredClearGpa" class="form-control" value="2.00" min="1.5" max="3.0" step="0.05"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="registeredHoursNext">عدد الساعات التي ستسجلها في الفصل القادم</label>
        <input type="number" id="registeredHoursNext" class="form-control" value="14" min="3" max="22" step="1"  oninput="calculateTool()">
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
  0 => 'الإنذار الأكاديمي يُرفع رسمياً بمجرد وصول المعدل التراكمي إلى 2.00 من 4.00 (أو 2.75 من 5.00) في معظم اللوائح الجامعية.',
  1 => 'إعادة دراسة المواد التي رسبت فيها سابقاً (F) أو حصلت فيها على (D) هي أسرع وسيلة قانونية لرفع المعدل لأنها تمحو النقاط السلبية القديمة.',
),
        'يفترض عدم الحصول على أي إنذار إضافي أثناء الفصل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يحدث إذا لم أرفع الإنذار في الفصل القادم؟',
    'a' => 'تمنح معظم الجامعات فرصة إنذار ثانٍ أو ثالث قبل اتخاذ قرار طي القيد أو الفصل الأكاديمي، ويمكن للطالب تقديم التماس للجنة الشؤون الأكاديمية لتمديد فرصة استثنائية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'target-gpa-calculator',
  1 => 'cumulative-gpa-calculator',
  2 => 'graduation-hours-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const current = Math.max(0, parseFloat(document.getElementById('currentWarningGpa').value) || 1.85);
            const pastHours = Math.max(1, parseFloat(document.getElementById('clearedHoursWarning').value) || 32);
            const target = Math.max(1.5, parseFloat(document.getElementById('requiredClearGpa').value) || 2.00);
            const nextHours = Math.max(3, parseFloat(document.getElementById('registeredHoursNext').value) || 14);

            const totalHours = pastHours + nextHours;
            const requiredPoints = target * totalHours;
            const currentPoints = current * pastHours;
            const neededSemesterPoints = requiredPoints - currentPoints;
            const neededGpa = neededSemesterPoints / nextHours;

            let possible = true;
            let status = 'ممكن تماماً ويمكنك رفع الإنذار بسهولة بإذن الله ✅';
            let color = '#10b981';

            if (neededGpa > 4.0) {
                possible = false;
                status = 'غير كافٍ في فصل واحد! تحتاج لتسجيل ساعات أكثر أو إعادة مواد سابقة ⚠️';
                color = '#ef4444';
            } else if (neededGpa > 3.0) {
                status = 'يحتاج لمعدل جيد جداً إلى ممتاز (B+ أو أعلى) 🎯';
                color = '#f59e0b';
            }

            setPrimaryResult(neededGpa > 4.0 ? 'غير ممكن بفصل واحد' : neededGpa.toFixed(2) + ' / 4.0', 'المعدل الفصلي المطلوب لرفع الإنذار');
            showResultArea();

            setDetailStats([
                { label: 'المعدل الفصلي المطلوب في الفصل القادم', value: neededGpa.toFixed(2) + ' من 4.0', color: '#3b82f6' },
                { label: 'حالة إمكانية رفع الإنذار', value: status, color: color },
                { label: 'التقدير المكافئ المطلوب في المواد', value: neededGpa <= 2.3 ? 'تقدير C+ في كل مادة' : (neededGpa <= 3.0 ? 'تقدير B في كل مادة' : 'تقدير A/B+'), color: '#8b5cf6' },
                { label: 'الساعات التراكمية بعد إنهاء الفصل', value: totalHours + ' ساعة', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لرفع معدلك التراكمي إلى <strong>${target.toFixed(2)}</strong> والتخلص من الإنذار الأكاديمي، تحتاج لتحقيق معدل فصلي لا يقل عن <strong>${neededGpa.toFixed(2)} من 4.0</strong> في الساعات المسجلة (${nextHours} ساعة).</p>
            `);
        
        saveLastInputs('next-semester-gpa-calculator');
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
    restoreLastInputs('next-semester-gpa-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>