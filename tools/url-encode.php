<?php
/**
 * أداة: ترميز وفك ترميز الروابط (URL Encode / Decode)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'url-encode';
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
        <label class="form-label" for="urlCodecInput">أدخل الرابط أو النص العربي</label>
        <textarea id="urlCodecInput" class="form-control" rows="5" placeholder="ضع الرابط أو النص هنا..." oninput="calculateTool()">https://example.com/search?q=برمجة المواقع&cat=تقنية</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="urlCodecAction">العملية المطلوبة</label>
        <select id="urlCodecAction" class="form-control" onchange="calculateTool()">
            <option value="encode" selected>ترميز الروابط (URL Encode) - تحويل العربية لنسب مئوية %D8%A7</option>
            <option value="decode" >فك ترميز الروابط (URL Decode) - إرجاع الروابط لكلمات عربية مقروءة</option>
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
  0 => 'ترميز الـ URL يحول الأحرف غير اللاتينية والمسافات والرموز الخاصة إلى تشفير النسبة المئوية المعتمد دولياً (%XX).',
  1 => 'فك الترميز يعيد الروابط المبهمة مثل %D8%B9%D8%B1%D8%A8%D9%8A إلى نصوص عربية مفهومة ومقروءة.',
),
        'يعتمد ترميز UTF-8 القياسي لشبكة الويب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تظهر الروابط العربية برموز %D8 في المتصفح؟',
    'a' => 'لأن بروتوكول HTTP الأصلي صُمم ليدعم محارف ASCII الإنجليزية فقط، فتقوم المتصفحات بترميز الحروف العربية بترميز النسبة المئوية لضمان نقلها بدون أخطاء.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'base64-converter',
  1 => 'html-entity-converter',
  2 => 'url-parser',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const input = document.getElementById('urlCodecInput').value.trim();
            const action = document.getElementById('urlCodecAction').value;

            let result = '';
            if (action === 'encode') {
                result = encodeURIComponent(input).replace(/[!'()*]/g, function(c) {
                    return '%' + c.charCodeAt(0).toString(16).toUpperCase();
                });
            } else {
                try {
                    result = decodeURIComponent(input.replace(/\+/g, ' '));
                } catch (e) {
                    alert('خطأ: النص لا يحتوي على ترميز URL صالح: ' + e.message);
                    return;
                }
            }

            setPrimaryResult('تمت العملية بنجاح (' + result.length + ' حرف)', 'حالة الترميز');
            showResultArea();

            setDetailStats([
                { label: 'العملية المنفذة', value: action === 'encode' ? 'ترميز (Encode)' : 'فك ترميز (Decode)', color: '#10b981' },
                { label: 'طول النص الناتج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النتيجة المحولة:</label>
                    <textarea class="form-control" rows="5" style="font-family:monospace;direction:ltr" readonly>${result}</textarea>
                </div>
            `);
        
        saveLastInputs('url-encode');
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
    restoreLastInputs('url-encode');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>