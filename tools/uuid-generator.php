<?php
/**
 * أداة: توليد معرفات فريدة عالمياً (UUID / GUID Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'uuid-generator';
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
        <label class="form-label" for="uuidCountInput">عدد المعرفات المطلوب توليدها</label>
        <input type="number" id="uuidCountInput" class="form-control" value="5" min="1" max="100" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="uuidVersionType">إصدار المعرف</label>
        <select id="uuidVersionType" class="form-control" onchange="calculateTool()">
            <option value="v4" selected>UUID v4 العشوائي المشفر (المعيار الأكثر أماناً وشهرة عالمياً)</option>
            <option value="v4_uppercase" >UUID v4 بأحرف كبيرة (UPPERCASE GUID)</option>
            <option value="v4_no_hyphens" >UUID v4 بدون شُرطات (32 حرف مدمج)</option>
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
  0 => 'الـ UUID v4 يتكون من 128 بت تولد عشوائياً باستخدام دوال التشفير القوية في المتصفح (crypto.getRandomValues).',
  1 => 'احتمالية توليد نفس المعرف مرتين تعادل صفر عملياً حتى لو قمت بتوليد مليارات المعرفات.',
),
        'المعرفات تعتمد معيار RFC 4122 القياسي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفائدة من استخدام UUID بدلاً من الأرقام التلقائية (Auto Increment ID)؟',
    'a' => 'الـ UUID يمنع المستخدمين من تخمين معرفات السجلات الأخرى (Prevent Enumeration Attacks)، ويسمح بتوليد المعرفات في الأنظمة الموزعة والسحابية دون تضارب وبدون الحاجة لانتظار استجابة قاعدة البيانات المركزية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'random-string-generator',
  1 => 'password-generator',
  2 => 'hash-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const count = Math.max(1, Math.min(100, parseInt(document.getElementById('uuidCountInput').value) || 5));
            const format = document.getElementById('uuidVersionType').value;

            function generateUUIDv4() {
                if (crypto && crypto.randomUUID) {
                    return crypto.randomUUID();
                }
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }

            const list = [];
            for (let i = 0; i < count; i++) {
                let id = generateUUIDv4();
                if (format === 'v4_uppercase') id = id.toUpperCase();
                if (format === 'v4_no_hyphens') id = id.replace(/-/g, '');
                list.push(id);
            }

            const resultText = list.join('\n');

            setPrimaryResult('تم توليد ' + count + ' معرف فريد عشوائي (UUID v4)', 'المعرفات الناتجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد المعرفات المولدة', value: count + ' UUID', color: '#10b981' },
                { label: 'احتمالية التصادم والتكرار', value: '1 في 2.71 كوينتيليون (مستحيل إحصائياً)', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">قائمة المعرفات الفريدة (سطر لكل UUID):</label>
                    <textarea class="form-control" rows="7" style="font-family:monospace;direction:ltr" readonly>${resultText}</textarea>
                </div>
            `);
        
        saveLastInputs('uuid-generator');
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
    restoreLastInputs('uuid-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>