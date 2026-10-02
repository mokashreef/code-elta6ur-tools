<?php
/**
 * أداة: تحويل النص والقوائم إلى كائن JSON مهيكل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'text-to-json-converter';
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
        <label class="form-label" for="rawTextToJsonInput">ألصق الأسطر أو البيانات المراد تحويلها لـ JSON</label>
        <textarea id="rawTextToJsonInput" class="form-control" rows="7" placeholder="ضع النص هنا (سطر لكل عنصر أو صيغة المفتاح: القيمة)..." oninput="calculateTool()">الرياض
دبي
القاهرة
عمان
الدوحة
الكويت</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="jsonOutputStructure">طبيعة هيكل الـ JSON المطلوب</label>
        <select id="jsonOutputStructure" class="form-control" onchange="calculateTool()">
            <option value="array_strings" selected>مصفوفة نصوص بسيطة ["نص1", "نص2"]</option>
            <option value="array_objects" >مصفوفة كائنات [{ "id": 1, "value": "نص" }]</option>
            <option value="key_value" >كائن مفتاح وقيمة (إذا كانت الأسطر بصيغة مفتاح: قيمة)</option>
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
  0 => 'يحول القوائم النصية العادية إلى مصفوفات JSON برمجية متوافقة مع لغات JavaScript و Python و PHP.',
  1 => 'يدعم استخراج الكائنات Key-Value إذا كانت الأسطر مفصولة بنقطتين رأسيتين (:).',
),
        'يفترض نصوصاً سليمة خالية من علامات التحكم غير المعرفة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أحول قائمة أسماء لـ JSON بصيغة id و name؟',
    'a' => 'اختر خيار مصفوفة كائنات (Array of Objects) وستقوم الأداة تلقائياً بترقيم كل عنصر برقم id متسلسل.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'json-to-formatted-text',
  1 => 'json-formatter',
  2 => 'json-validator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('rawTextToJsonInput').value;
            const struct = document.getElementById('jsonOutputStructure').value;

            const lines = text.split(/\n/).map(l => l.trim()).filter(l => l.length > 0);
            let jsonObj;

            if (struct === 'array_strings') {
                jsonObj = lines;
            } else if (struct === 'array_objects') {
                jsonObj = lines.map((val, idx) => ({ id: idx + 1, name: val }));
            } else {
                jsonObj = {};
                lines.forEach(l => {
                    const parts = l.split(/[:=]/);
                    if (parts.length >= 2) {
                        jsonObj[parts[0].trim()] = parts.slice(1).join(':').trim();
                    } else {
                        jsonObj[l] = '';
                    }
                });
            }

            const jsonStr = JSON.stringify(jsonObj, null, 2);

            setPrimaryResult('تم توليد JSON بنجاح (' + lines.length + ' عناصر)', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المحولة', value: lines.length + ' عنصر', color: '#10b981' },
                { label: 'حجم ملف JSON الناتج', value: jsonStr.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">كود JSON المنسق الجاهز للنسخ:</label>
                    <textarea class="form-control" rows="8" style="font-family:monospace;direction:ltr" readonly>${jsonStr}</textarea>
                </div>
            `);
        
        saveLastInputs('text-to-json-converter');
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
    restoreLastInputs('text-to-json-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>