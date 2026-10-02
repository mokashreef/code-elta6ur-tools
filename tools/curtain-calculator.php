<?php
/**
 * أداة: حاسبة كمية الستائر والقماش
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'curtain-calculator';
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
        <label class="form-label" for="windowWidth">عرض شباك / نافذة الغرفة (متر)</label>
        <input type="number" id="windowWidth" class="form-control" value="2.5" min="0.5"  step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="windowHeight">ارتفاع الستارة المطلوب حتى الأرض (متر)</label>
        <input type="number" id="windowHeight" class="form-control" value="2.8" min="1.0" max="5.0" step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="fullnessRatio">كثافة الكشكشة والكسرات (Fullness)</label>
        <select id="fullnessRatio" class="form-control" onchange="calculateTool()">
            <option value="1.5" >كسرات خفيفة بسيطة (1.5x عرض الشباك)</option>
            <option value="2.0" selected>كسرات قياسية متوسطة (2.0x عرض الشباك - المعيار الأكثر جمالاً)</option>
            <option value="2.5" >كسرات فندقية كثيفة وفخمة (2.5x عرض الشباك)</option>
            <option value="3.0" >ستائر ملكية ممتلئة جداً (3.0x)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="curtainLayersCount">عدد طبقات الستارة</label>
        <select id="curtainLayersCount" class="form-control" onchange="calculateTool()">
            <option value="1" >طبقة واحدة (قماش خفيف أو بلاك آوت فقط)</option>
            <option value="2" selected>طبقتين (طبقة شيفون خفيف + طبقة قماش ثقيل/بلاك آوت)</option>
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
  0 => 'ماسورة أو مجرى الستارة يجب أن يزيد بمقدار 15 إلى 20 سم من كل طرف خارج إطار الشباك.',
  1 => 'معامل الكشكشة 2x يعني أن عرض القماش المشترى يساوي ضعف عرض المسار ليظهر بثنيات متموجة أنيقة.',
  2 => 'يجب زيادة 25 إلى 30 سم لارتفاع القماش لاحتساب ثنية الكفة العلوية وشريط الحلقات والكفة السفلية.',
),
        'يفترض ستارة ممتدة من قرب السقف إلى قبل الأرضية بـ 1 سم كمعيار ديكوري حديث.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُفضل تعليق الستارة من أعلى نقطة قريبة من السقف؟',
    'a' => 'تعليق الستارة قرب السقف وجعلها تلامس الأرضية يعطي إيحاءً بصرياً رائعاً بأن سقف الغرفة أعلى وبأن النافذة أوسع وأفخم بكثير.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'wallpaper-calculator',
  1 => 'room-furniture-area-calculator',
  2 => 'carpet-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const winW = Math.max(0.5, parseFloat(document.getElementById('windowWidth').value) || 2.5);
            const winH = Math.max(1.0, parseFloat(document.getElementById('windowHeight').value) || 2.8);
            const fullness = parseFloat(document.getElementById('fullnessRatio').value) || 2.0;
            const layers = parseInt(document.getElementById('curtainLayersCount').value) || 2;

            // إضافة 20 سم من كل جانب للمجرى
            const rodWidth = winW + 0.40;
            const fabricWidthPerLayer = rodWidth * fullness;
            const fabricHeightWithHem = winH + 0.30; // 30 سم للثنيات العلوية والسفلية
            const totalMeters = fabricWidthPerLayer * layers;

            setPrimaryResult(fabricWidthPerLayer.toFixed(1) + ' متر عرض قماش لكل طبقة', 'عرض قماش الستارة المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'طول مسار الستارة (الماسورة / المجرى)', value: rodWidth.toFixed(2) + ' متر', color: '#3b82f6' },
                { label: 'معامل الكشكشة المعتمد', value: fullness + 'x', color: '#10b981' },
                { label: 'ارتفاع القماش المطلوب مع الثنيات', value: fabricHeightWithHem.toFixed(2) + ' متر', color: '#f59e0b' },
                { label: 'إجمالي عرض القماش للطبقتين', value: totalMeters.toFixed(1) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لنافذة بعرض <strong>${winW} م</strong> وارتفاع <strong>${winH} م</strong>، يوصى بمجرى ستارة بعرض <strong>${rodWidth.toFixed(2)} م</strong>. تحتاج كل طبقة إلى <strong>${fabricWidthPerLayer.toFixed(1)} متر قماش</strong> لتحقيق كشكشة فخمة بمضاعف ${fullness}x.</p>
            `);
        
        saveLastInputs('curtain-calculator');
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
    restoreLastInputs('curtain-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>