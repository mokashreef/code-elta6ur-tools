<?php
/**
 * أداة: حاسبة كمية الدهان
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'paint-calculator';
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
        <label class="form-label" for="roomPerimeter">إجمالي محيط الجدران (متر) - مجموع أطوال الحوائط</label>
        <input type="number" id="roomPerimeter" class="form-control" value="16" min="1"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="wallHeight">ارتفاع الجدار حتى السقف (متر)</label>
        <input type="number" id="wallHeight" class="form-control" value="3.0" min="1.5" max="8" step="0.1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="doorsWindowsDeduct">مساحة الأبواب والنوافذ المخصومة (م²)</label>
        <input type="number" id="doorsWindowsDeduct" class="form-control" value="4.5" min="0"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="coatsCount">عدد أوجه / طبقات الدهان (غالباً وجهين)</label>
        <input type="number" id="coatsCount" class="form-control" value="2" min="1" max="5" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="paintSpreadRate">معدل فرد اللتر (م²/لتر) - عادة 10 إلى 12</label>
        <input type="number" id="paintSpreadRate" class="form-control" value="11" min="5" max="20" step="0.5"  oninput="calculateTool()">
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
  0 => 'مساحة الجدران الإجمالية = محيط الغرفة × الارتفاع.',
  1 => 'المساحة الصافية = المساحة الإجمالية - مساحات الأبواب والنوافذ.',
  2 => 'لترات الدهان = (المساحة الصافية × عدد الأوجه) ÷ معدل تغطية اللتر.',
),
        'يفترض جدران مجهزة بأساس ومعجون؛ الجدران الخشنة غير المدهونة قد تستهلك 20% دهان إضافي.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحتاج السقف لحساب منفصل؟',
    'a' => 'نعم؛ يفضل حساب السقف بشكل مستقل لأنه غالباً يُدهن بلون أبيض مطفي بنوع دهان مخصص للأسقف.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'putty-calculator',
  1 => 'wall-area-calculator',
  2 => 'ceiling-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const perim = Math.max(1, parseFloat(document.getElementById('roomPerimeter').value) || 16);
            const height = Math.max(1.5, parseFloat(document.getElementById('wallHeight').value) || 3.0);
            const deduct = Math.max(0, parseFloat(document.getElementById('doorsWindowsDeduct').value) || 4.5);
            const coats = Math.max(1, parseInt(document.getElementById('coatsCount').value) || 2);
            const spread = Math.max(5, parseFloat(document.getElementById('paintSpreadRate').value) || 11);

            const grossArea = perim * height;
            const netWallArea = Math.max(1, grossArea - deduct);
            const totalCoatsArea = netWallArea * coats;
            const litersNeeded = totalCoatsArea / spread;
            const gallonsNeeded = litersNeeded / 3.75; // جالون أمريكي قياسي 3.75 لتر
            const bucketsNeeded = Math.ceil(litersNeeded / 18); // برميل كبير 18 لتر

            setPrimaryResult(Math.ceil(litersNeeded) + ' لتر دهان (' + gallonsNeeded.toFixed(1) + ' جالون)', 'كمية الدهان الصافية المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'المساحة الصافية للجدران', value: netWallArea.toFixed(1) + ' م²', color: '#3b82f6' },
                { label: 'إجمالي مساحة الطلاء (' + coats + ' طبقات)', value: totalCoatsArea.toFixed(1) + ' م²', color: '#10b981' },
                { label: 'عدد الجالونات القياسية (3.75 لتر)', value: Math.ceil(gallonsNeeded) + ' جالون', color: '#f59e0b' },
                { label: 'عدد البراميل الكبيرة (18 لتر)', value: bucketsNeeded + ' برميل', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لدهان جدران بمساحة صافية <strong>${netWallArea.toFixed(1)} م²</strong> بوجهين (طبقتين)، تحتاج إلى <strong>${Math.ceil(litersNeeded)} لتر</strong>، وهو ما يعادل تقريباً <strong>${Math.ceil(gallonsNeeded)} جالون</strong> أو <strong>${bucketsNeeded} برميل سعة 18 لتر</strong>.</p>
            `);
        
        saveLastInputs('paint-calculator');
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
    restoreLastInputs('paint-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>