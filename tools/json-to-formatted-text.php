<?php
/**
 * أداة: تحويل كود JSON إلى نص منسق وقابل للقراءة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'json-to-formatted-text';
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
        <label class="form-label" for="jsonSourceInput">ألصق كود الـ JSON هنا</label>
        <textarea id="jsonSourceInput" class="form-control" rows="8" placeholder="ضع كود JSON هنا..." oninput="calculateTool()">[
  {"id": 1, "name": "الرياض", "country": "السعودية"},
  {"id": 2, "name": "دبي", "country": "الإمارات"},
  {"id": 3, "name": "القاهرة", "country": "مصر"}
]</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="textFormatModeJson">طريقة عرض النص الناتج</label>
        <select id="textFormatModeJson" class="form-control" onchange="calculateTool()">
            <option value="bullet_list" selected>قائمة نقطية منسقة بوضوح</option>
            <option value="table_csv" >جدول نصي / أسطر مفصولة بفواصل CSV</option>
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
  0 => 'يحول هياكل JSON المعقدة إلى نصوص وجداول مبسطة يفهمها المستخدم العادي وغير المبرمج.',
  1 => 'يدعم المصفوفات والكائنات المتداخلة وقوائم البيانات.',
),
        'يفترض JSON صالح البنية والصياغة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ماذا يحدث إذا كان الـ JSON يحتوي على خطأ كتابي؟',
    'a' => 'ستعرض الأداة رسالة تنبيه توضح سطر ونوع الخطأ النحوي في ملف الـ JSON لتصحيحه.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'text-to-json-converter',
  1 => 'json-formatter',
  2 => 'json-validator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const jsonText = document.getElementById('jsonSourceInput').value;
            const mode = document.getElementById('textFormatModeJson').value;

            let parsed;
            try {
                parsed = JSON.parse(jsonText);
            } catch (e) {
                alert('خطأ في صيغة الـ JSON: ' + e.message);
                return;
            }

            let output = '';
            if (Array.isArray(parsed)) {
                if (mode === 'bullet_list') {
                    output = parsed.map((item, idx) => {
                        if (typeof item === 'object' && item !== null) {
                            const details = Object.entries(item).map(([k, v]) => k + ': ' + v).join(' | ');
                            return (idx + 1) + '. ' + details;
                        }
                        return '• ' + item;
                    }).join('\n');
                } else {
                    if (parsed.length > 0 && typeof parsed[0] === 'object') {
                        const headers = Object.keys(parsed[0]);
                        output = headers.join(', ') + '\n' + parsed.map(row => headers.map(h => row[h] || '').join(', ')).join('\n');
                    } else {
                        output = parsed.join(', ');
                    }
                }
            } else if (typeof parsed === 'object' && parsed !== null) {
                output = Object.entries(parsed).map(([k, v]) => '• ' + k + ': ' + (typeof v === 'object' ? JSON.stringify(v) : v)).join('\n');
            } else {
                output = String(parsed);
            }

            setPrimaryResult('تم تحويل كود JSON إلى نص مقروء', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المعالجة', value: (Array.isArray(parsed) ? parsed.length : Object.keys(parsed).length) + ' عنصر', color: '#10b981' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص المقروء الناتج:</label>
                    <textarea class="form-control" rows="8" style="direction:rtl;line-height:1.8" readonly>${output}</textarea>
                </div>
            `);
        
        saveLastInputs('json-to-formatted-text');
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
    restoreLastInputs('json-to-formatted-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>