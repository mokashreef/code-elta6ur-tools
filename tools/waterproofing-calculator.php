<?php
/**
 * أداة: حاسبة كمية العزل المائي للأسطح والحمامات
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'waterproofing-calculator';
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
        <label class="form-label" for="waterproofArea">مساحة الأرضية الصافية (متر مربع)</label>
        <input type="number" id="waterproofArea" class="form-control" value="60" min="1"  step="2"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="perimeterWalls">محيط الجدران لرفع رقبة الزجاجة والوزرة (متر)</label>
        <input type="number" id="perimeterWalls" class="form-control" value="32" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="upstandHeightCm">ارتفاع رفع العزل على الجدران (سم) - عادة 25 إلى 30 سم</label>
        <input type="number" id="upstandHeightCm" class="form-control" value="30" min="15" max="60" step="5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="membraneLayers">عدد طبقات الممبرين (لفائف بيتومين 4 مم)</label>
        <input type="number" id="membraneLayers" class="form-control" value="1" min="1" max="3" step="1"  oninput="calculateTool()">
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
  0 => 'رفع العزل على الجدران بارتفاع 25-30 سم يمنع تسرب المياه للأدوار السفلية عند حدوث أي تجمع للمياه.',
  1 => 'يلزم عمل رقبة زجاجة (شطفة خرسانية مثلثة) في زاوية التقاء الأرضية بالحائط لضمان عدم انكسار لفائف العزل.',
),
        'يفترض استخدام رولات ممبرين بوليستر 4 مم مسلحة ذات كفاءة عالية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يكفي دهان البيتومين السائل بدون لفائف ممبرين؟',
    'a' => 'الدهان السائل لا يكفي وحده في الأسطح المعرضة للشمس والحركة؛ يجب استخدام لفائف ممبرين ملحومة بالنار لتتحمل التمدد والانكماش بدون تشقق.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'insulation-calculator',
  1 => 'thermal-insulation-calculator',
  2 => 'roof-slope-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const floor = Math.max(1, parseFloat(document.getElementById('waterproofArea').value) || 60);
            const perim = Math.max(1, parseFloat(document.getElementById('perimeterWalls').value) || 32);
            const upstand = Math.max(15, parseFloat(document.getElementById('upstandHeightCm').value) || 30) / 100;
            const layers = Math.max(1, parseInt(document.getElementById('membraneLayers').value) || 1);

            const upstandArea = perim * upstand;
            const totalNetArea = floor + upstandArea;
            const totalWithOverlap = totalNetArea * layers * 1.12; // 12% ركوب أطراف وهالك
            const rollsCount = Math.ceil(totalWithOverlap / 10); // الرول الصافي 10 م²
            const primerBuckets = Math.ceil(totalNetArea / 40); // برميل برايمر 40 م²

            setPrimaryResult(rollsCount + ' رول ممبرين عازل (4 مم)', 'كمية رولات العزل المائي');
            showResultArea();

            setDetailStats([
                { label: 'مساحة الأرضية الصافية', value: floor.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'مساحة الرفع على الحوائط (الرقبة)', value: upstandArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'إجمالي المساحة المطلوبة مع الركوب', value: totalWithOverlap.toFixed(1) + ' م²', color: '#f59e0b' },
                { label: 'عدد براميل دهان الأساس (Primer)', value: primerBuckets + ' برميل (18 لتر)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لعزل أرضية بمساحة <strong>${floor} م²</strong> مع رفع رقبة العزل بارتفاع <strong>${(upstand*100)} سم</strong>، تحتاج إلى <strong>${rollsCount} رول ممبرين</strong> و <strong>${primerBuckets} برميل برايمر</strong> تأسيسي.</p>
            `);
        
        saveLastInputs('waterproofing-calculator');
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
    restoreLastInputs('waterproofing-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>