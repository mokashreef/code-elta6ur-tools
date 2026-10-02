<?php
/**
 * أداة: مولد نصوص لوريم إيبسوم عربي ولاتيني (Lorem Ipsum)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'lorem-ipsum-generator';
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
        <label class="form-label" for="loremLanguage">لغة النص المولد</label>
        <select id="loremLanguage" class="form-control" onchange="calculateTool()">
            <option value="arabic" selected>نص عربي تجريبي فصيح (بديل لوريم إيبسوم العربي)</option>
            <option value="latin" >نص لاتيني كلاسيكي (Lorem ipsum dolor sit amet)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="paragraphsCountLorem">عدد الفقرات المطلوبة</label>
        <input type="number" id="paragraphsCountLorem" class="form-control" value="3" min="1" max="20" step="1"  oninput="calculateTool()">
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
  0 => 'النصوص العربية التجريبية تراعي الطبيعة المتصلة للحروف وتوزيع المسافات الخاصة بالخطوط العربية.',
  1 => 'مثالية لمصممي UI/UX ومطوري الويب لملء قوالب الصفحات والنماذج قبل إضافة المحتوى الفعلي.',
),
        'يفترض نصوصاً نموذجية خالية من الأخطاء الإملائية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُفضل استخدام بديل عربي بدلاً من لوريم إيبسوم اللاتيني؟',
    'a' => 'لأن الحروف العربية تختلف في ارتفاعاتها وتمددها وتأثيرها على محاذاة الصناديق وحجم الخطوط مقارنة بالأحرف اللاتينية المنفصلة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'random-string-generator',
  1 => 'clean-arabic-text',
  2 => 'word-counter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const lang = document.getElementById('loremLanguage').value;
            const count = Math.max(1, Math.min(20, parseInt(document.getElementById('paragraphsCountLorem').value) || 3));

            const arabicParas = [
                'هذا نص تجريبي يمكن أن يستبدل في نفس المساحة، لقد تم توليد هذا النص من منصة كود التطور لتجربة التصميم والتنسيق البصري، حيث يحتاج المصممون إلى نصوص تحاكي الواقع لاختبار تناسق الخطوط والألوان.',
                'عندما يوضع النص في مكانه الصحيح، يظهر الشكل العام للتصميم بشكل أوضح، مما يساعد العميل على تصور النتيجة النهائية للموقع أو التطبيق دون أن يتشتت بالمعنى المباشر للكلمات.',
                'الخط العربي يتميز بجماليات فريدة وتناغم بصري استثنائي يجمع بين الأصالة والحداثة، وتعتبر التغذية البصرية السليمة من أهم ركائز النجاح في عالم التصميم وتجربة المستخدم.',
                'إن بناء تجربة مستخدم استثنائية يتطلب الاهتمام بأدق التفاصيل المعمارية والبرمجية، والتأكد من سهولة القراءة وتناسق المسافات البيضاء والتباين اللوني عبر كافة الشاشات.'
            ];

            const latinParas = [
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida.'
            ];

            const source = lang === 'arabic' ? arabicParas : latinParas;
            let resultList = [];
            for (let i = 0; i < count; i++) {
                resultList.push(source[i % source.length]);
            }

            const textOutput = resultList.join('\n\n');

            setPrimaryResult('تم توليد ' + count + ' فقرات ' + (lang === 'arabic' ? 'عربية' : 'لاتينية'), 'النص المولد');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات', value: count + ' فقرات', color: '#10b981' },
                { label: 'عدد الكلمات', value: textOutput.split(/\s+/).length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">النص التجريبي الجاهز للنسخ:</label>
                    <textarea class="form-control" rows="8" style="direction:${lang === 'arabic' ? 'rtl' : 'ltr'};line-height:1.8" readonly>${textOutput}</textarea>
                </div>
            `);
        
        saveLastInputs('lorem-ipsum-generator');
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
    restoreLastInputs('lorem-ipsum-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>