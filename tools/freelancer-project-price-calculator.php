<?php
/**
 * أداة: حاسبة سعر المشروع للفريلانسر
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'freelancer-project-price-calculator';
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
    <?php renderCurrencySelector('calcCurrency', 'SAR', 'العملة المفضلة للنتائج'); ?>
    <div class="form-group">
        <label class="form-label" for="estimatedHours">عدد الساعات المقدرة لتنفيذ المشروع</label>
        <input type="number" id="estimatedHours" class="form-control" value="40" min="1"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="hourlyRate">سعر ساعة العمل الخاصة بك</label>
        <input type="number" id="hourlyRate" class="form-control" value="35" min="1"  step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="revisionsBuffer">هامش التعديلات والمراجعات غير المتوقعة (%)</label>
        <input type="number" id="revisionsBuffer" class="form-control" value="20" min="0" max="100" step="5" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="directExpenses">مصاريف مباشرة للمشروع (خطوط، إضافات، صور مدفوعة)</label>
        <input type="number" id="directExpenses" class="form-control" value="50" min="0"  step="10" oninput="calculateTool()">
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
  0 => 'سعر العمل الأساسي = ساعات العمل المقدرة × سعر ساعتك.',
  1 => 'مخصص التعديلات يحميك من طلبات العميل الإضافية وتوسيع نطاق العمل (Scope Creep).',
  2 => 'السعر الإجمالي = سعر العمل الأساسي + مخصص التعديلات + التكاليف المباشرة.',
),
        'يُوصى دائماً بأخذ دفعة مقدمة 50% قبل البدء، و50% عند التسليم النهائي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل أخبر العميل بعدد ساعاتي أم أقدم سعراً ثابتاً؟',
    'a' => 'يُفضل دائماً تقديم سعر ثابت للمشروع (Fixed Price) مع تحديد نطاق العمل وعدد جولات التعديل، حيث يشعر العميل بالأمان ولا يشغل نفسه بعدد ساعاتك.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'freelancer-hourly-rate-calculator',
  1 => 'project-hours-calculator',
  2 => 'proposal-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const hours = Math.max(1, parseFloat(document.getElementById('estimatedHours').value) || 1);
            const rate = Math.max(1, parseFloat(document.getElementById('hourlyRate').value) || 1);
            const buffer = Math.max(0, parseFloat(document.getElementById('revisionsBuffer').value) || 0) / 100;
            const exp = Math.max(0, parseFloat(document.getElementById('directExpenses').value) || 0);
            const curr = getSelectedCurrency();

            const baseWorkCost = hours * rate;
            const bufferCost = baseWorkCost * buffer;
            const totalProjectPrice = baseWorkCost + bufferCost + exp;

            setPrimaryResult(formatMoney(totalProjectPrice, curr), 'سعر عرض السعر المقترح للمشروع');
            showResultArea();

            setDetailStats([
                { label: 'أجر ساعات التنفيذ الأساسية', value: formatMoney(baseWorkCost, curr), color: '#3b82f6' },
                { label: 'مخصص التعديلات والاجتماعات (' + (buffer * 100) + '%)', value: formatMoney(bufferCost, curr), color: '#f59e0b' },
                { label: 'المصاريف المباشرة المشتراة', value: formatMoney(exp, curr), color: '#8b5cf6' },
                { label: 'دفعة البداية الموصى بها (50%)', value: formatMoney(totalProjectPrice / 2, curr), color: '#10b981' }
            ]);

            setResultContent(`
                <p>بناءً على <strong>${hours} ساعة عمل</strong> بمعدل <strong>${formatMoney(rate, curr)}</strong> للساعة، مع احتساب هامش أمان للتعديلات ومصاريف الأدوات، يكون السعر العادل للمشروع <strong>${formatMoney(totalProjectPrice, curr)}</strong>.</p>
            `);
        
        saveLastInputs('freelancer-project-price-calculator');
    } catch (e) {
        console.error('Calculation error:', e);
    }
}

function resetToolInputs() {
    document.querySelectorAll('.tool-card input').forEach(el => {
        if (el.defaultValue !== undefined) el.value = el.defaultValue;
    });
    calculateTool();
}

function onCurrencyChange() {
    calculateTool();
}

// تنفيذ الحساب تلقائياً عند تحميل الصفحة واسترجاع المدخلات المحفوظة
document.addEventListener('DOMContentLoaded', () => {
    restoreLastInputs('freelancer-project-price-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>