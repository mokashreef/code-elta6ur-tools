<?php
/**
 * أداة: تحويل الأرقام العربية والإنجليزية (المشرقية والغربية)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'arabic-english-numbers-converter';
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
        <label class="form-label" for="numbersTextInput">ألصق النص المحتوي على أرقام للتحويل</label>
        <textarea id="numbersTextInput" class="form-control" rows="6" placeholder="ضع النص هنا..." oninput="calculateTool()">تأسست الشركة في عام 2024 وحققت أرباحاً تجاوزت ١٥٠٠٠٠ دولار خلال أول ٦ أشهر من إطلاقها.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="targetNumberFormat">تحويل كافة الأرقام إلى</label>
        <select id="targetNumberFormat" class="form-control" onchange="calculateTool()">
            <option value="to_english" selected>أرقام عربية غربية / إنجليزية (0, 1, 2, 3, 4, 5, 6, 7, 8, 9) - موصى به</option>
            <option value="to_arabic" >أرقام مشرقية / هندية (٠، ١، ٢، ٣، ٤، ٥، ٦، ٧، ٨، ٩)</option>
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
  0 => 'الأرقام (0, 1, 2, 3, 4...) هي الأرقام العربية الغربية التي ابتكرها الخوارزمي ونقلها الغرب عن العرب وتستخدم رسمياً في دول المغرب العربي والجهات الحكومية بالمملكة.',
  1 => 'الأرقام (٠، ١، ٢، ٣...) هي الأرقام المشرقية ذات الأصل الهندي وتستخدم في مصر وبلاد الشام والعراق.',
),
        'يحافظ التحويل على الكلمات والرموز المحيطة بالأرقام دون أي تغيير.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أيهما أفضل استخدامه في المواقع الإلكترونية؟',
    'a' => 'يُوصى دائماً باستخدام الأرقام الغربية (0-9) في المواقع والتطبيقات لأنها مدعومة في كافة المتصفحات ولا تسبب أخطاء في الحسابات البرمجية أو قواعد البيانات.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'clean-arabic-text',
  1 => 'clean-word-text',
  2 => 'quote-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('numbersTextInput').value;
            const target = document.getElementById('targetNumberFormat').value;

            const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            let result = '';
            if (target === 'to_english') {
                result = text;
                for (let i = 0; i < 10; i++) {
                    result = result.replace(new RegExp(eastern[i], 'g'), western[i]);
                }
            } else {
                result = text;
                for (let i = 0; i < 10; i++) {
                    result = result.replace(new RegExp(western[i], 'g'), eastern[i]);
                }
            }

            setPrimaryResult('تم توحيد الأرقام بنجاح في كامل النص', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'الصيغة المعتمدة الناتجة', value: target === 'to_english' ? 'أرقام غربية (0-9)' : 'أرقام مشرقية (٠-٩)', color: '#10b981' },
                { label: 'طول النص المعالج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص بعد توحيد صيغة الأرقام:</label>
                    <textarea class="form-control" rows="6" style="direction:rtl;line-height:1.8" readonly>${result}</textarea>
                </div>
            `);
        
        saveLastInputs('arabic-english-numbers-converter');
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
    restoreLastInputs('arabic-english-numbers-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>