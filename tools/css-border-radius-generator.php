<?php
/**
 * أداة: مولد حواف مخصصة (CSS Border Radius Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'css-border-radius-generator';
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
        <label class="form-label" for="brTopLeft">الحافة العلوية اليمنى (%)</label>
        <input type="number" id="brTopLeft" class="form-control" value="30" min="0" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="brTopRight">الحافة العلوية اليسرى (%)</label>
        <input type="number" id="brTopRight" class="form-control" value="70" min="0" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="brBottomRight">الحافة السفلية اليسرى (%)</label>
        <input type="number" id="brBottomRight" class="form-control" value="70" min="0" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="brBottomLeft">الحافة السفلية اليمنى (%)</label>
        <input type="number" id="brBottomLeft" class="form-control" value="30" min="0" max="100" step="5"  oninput="calculateTool()">
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
  0 => 'يولد أشكالاً عضوية متغيرة (Blob shapes) باستخدام الخاصية المتقدمة لثماني قيم في border-radius.',
  1 => 'مفيدة جداً لخلفيات صور البروفايل والأيقونات العصرية في صفحات الهبوط.',
),
        'النسب المئوية تضمن تماسك الشكل.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما فائدة استخدام علامة السلاش / في border-radius؟',
    'a' => 'علامة السلاش تفصل بين أنصاف الأقطار الأفقية والعمودية للحواف، مما يسمح بصنع أشكال بيضاوية وعضوية غير متناظرة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'css-box-shadow-generator',
  1 => 'css-gradient-generator',
  2 => 'color-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const tl = parseInt(document.getElementById('brTopLeft').value) || 30;
            const tr = parseInt(document.getElementById('brTopRight').value) || 70;
            const br = parseInt(document.getElementById('brBottomRight').value) || 70;
            const bl = parseInt(document.getElementById('brBottomLeft').value) || 30;

            const radiusRule = tl + '% ' + (100 - tl) + '% ' + br + '% ' + (100 - br) + '% / ' + bl + '% ' + (100 - tr) + '% ' + tr + '% ' + (100 - bl) + '%';
            const cssCode = 'border-radius: ' + radiusRule + ';';

            setPrimaryResult(cssCode, 'كود الحواف المخصصة');
            showResultArea();

            setDetailStats([
                { label: 'نمط الشكل', value: 'شكل عضوي ناعم (Organic Blob)', color: '#10b981' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <div style="padding:2.5rem;display:flex;justify-content:center;background:var(--bg-glass);border-radius:var(--radius-lg);margin-bottom:1rem">
                        <div style="width:160px;height:160px;background:linear-gradient(135deg, #6c63ff, #00d4ff);border-radius:${radiusRule};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600">معاينة الشكل</div>
                    </div>
                    <label class="form-label">كود CSS:</label>
                    <textarea class="form-control" rows="2" style="font-family:monospace;direction:ltr" readonly>${cssCode}</textarea>
                </div>
            `);
        
        saveLastInputs('css-border-radius-generator');
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
    restoreLastInputs('css-border-radius-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>