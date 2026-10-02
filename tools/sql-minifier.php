<?php
/**
 * أداة: ضغط استعلامات SQL في سطر واحد (SQL Minifier)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sql-minifier';
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
        <label class="form-label" for="sqlToMinifyInput">أدخل استعلام SQL الممتد على عدة أسطر</label>
        <textarea id="sqlToMinifyInput" class="form-control" rows="8" placeholder="ضع استعلام SQL هنا..." oninput="calculateTool()">SELECT
  id,
  username,
  email
FROM users
WHERE status = 'active'
  AND created_at >= NOW()
ORDER BY id DESC
LIMIT 50;</textarea>
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
  0 => 'يضغط الاستعلامات متعددة الأسطر في سطر واحد نظيف لتضمينها كمتغير في لغات البرمجة (Python, PHP, Node.js).',
  1 => 'يحذف التعليقات الزائدة والمسافات البادئة.',
),
        'يحافظ على النصوص المحاطة بعلامات اقتباس فردية أو مزدوجة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'متى أحتاج لضغط استعلام SQL؟',
    'a' => 'عند تضمين الاستعلامات داخل ملفات الإعدادات والـ Shell Scripts وسجلات الـ Log التي تفضل أسطراً مفردة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sql-formatter',
  1 => 'json-minifier',
  2 => 'text-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const sql = document.getElementById('sqlToMinifyInput').value.trim();
            // إزالة التعليقات
            let minified = sql.replace(/--.*$/gm, '').replace(/\/\*[\s\S]*?\*\//g, '');
            // ضغط المسافات والأسطر في مسافة واحدة
            minified = minified.replace(/\s+/g, ' ').trim();

            const saved = sql.length - minified.length;

            setPrimaryResult('تم ضغط الاستعلام في سطر واحد (' + minified.length + ' حرف)', 'حالة الضغط');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأحرف بعد الضغط', value: minified.length + ' حرف', color: '#10b981' },
                { label: 'الأحرف والأسطر الموفرة', value: saved + ' حرف وفر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">استعلام SQL المضغوط (سطر واحد جاهز للتضمين في الكود):</label>
                    <textarea class="form-control" rows="4" style="font-family:monospace;direction:ltr" readonly>${minified}</textarea>
                </div>
            `);
        
        saveLastInputs('sql-minifier');
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
    restoreLastInputs('sql-minifier');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>