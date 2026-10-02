<?php
/**
 * أداة: تشفير وفك تشفير رموز HTML Entities
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'html-entity-converter';
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
        <label class="form-label" for="entityInputText">أدخل النص أو الرموز</label>
        <textarea id="entityInputText" class="form-control" rows="5" placeholder="ضع النص هنا..." oninput="calculateTool()"><div>© 2026 "منصة الأدوات" & <المطورين></div></textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="entityActionChoice">العملية المطلوبة</label>
        <select id="entityActionChoice" class="form-control" onchange="calculateTool()">
            <option value="encode" selected>تشفير الرموز الخاصة إلى HTML Entities (&lt;, &gt;, &quot;, إلخ)</option>
            <option value="decode" >فك التشفير وإرجاع الرموز لأصلها</option>
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
  0 => 'تشفير رموز الـ HTML يمنع تشوه الأكواد ويحمي المواقع من ثغرات الحقن وتداخل الأقواس < >.',
  1 => 'مفيد لعرض أكواد برمجية على صفحات الويب دون أن يقوم المتصفح بتنفيذها كعناصر HTML حقيقية.',
),
        'يعتمد المعايير القياسية لترميز الرموز المحجوزة في HTML5.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا نشفر علامة الأكبر والأصغر < >؟',
    'a' => 'لأن المتصفح سيعتبرها بداية وسم HTML وسيحاول تنفيذها بدلاً من عرضها كنص عادي للمستخدم.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'url-encode',
  1 => 'html-formatter',
  2 => 'clean-html-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('entityInputText').value;
            const action = document.getElementById('entityActionChoice').value;

            let result = '';
            if (action === 'encode') {
                const el = document.createElement('div');
                el.innerText = text;
                result = el.innerHTML.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            } else {
                const el = document.createElement('div');
                el.innerHTML = text;
                result = el.innerText;
            }

            setPrimaryResult('تمت معالجة رموز HTML بنجاح', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'العملية', value: action === 'encode' ? 'تشفير Entities' : 'فك التشفير', color: '#10b981' },
                { label: 'طول النص الناتج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النتيجة المحولة:</label>
                    <textarea class="form-control" rows="6" style="font-family:monospace;direction:ltr" readonly>${result}</textarea>
                </div>
            `);
        
        saveLastInputs('html-entity-converter');
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
    restoreLastInputs('html-entity-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>