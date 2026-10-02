<?php
/**
 * أداة: التحقق من صحة كود XML
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'xml-validator';
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
        <label class="form-label" for="xmlToValidate">أدخل كود XML للتحقق منه</label>
        <textarea id="xmlToValidate" class="form-control" rows="8" placeholder="ضع كود XML هنا..." oninput="calculateTool()"><?xml version="1.0" encoding="UTF-8"?>
<note>
  <to>محمد</to>
  <from>خالد</from>
  <heading>تذكير</heading>
  <body>لا تنس موعد الاجتماع غداً</body>
</note></textarea>
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
  0 => 'التحقق من شروط Well-Formed XML: إغلاق كافة الوسوم بدقة، وحساسية حالة الأحرف، ووجود عنصر جذري وحيد (Root Element).',
  1 => 'يعرض رسالة الخطأ ورقم السطر المتسبب في المشكلة عند وجود وسم غير مغلق.',
),
        'يعتمد محرك التحليل DOMParser القياسي المدمج في المتصفح.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو الخطأ الأكثر شيوعاً في ملفات XML؟',
    'a' => 'نسيان إغلاق الوسوم الذاتية (مثل عدم كتابة /> في نهاية الوسم) أو وجود أكثر من وسم جذري في الملف.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'xml-formatter',
  1 => 'json-validator',
  2 => 'html-formatter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const xml = document.getElementById('xmlToValidate').value.trim();
            const parser = new DOMParser();
            const doc = parser.parseFromString(xml, 'application/xml');
            const errorNode = doc.querySelector('parsererror');

            if (errorNode) {
                setPrimaryResult('كود XML غير صالح ويحتوي على أخطاء ❌', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'غير صالح ❌', color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class="alert alert-danger" style="margin-top:1rem;color:#ef4444;background:rgba(239,68,68,0.1);border:1px solid #ef4444">
                        <strong>خطأ في صياغة XML:</strong><br>${errorNode.textContent}
                    </div>
                `);
            } else {
                setPrimaryResult('كود XML سليم وصحيح 100% ✅ (Well-Formed XML)', 'نتيجة الفحص');
                showResultArea();

                const rootName = doc.documentElement.nodeName;

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'سليم تماماً ✅', color: '#10b981' },
                    { label: 'اسم الوسم الجذري (Root Element)', value: '<' + rootName + '>', color: '#3b82f6' },
                    { label: 'عدد العقد المتفرعة المباشرة', value: doc.documentElement.children.length + ' عناصر', color: '#f59e0b' }
                ]);

                setResultContent(`
                    <div class="alert alert-success" style="margin-top:1rem">
                        <i class="fas fa-check-circle"></i> كود XML صالح تماماً ومتوافق مع معايير W3C الرسمية.
                    </div>
                `);
            }
        
        saveLastInputs('xml-validator');
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
    restoreLastInputs('xml-validator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>