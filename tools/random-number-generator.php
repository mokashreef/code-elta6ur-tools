<?php
/**
 * أداة: توليد أرقام عشوائية ضمن مجال معين (Random Number)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'random-number-generator';
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
        <label class="form-label" for="minNumberRange">الحد الأدنى للمجال (Min)</label>
        <input type="number" id="minNumberRange" class="form-control" value="1" min="-1000000"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="maxNumberRange">الحد الأقصى للمجال (Max)</label>
        <input type="number" id="maxNumberRange" class="form-control" value="100" min="-1000000"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="numbersCountToGen">كم رقماً تريد توليده؟</label>
        <input type="number" id="numbersCountToGen" class="form-control" value="5" min="1" max="500" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="allowDuplicatesNumbers">السماح بتكرار الأرقام في السحب</label>
        <select id="allowDuplicatesNumbers" class="form-control" onchange="calculateTool()">
            <option value="no" selected>أرقام فريدة غير مكررة (سحب قرعة)</option>
            <option value="yes" >السماح بالتكرار</option>
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
  0 => 'توليد عشوائي آمن تشفيرياً ومثالي لإجراء القرعة وسحب الفائزين في المسابقات بدون أي تحيز.',
  1 => 'يدعم استبعاد الأرقام المكررة وتحديد النطاق بالأرقام السالبة والموجبة.',
),
        'الأرقام صحيحة Integers.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل القرعة عادلة 100%؟',
    'a' => 'نعم؛ الخوارزمية تستخدم وحدة crypto في المتصفح التي تعتمد على ضوضاء النظام الفيزيائية وتضمن توزيعاً احتمالياً متساوياً لجميع الأرقام دون أي أفضلية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'random-string-generator',
  1 => 'password-generator',
  2 => 'uuid-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const min = parseInt(document.getElementById('minNumberRange').value) || 1;
            const max = parseInt(document.getElementById('maxNumberRange').value) || 100;
            const count = Math.max(1, Math.min(500, parseInt(document.getElementById('numbersCountToGen').value) || 5));
            const allowDup = document.getElementById('allowDuplicatesNumbers').value === 'yes';

            if (min >= max) {
                alert('الحد الأدنى يجب أن يكون أقل من الحد الأقصى!');
                return;
            }

            const rangeSize = max - min + 1;
            if (!allowDup && count > rangeSize) {
                alert('لا يمكن توليد ' + count + ' رقم فريد من مجال يحتوي على ' + rangeSize + ' رقماً فقط!');
                return;
            }

            const results = [];
            const used = new Set();

            while (results.length < count) {
                const arr = new Uint32Array(1);
                crypto.getRandomValues(arr);
                const rand = min + (arr[0] % rangeSize);
                if (allowDup) {
                    results.push(rand);
                } else if (!used.has(rand)) {
                    used.add(rand);
                    results.push(rand);
                }
            }

            const output = results.join(', ');

            setPrimaryResult(output, 'الأرقام العشوائية المولدة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأرقام', value: count + ' أرقام', color: '#10b981' },
                { label: 'نطاق المجال', value: 'من ' + min + ' إلى ' + max, color: '#3b82f6' },
                { label: 'حالة التكرار', value: allowDup ? 'مسموح' : 'فريدة تماماً (قرعة عادلة)', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الأرقام المولدة: <strong>${output}</strong></p>
            `);
        
        saveLastInputs('random-number-generator');
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
    restoreLastInputs('random-number-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>