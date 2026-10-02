<?php
/**
 * أداة: حساب وقت إلقاء النص والخطاب (Speech Time)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'speech-time-calculator';
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
        <label class="form-label" for="speechTextInput">ألصق نص الخطاب أو العرض التقديمي أو الفيديو</label>
        <textarea id="speechTextInput" class="form-control" rows="8" placeholder="ضع نص الخطاب هنا..." oninput="calculateTool()">السلام عليكم ورحمة الله وبركاته،
أيها الحضور الكريم، يسعدني ويشرفني أن أقف بينكم اليوم لنتحدث عن مستقبل التحول الرقمي، وكيف تسهم التكنولوجيا في تمكين الشباب العربي من بناء مشاريع رائدة.

إن التحديات التي نواجهها اليوم هي ذاتها الفرص التي ستصنع قادة الغد، وبالعزيمة والعمل المشترك سنحقق الطموحات.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="speakingPace">سرعة ونبرة الإلقاء</label>
        <select id="speakingPace" class="form-control" onchange="calculateTool()">
            <option value="110" >إلقاء بطيء ورسمي وهادئ مع وقفات مؤثرة (110 كلمة/دقيقة)</option>
            <option value="130" selected>إلقاء خطابي قياسي للمؤتمرات والندوات (130 كلمة/دقيقة)</option>
            <option value="150" >إلقاء سريع وإعلاني وتفاعلي (فيديوهات يوتيوب وتيك توك) ~ 150 كلمة/دقيقة</option>
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
  0 => 'السرعة الذهبية للخطابات المؤثرة والعروض التقديمية هي 120 إلى 140 كلمة في الدقيقة.',
  1 => 'التحدث بسرعة أعلى من 160 كلمة/دقيقة يصعب على الجمهور متابعة واستيعاب النقاط المحورية.',
),
        'يفترض وقفات تنفس طبيعية وتأكيداً على الكلمات المفتاحية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم عدد الكلمات المناسبة لعرض تقديمي مدته 10 دقائق؟',
    'a' => 'الخطاب المثالي لمدة 10 دقائق يحتوي بين 1200 إلى 1300 كلمة كحد أقصى لإتاحة وقت للترحيب والأسئلة والوقفات التوضيحية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'reading-time-calculator',
  1 => 'arabic-word-counter',
  2 => 'avg-sentence-length',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('speechTextInput').value.trim();
            const wpm = parseFloat(document.getElementById('speakingPace').value) || 130;
            const words = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;

            const totalMins = words / wpm;
            const mins = Math.floor(totalMins);
            const secs = Math.round((totalMins - mins) * 60);

            let timeStr = '';
            if (mins > 0) timeStr += mins + ' دقيقة ';
            if (secs > 0) timeStr += 'و ' + secs + ' ثانية';
            if (timeStr === '') timeStr = 'أقل من 5 ثوانٍ';

            setPrimaryResult(timeStr, 'مدة الإلقاء والحديث على المسرح');
            showResultArea();

            setDetailStats([
                { label: 'عدد كلمات الخطاب', value: words.toLocaleString() + ' كلمة', color: '#3b82f6' },
                { label: 'سرعة الإلقاء الصوتية', value: wpm + ' كلمة / دقيقة', color: '#10b981' },
                { label: 'الشرائح المقترحة للعرض (Slide)', value: Math.ceil(totalMins / 2) + ' شرائح (بمعدل دقيقتين للوحة)', color: '#f59e0b' },
                { label: 'الوقت بالدقائق العشرية', value: totalMins.toFixed(1) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يستغرق إلقاء هذا النص أمام الجمهور حوالي <strong>${timeStr}</strong> بنبرة إلقاء <strong>${wpm} كلمة/دقيقة</strong> مع مراعاة فترات التوقف والتنفس الطبيعية.</p>
            `);
        
        saveLastInputs('speech-time-calculator');
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
    restoreLastInputs('speech-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>