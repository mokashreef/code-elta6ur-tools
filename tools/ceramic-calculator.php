<?php
/**
 * أداة: حاسبة كمية السيراميك والبورسلان
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'ceramic-calculator';
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
        <label class="form-label" for="ceramicSurfaceArea">المساحة المراد تبليطها (أرضيات أو جدران) بالمتر المربع</label>
        <input type="number" id="ceramicSurfaceArea" class="form-control" value="50" min="1"  step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="boxMeters">المساحة التي تغطيها الكرتونة الواحدة بالمتر المربع (مكتوبة على العلبة)</label>
        <input type="number" id="boxMeters" class="form-control" value="1.44" min="0.5"  step="0.04"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="ceramicWastePercent">نسبة هالك القص والزوائد (%)- عادة 8%</label>
        <input type="number" id="ceramicWastePercent" class="form-control" value="8" min="5" max="20" step="1"  oninput="calculateTool()">
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
  0 => 'الكرتونة من مقاس 60×60 غالباً تحتوي على 4 قطع وتغطي 1.44 م².',
  1 => 'كرتونة مقاس 60×120 غالباً تحتوي على قطعتين وتغطي 1.44 م².',
  2 => 'كرتونة مقاس 80×80 غالباً تحتوي على قطعتين أو 3 وتغطي حوالي 1.28 أو 1.92 م².',
),
        'سيراميك الجدران للحمامات والمطابخ يتطلب دقة في خصم مساحة الباب والشباك وحساب الوزرات.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما الفرق بين كرتونة السيراميك والبورسلان؟',
    'a' => 'البورسلان أكثر كثافة وصلابة ووزناً وأقل امتصاصاً للماء، ويحتاج دائماً إلى غراء مخصص (وليس أسمنت عادي) للتثبيت على الأرضيات.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'floor-tiles-calculator',
  1 => 'tile-adhesive-calculator',
  2 => 'grout-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const area = Math.max(1, parseFloat(document.getElementById('ceramicSurfaceArea').value) || 50);
            const mPerBox = Math.max(0.5, parseFloat(document.getElementById('boxMeters').value) || 1.44);
            const waste = Math.max(5, parseFloat(document.getElementById('ceramicWastePercent').value) || 8) / 100;

            const areaWithWaste = area * (1 + waste);
            const boxesNeeded = Math.ceil(areaWithWaste / mPerBox);
            const actualTotalArea = boxesNeeded * mPerBox;

            setPrimaryResult(boxesNeeded + ' كرتونة سيراميك / بورسلان', 'عدد الكراتين المطلوبة للشراء');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للمشروع', value: area.toFixed(2) + ' م²', color: '#3b82f6' },
                { label: 'المساحة الإجمالية مع الهالك', value: areaWithWaste.toFixed(2) + ' م²', color: '#10b981' },
                { label: 'المساحة المشتراة فعلياً بالكراتين', value: actualTotalArea.toFixed(2) + ' م²', color: '#8b5cf6' },
                { label: 'مساحة الكرتونة الواحدة', value: mPerBox + ' م²', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>لتغطية مساحة صافية <strong>${area} م²</strong> بنسبة هالك <strong>${(waste*100).toFixed(0)}%</strong>، تحتاج إلى <strong>${boxesNeeded} كرتونة</strong> تشتمل على <strong>${actualTotalArea.toFixed(2)} متر مربع</strong> فعلياً.</p>
            `);
        
        saveLastInputs('ceramic-calculator');
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
    restoreLastInputs('ceramic-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>