<?php
/**
 * أداة: استخراج الكلمات المفتاحية الأكثر تكراراً وأهمية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'extract-keywords-from-text';
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
        <label class="form-label" for="keywordsTextInput">ألصق المقال لتحليل واستخراج كلماته المفتاحية</label>
        <textarea id="keywordsTextInput" class="form-control" rows="8" placeholder="ضع المقال هنا..." oninput="calculateTool()">التجارة الإلكترونية تشهد نمواً هائلاً في العالم العربي. يتيح المتجر الإلكتروني للشركات الوصول إلى شريحة واسعة من العملاء. لتحقيق النجاح في التجارة الإلكترونية، يجب التركيز على تجربة المستخدم وسرعة الشحن وتوفير بوابات دفع آمنة. التسويق الرقمي هو ركيزة أساسية لنمو أي متجر إلكتروني وزيادة مبيعات التجارة الإلكترونية.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="topKeywordsCount">عدد أهم الكلمات المطلوب إظهارها</label>
        <input type="number" id="topKeywordsCount" class="form-control" value="10" min="5" max="30" step="5"  oninput="calculateTool()">
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
  0 => 'تستبعد الأداة تلقائياً حروف الجر وأسماء الإشارة والضمائر (Stop Words) للتركيز فقط على الكلمات ذات الدلالة المعنوية.',
  1 => 'تفيد في تحسين السيو (SEO) والتأكد من مطابقة المقال للكلمات المفتاحية المستهدفة.',
),
        'الكلمات الأقل من 3 أحرف تستبعد تلقائياً.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي كثافة الكلمات المفتاحية المثالية للسيو؟',
    'a' => 'النسبة الموصى بها لمحركات البحث مثل Google هي بين 1% إلى 2.5% للكلمة المفتاحية الرئيسية في المقال لتفادي حشو الكلمات (Keyword Stuffing).',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'keyword-density-calculator',
  1 => 'arabic-word-counter',
  2 => 'extract-hashtags-from-text',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('keywordsTextInput').value.toLowerCase();
            const topN = Math.max(5, parseInt(document.getElementById('topKeywordsCount').value) || 10);

            // قائمة كلمات التوقف العربية الشائعة لاستبعادها (Stop Words)
            const stopWords = new Set([
                'في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'تم', 'كان', 'كانت',
                'أن', 'إن', 'أو', 'ثم', 'حيث', 'كل', 'هو', 'هي', 'التي', 'الذي', 'التي',
                'ما', 'لا', 'لم', 'لن', 'قد', 'بين', 'كما', 'ذلك', 'تلك', 'حتى', 'إذا',
                'غير', 'نحو', 'أي', 'فإن', 'ولكن', 'لكن', 'به', 'بها', 'له', 'لها', 'عند'
            ]);

            // تنظيف النص وتقطيعه لكلمات
            const rawWords = text
                .replace(/[^\u0600-\u06FFa-zA-Z0-9]/g, ' ')
                .split(/\s+/)
                .filter(w => w.length > 2 && !stopWords.has(w));

            const freqMap = {};
            rawWords.forEach(w => { freqMap[w] = (freqMap[w] || 0) + 1; });

            const sorted = Object.entries(freqMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, topN);

            let tableHtml = '<div style="margin-top:1rem"><table class="table" style="width:100%"><thead><tr><th>الكلمة المفتاحية</th><th>عدد مرات التكرار</th><th>الكثافة المئوية</th></tr></thead><tbody>';
            sorted.forEach(([word, count]) => {
                const density = ((count / Math.max(1, rawWords.length)) * 100).toFixed(1);
                tableHtml += `<tr><td><strong>${word}</strong></td><td>${count} مرات</td><td>${density}%</td></tr>`;
            });
            tableHtml += '</tbody></table></div>';

            setPrimaryResult(sorted.length + ' كلمات مفتاحية رئيسية تم استخراجها', 'الكلمات المفتاحية البارزة');
            showResultArea();

            setDetailStats([
                { label: 'أكثر كلمة تكراراً في النص', value: sorted[0] ? sorted[0][0] + ' (' + sorted[0][1] + 'x)' : 'لا يوجد', color: '#10b981' },
                { label: 'إجمالي الكلمات المعنوية المفلترة', value: rawWords.length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(tableHtml);
        
        saveLastInputs('extract-keywords-from-text');
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
    restoreLastInputs('extract-keywords-from-text');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>