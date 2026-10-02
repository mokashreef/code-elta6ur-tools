<?php
/**
 * أداة: محول صيغ الألوان ومنتقي الألوان (HEX / RGB / HSL)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'color-converter';
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
        <label class="form-label" for="colorPickerInput">اختر اللون بصرياً أو أدخل كود HEX</label>
        <input type="text" id="colorPickerInput" class="form-control" value="#6c63ff"    placeholder="#6c63ff" oninput="calculateTool()">
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
  0 => 'يحول الألوان بدقة متناهية بين أشهر 3 أنظمة لونية معتمدة في تصميم وتطوير الويب.',
  1 => 'نظام HSL (Hue, Saturation, Lightness) هو الأسهل في إنشاء تدرجات وظلال لنفس اللون برمجياً.',
),
        'يفترض ألوان RGB بنطاق 8-bit قياسي (0 إلى 255).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى يفضل استخدام HSL بدلاً من HEX؟',
    'a' => 'عند تصميم أنظمة الـ Themes وأوضاع الـ Dark Mode، حيث يسهل تعديل إضاءة اللون (Lightness) بنسبة مئوية دون تغيير درجته الأصلية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'css-gradient-generator',
  1 => 'css-box-shadow-generator',
  2 => 'color-palette',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let hex = document.getElementById('colorPickerInput').value.trim();
            if (!hex.startsWith('#')) hex = '#' + hex;

            // تحويل HEX إلى RGB
            let r = 0, g = 0, b = 0;
            if (hex.length === 4) {
                r = parseInt(hex[1] + hex[1], 16);
                g = parseInt(hex[2] + hex[2], 16);
                b = parseInt(hex[3] + hex[3], 16);
            } else if (hex.length === 7) {
                r = parseInt(hex.substring(1, 3), 16);
                g = parseInt(hex.substring(3, 5), 16);
                b = parseInt(hex.substring(5, 7), 16);
            }

            if (isNaN(r) || isNaN(g) || isNaN(b)) {
                alert('كود HEX غير صالح! يرجى إدخال كود مثل #6c63ff');
                return;
            }

            const rgbStr = 'rgb(' + r + ', ' + g + ', ' + b + ')';

            // تحويل RGB إلى HSL
            let rNorm = r / 255, gNorm = g / 255, bNorm = b / 255;
            let max = Math.max(rNorm, gNorm, bNorm), min = Math.min(rNorm, gNorm, bNorm);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                let d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case rNorm: h = (gNorm - bNorm) / d + (gNorm < bNorm ? 6 : 0); break;
                    case gNorm: h = (bNorm - rNorm) / d + 2; break;
                    case bNorm: h = (rNorm - gNorm) / d + 4; break;
                }
                h /= 6;
            }
            const hslStr = 'hsl(' + Math.round(h * 360) + ', ' + Math.round(s * 100) + '%, ' + Math.round(l * 100) + '%)';

            setPrimaryResult(hex.toUpperCase() + ' | ' + rgbStr, 'صيغ اللون المحولة');
            showResultArea();

            setDetailStats([
                { label: 'كود HEX', value: hex.toUpperCase(), color: hex },
                { label: 'صيغة RGB', value: rgbStr, color: '#3b82f6' },
                { label: 'صيغة HSL', value: hslStr, color: '#10b981' }
            ]);

            setResultContent(`
                <div style="display:flex;align-items:center;gap:1.5rem;margin-top:1rem;background:var(--bg-card);padding:1rem;border-radius:var(--radius-md)">
                    <div style="width:60px;height:60px;border-radius:var(--radius-md);background:${hex};box-shadow:0 4px 12px rgba(0,0,0,0.3);border:2px solid #fff"></div>
                    <div style="flex:1">
                        <div><strong>CSS HEX:</strong> <code style="color:var(--text-accent-light)">${hex.toUpperCase()}</code></div>
                        <div style="margin-top:0.25rem"><strong>CSS RGB:</strong> <code style="color:var(--text-accent-light)">${rgbStr}</code></div>
                        <div style="margin-top:0.25rem"><strong>CSS HSL:</strong> <code style="color:var(--text-accent-light)">${hslStr}</code></div>
                    </div>
                </div>
            `);
        
        saveLastInputs('color-converter');
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
    restoreLastInputs('color-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>