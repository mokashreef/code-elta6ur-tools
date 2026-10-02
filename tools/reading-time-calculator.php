<?php
/**
 * أداة: حساب وقت قراءة النص
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'reading-time-calculator';
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
        <label class="form-label" for="readingTextInput">ألصق المقال أو المحتوى لحساب مدة قراءته</label>
        <textarea id="readingTextInput" class="form-control" rows="8" placeholder="ضع النص هنا..." oninput="calculateTool()">تعتبر القراءة من أهم الأدوات التي تفتح آفاق العقل البشري، وتمنحه القدرة على رؤية العالم من زوايا متعددة ومختلفة. عندما يقرأ الإنسان، فإنه لا يكتفي باستيعاب المعلومات فحسب، بل يتدرب عقله على التحليل النقدي والتفكير المنطقي والربط بين الأفكار المتباعدة.

إن القارئ النهم يعيش مئات الحيوات في حياة واحدة، ويتعلم من خلاصة تجارب وحكمة الآخرين التي استغرقت عقوداً من الزمن لتدوينها. الاستثمار في القراءة اليومية هو أعظم استثمار يمكن أن يقدمه الشخص لنفسه ولمستقبله المهني والشخصي.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="readingSpeedWpm">سرعة القراءة المستهدفة</label>
        <select id="readingSpeedWpm" class="form-control" onchange="calculateTool()">
            <option value="160" >قراءة متأنية ودقيقة (دراسة وتحليل) ~ 160 كلمة/دقيقة</option>
            <option value="200" selected>قراءة قياسية طبيعية للمقالات ~ 200 كلمة/دقيقة</option>
            <option value="250" >قراءة سريعة واعية ~ 250 كلمة/دقيقة</option>
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
  0 => 'وقت القراءة = عدد كلمات النص ÷ سرعة القراءة بالكلمة في الدقيقة (WPM).',
  1 => 'إضافة علامة (وقت القراءة المقدر: 3 دقائق) في بداية المقالات والمدونات يرفع نسبة إكمال القراءة بنسبة 40%.',
),
        'معدل سرعة القراءة الصامتة للبالغين باللغة العربية يتراوح بين 180 إلى 220 كلمة/دقيقة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا تختلف سرعة القراءة الصوتية عن الصامتة؟',
    'a' => 'القراءة الصامتة أسرع بحوالي 40% لأن العين والدماغ يعالجان الكلمات كصور وأنماط مباشرة دون انتظار حركة عضلات اللسان والحبال الصوتية.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'speech-time-calculator',
  1 => 'arabic-word-counter',
  2 => 'avg-sentence-length',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('readingTextInput').value.trim();
            const wpm = parseFloat(document.getElementById('readingSpeedWpm').value) || 200;
            const words = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;

            const totalMinutes = words / wpm;
            const mins = Math.floor(totalMinutes);
            const secs = Math.round((totalMinutes - mins) * 60);

            let timeStr = '';
            if (mins > 0) timeStr += mins + ' دقيقة ';
            if (secs > 0) timeStr += 'و ' + secs + ' ثانية';
            if (timeStr === '') timeStr = 'أقل من 5 ثوانٍ';

            setPrimaryResult(timeStr, 'الوقت المقدر لقراءة المقال');
            showResultArea();

            setDetailStats([
                { label: 'عدد كلمات النص', value: words.toLocaleString() + ' كلمة', color: '#3b82f6' },
                { label: 'سرعة القراءة المعتمدة', value: wpm + ' كلمة / دقيقة', color: '#10b981' },
                { label: 'عدد الأحرف والرموز', value: text.length.toLocaleString() + ' حرف', color: '#f59e0b' },
                { label: 'الوقت بالدقائق العشرية', value: totalMinutes.toFixed(1) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>${words} كلمة</strong>. يستغرق القارئ العادي حوالي <strong>${timeStr}</strong> لإنهاء قراءته وفهم محتواه.</p>
            `);
        
        saveLastInputs('reading-time-calculator');
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
    restoreLastInputs('reading-time-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>