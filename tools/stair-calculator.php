<?php
/**
 * أداة: حاسبة الدرج المعمارية (معادلة بلونديل)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'stair-calculator';
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
        <label class="form-label" for="totalFloorHeight">ارتفاع الطابق الكلي من التشطيب للتشطيب (سم)</label>
        <input type="number" id="totalFloorHeight" class="form-control" value="300" min="100" max="600" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="idealRiserHeight">ارتفاع القائمة المستهدف (Riser) - المريح 15 إلى 17 سم</label>
        <input type="number" id="idealRiserHeight" class="form-control" value="16.5" min="14" max="21" step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="stairWidthCm">عرض شاحط الدرج (سم) - القياسي 110 إلى 130 سم</label>
        <input type="number" id="stairWidthCm" class="form-control" value="120" min="80" max="250" step="5"  oninput="calculateTool()">
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
  0 => 'معادلة بلونديل المعمارية الشهيرة: 2 × القائمة + النائمة = من 62 إلى 64 سم.',
  1 => 'ارتفاع القائمة المريح للإنسان يتراوح بين 15 سم إلى 17 سم كحد أقصى.',
  2 => 'عرض النائمة (موطئ القدم) يجب ألا يقل عن 28 إلى 30 سم لسلامة النزول.',
),
        'يفترض ارتفاع الطابق مقاساً من منسوب تشطيب البلاط السفلي إلى منسوب تشطيب البلاط العلوي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كم يجب أن يكون الحد الأدنى لارتفاع الرأس (Headroom) فوق الدرج؟',
    'a' => 'يجب ألا يقل الارتفاع الحر العمودي بين أي درجة وسقف الطابق العلوي عن 2.10 متر إلى 2.20 متر لضمان عدم اصطدام رأس الشخص أثناء النزول.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'stair-steps-calculator',
  1 => 'house-building-cost-calculator',
  2 => 'floor-tiles-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const totalH = Math.max(100, parseFloat(document.getElementById('totalFloorHeight').value) || 300);
            const targetR = Math.max(14, parseFloat(document.getElementById('idealRiserHeight').value) || 16.5);
            const stairW = Math.max(80, parseFloat(document.getElementById('stairWidthCm').value) || 120);

            const stepsCount = Math.round(totalH / targetR);
            const exactRiser = totalH / stepsCount;
            // معادلة بلونديل: 2R + G = 63 سم -> G = 63 - 2R
            const exactTread = 63 - (2 * exactRiser);
            // طول الشاحط الأفقي = (عدد الدرجات - 1) * النائمة
            const horizontalRun = (stepsCount - 1) * exactTread;

            let comfortStatus = 'مريح جداً ومطابق للمواصفات الدولية ✅';
            let color = '#10b981';
            if (exactRiser > 18) { comfortStatus = 'شديد الانحدار ومرهق في الصعود ⚠️'; color = '#ef4444'; }

            setPrimaryResult(stepsCount + ' درجة (قائمة: ' + exactRiser.toFixed(1) + ' سم | نائمة: ' + exactTread.toFixed(1) + ' سم)', 'المواصفات المعمارية للدرج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الدرجات الكلي', value: stepsCount + ' درجات', color: '#3b82f6' },
                { label: 'ارتفاع الدرجة (القائمة Riser)', value: exactRiser.toFixed(1) + ' سم', color: '#10b981' },
                { label: 'عرض موطئ القدم (النائمة Tread)', value: exactTread.toFixed(1) + ' سم', color: '#f59e0b' },
                { label: 'المسافة الأفقية المطلوبة للدرج', value: (horizontalRun / 100).toFixed(2) + ' متر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتجاوز ارتفاع <strong>${totalH} سم</strong>، يقسم الدرج إلى <strong>${stepsCount} درجة</strong> بارتفاع قائمة <strong>${exactRiser.toFixed(1)} سم</strong> وعرض نائمة <strong>${exactTread.toFixed(1)} سم</strong>. المعادلة تحقق شرط بلونديل (2R + G = ${(2*exactRiser + exactTread).toFixed(1)} سم) - ${comfortStatus}.</p>
            `);
        
        saveLastInputs('stair-calculator');
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
    restoreLastInputs('stair-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>