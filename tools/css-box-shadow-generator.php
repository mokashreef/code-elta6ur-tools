<?php
/**
 * أداة: مولد ظلال الصناديق (CSS Box Shadow Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'css-box-shadow-generator';
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
        <label class="form-label" for="shadowX">الإزاحة الأفقية X (بكسل)</label>
        <input type="number" id="shadowX" class="form-control" value="0" min="-50" max="50" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shadowY">الإزاحة الرأسية Y (بكسل)</label>
        <input type="number" id="shadowY" class="form-control" value="10" min="-50" max="50" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shadowBlur">درجة التمويه والضبابية Blur (بكسل)</label>
        <input type="number" id="shadowBlur" class="form-control" value="25" min="0" max="100" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shadowSpread">نطاق الانتشار Spread (بكسل)</label>
        <input type="number" id="shadowSpread" class="form-control" value="-5" min="-50" max="50" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="shadowColorHex">لون الظل</label>
        <input type="text" id="shadowColorHex" class="form-control" value="rgba(0, 0, 0, 0.35)"    placeholder="rgba(0, 0, 0, 0.35)" oninput="calculateTool()">
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
  0 => 'الظلال الناعمة ذات الانتشار السالب (Negative Spread) تمنح البطاقات مظهراً ثلاثي الأبعاد فاخراً وعصرياً.',
  1 => 'يتضمن بادئة -webkit- لضمان التوافق مع كافة المتصفحات.',
),
        'يفترض ظلاً خارجياً متناسقاً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أجعل الظل يبدو طبيعياً وناعماً؟',
    'a' => 'اجعل قيمة الـ Blur عالية (20px إلى 30px) واستخدم انتشاراً سالباً خفيفاً (-5px) مع شفافية لون منخفضة (Opacity 0.1 إلى 0.2).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'css-border-radius-generator',
  1 => 'css-gradient-generator',
  2 => 'color-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const x = parseInt(document.getElementById('shadowX').value) || 0;
            const y = parseInt(document.getElementById('shadowY').value) || 10;
            const blur = parseInt(document.getElementById('shadowBlur').value) || 25;
            const spread = parseInt(document.getElementById('shadowSpread').value) || -5;
            const color = document.getElementById('shadowColorHex').value || 'rgba(0, 0, 0, 0.35)';

            const shadowRule = x + 'px ' + y + 'px ' + blur + 'px ' + spread + 'px ' + color;
            const cssFull = 'box-shadow: ' + shadowRule + ';\n-webkit-box-shadow: ' + shadowRule + ';';

            setPrimaryResult('box-shadow: ' + shadowRule, 'كود الظل');
            showResultArea();

            setDetailStats([
                { label: 'التمويه والانتشار', value: blur + 'px / ' + spread + 'px', color: '#10b981' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <div style="padding:2.5rem;display:flex;justify-content:center;background:var(--bg-glass);border-radius:var(--radius-lg);margin-bottom:1rem">
                        <div style="width:180px;height:100px;background:var(--bg-card);border-radius:var(--radius-md);box-shadow:${shadowRule};display:flex;align-items:center;justify-content:center;font-weight:600">معاينة الصندوق</div>
                    </div>
                    <label class="form-label">كود CSS:</label>
                    <textarea class="form-control" rows="2" style="font-family:monospace;direction:ltr" readonly>${cssFull}</textarea>
                </div>
            `);
        
        saveLastInputs('css-box-shadow-generator');
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
    restoreLastInputs('css-box-shadow-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>