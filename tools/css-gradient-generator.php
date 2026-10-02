<?php
/**
 * أداة: مولد تدرجات الألوان (CSS Gradient Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'css-gradient-generator';
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
        <label class="form-label" for="gradientColor1">اللون الأول (البداية)</label>
        <input type="text" id="gradientColor1" class="form-control" value="#6c63ff"    placeholder="#6c63ff" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gradientColor2">اللون الثاني (النهاية)</label>
        <input type="text" id="gradientColor2" class="form-control" value="#00d4ff"    placeholder="#00d4ff" oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gradientAngle">زاوية التدرج بالدرجات (Angle)</label>
        <input type="number" id="gradientAngle" class="form-control" value="135" min="0" max="360" step="15"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="gradientType">نوع التدرج</label>
        <select id="gradientType" class="form-control" onchange="calculateTool()">
            <option value="linear" selected>تدرج خطي (Linear Gradient)</option>
            <option value="radial" >تدرج دائري شعاعي (Radial Gradient)</option>
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
  0 => 'يولد كود CSS نظيف مع كود احتياطي (Fallback color) للمتصفحات القديمة.',
  1 => 'يوفر معاينة بصرية حية فورية للتدرج قبل نسخه ولصقه في مشروعك.',
),
        'الألوان مدعومة بصيغ HEX و RGB وأسماء الألوان الإنجليزية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي الزاوية الأكثر جاذبية للتدرجات الخطية في المواقع الحديثة؟',
    'a' => 'الزاوية 135 درجة (من أعلى اليسار إلى أسفل اليمين) تعتبر الأكثر استخداماً في تصاميم الويب العصرية لأنها تماثل الاتجاه الطبيعي للإضاءة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'color-converter',
  1 => 'css-box-shadow-generator',
  2 => 'css-border-radius-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const c1 = document.getElementById('gradientColor1').value.trim() || '#6c63ff';
            const c2 = document.getElementById('gradientColor2').value.trim() || '#00d4ff';
            const angle = parseInt(document.getElementById('gradientAngle').value) || 135;
            const type = document.getElementById('gradientType').value;

            let cssRule = '';
            if (type === 'linear') {
                cssRule = 'linear-gradient(' + angle + 'deg, ' + c1 + ' 0%, ' + c2 + ' 100%)';
            } else {
                cssRule = 'radial-gradient(circle, ' + c1 + ' 0%, ' + c2 + ' 100%)';
            }

            const fullCss = 'background: ' + c1 + ';\nbackground: ' + cssRule + ';';

            setPrimaryResult('تم توليد التدرج بنجاح', 'تدرج لوني عصري');
            showResultArea();

            setDetailStats([
                { label: 'النوع المعتمد', value: type === 'linear' ? 'خطي ' + angle + '°' : 'دائري شعاعي', color: '#10b981' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <div style="height:120px;border-radius:var(--radius-lg);background:${cssRule};box-shadow:0 8px 24px rgba(0,0,0,0.3);margin-bottom:1rem"></div>
                    <label class="form-label">كود CSS الجاهز للنسخ:</label>
                    <textarea class="form-control" rows="3" style="font-family:monospace;direction:ltr" readonly>${fullCss}</textarea>
                </div>
            `);
        
        saveLastInputs('css-gradient-generator');
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
    restoreLastInputs('css-gradient-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>