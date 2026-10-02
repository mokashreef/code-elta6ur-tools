<?php
/**
 * أداة: حاسبة كثافة الكلمات المفتاحية (Keyword Density)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'keyword-density-calculator';
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
        <label class="form-label" for="densityTextInput">نص المقال بالكامل</label>
        <textarea id="densityTextInput" class="form-control" rows="7" placeholder="ضع النص هنا..." oninput="calculateTool()">الطاقة الشمسية أصبحت الخيار الأول لتوليد الكهرباء النظيفة في العالم العربي. توفر منظومة الطاقة الشمسية استقلالاً كاملاً عن انقطاعات الشبكة، وتخفض فاتورة الكهرباء الشهرية بشكل ملموس. الاستثمار في الطاقة الشمسية يعود بأرباح مجزية على المدى الطويل.</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="targetKeywordInput">الكلمة أو العبارة المفتاحية المستهدفة</label>
        <input type="text" id="targetKeywordInput" class="form-control" value="الطاقة الشمسية"    placeholder="اكتب الكلمة المفتاحية المستهدفة..." oninput="calculateTool()">
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
  0 => 'كثافة الكلمة المفتاحية = (عدد مرات تكرار الكلمة ÷ إجمالي كلمات المقال) × 100.',
  1 => 'الحفاظ على الكثافة بين 1% إلى 2.5% يضمن لمحركات البحث فهم موضوع المقال بدقة دون التعرض لعقوبة الحشو الزائد.',
),
        'يفترض نص مقال كامل يتجاوز 100 كلمة للحصول على نسبة ذات دلالة إحصائية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'أين يجب توزيع الكلمة المفتاحية في المقال؟',
    'a' => 'يُوصى بذكر الكلمة المفتاحية في عنوان المقال الرئيسي H1، وفي أول 100 كلمة من المقدمة، وفي أحد العناوين الفرعية H2، ومرة في الخاتمة بشكل طبيعي وسلس.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'extract-keywords-from-text',
  1 => 'arabic-word-counter',
  2 => 'arabic-english-slug-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('densityTextInput').value.toLowerCase();
            const keyword = document.getElementById('targetKeywordInput').value.trim().toLowerCase();

            if (!keyword) {
                alert('يرجى إدخال الكلمة المفتاحية المراد فحص كثافتها');
                return;
            }

            const totalWords = text.trim() ? text.trim().split(/\s+/).length : 0;
            // حساب تكرار العبارة
            const regex = new RegExp(keyword.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'gi');
            const matches = text.match(regex) || [];
            const count = matches.length;

            const density = totalWords > 0 ? ((count / totalWords) * 100) : 0;

            let status = 'كثافة ممتازة ومثالية للسيو ✅ (1% - 2.5%)';
            let color = '#10b981';
            if (density < 0.8) { status = 'منخفضة جداً؛ يفضل ذكر الكلمة المفتاحية أكثر ⚠️'; color = '#3b82f6'; }
            else if (density > 3.0) { status = 'مرتفعة جداً! خطر حشو الكلمات (Keyword Stuffing) ❌'; color = '#ef4444'; }

            setPrimaryResult(density.toFixed(2) + '% (' + count + ' مرات تكرار)', 'كثافة الكلمة المفتاحية في المقال');
            showResultArea();

            setDetailStats([
                { label: 'عدد مرات ذكر الكلمة المستهدفة', value: count + ' مرات', color: '#10b981' },
                { label: 'إجمالي كلمات المقال الكلي', value: totalWords + ' كلمة', color: '#3b82f6' },
                { label: 'تقييم التوافق مع محركات البحث (SEO)', value: status, color: color },
                { label: 'النسبة المثالية الموصى بها', value: '1.0% إلى 2.5%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكررت عبارة <strong>"${keyword}"</strong> بمعدل <strong>${count} مرات</strong> في مقال من <strong>${totalWords} كلمة</strong>، بكثافة مئوية <strong>${density.toFixed(2)}%</strong> - ${status}.</p>
            `);
        
        saveLastInputs('keyword-density-calculator');
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
    restoreLastInputs('keyword-density-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>