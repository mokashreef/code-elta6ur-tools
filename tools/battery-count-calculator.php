<?php
/**
 * أداة: حاسبة عدد البطاريات المطلوبة
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'battery-count-calculator';
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
        <label class="form-label" for="dailyEnergyNeedWh">إجمالي الاستهلاك اليومي المطلوب تغطيته بالبطاريات (واط-ساعة Wh)</label>
        <input type="number" id="dailyEnergyNeedWh" class="form-control" value="3000" min="200"  step="100"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="selectedBatteryAh">سعة البطارية الواحدة المتوفرة في السوق (Ah)</label>
        <select id="selectedBatteryAh" class="form-control" onchange="calculateTool()">
            <option value="100" >100 أمبير-ساعة Ah</option>
            <option value="150" >150 أمبير-ساعة Ah</option>
            <option value="200" selected>200 أمبير-ساعة Ah (المقاس الأكثر شيوعاً)</option>
            <option value="250" >250 أمبير-ساعة Ah</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="batteryTypeChemistry">نوع البطارية وعمق التفريغ (DoD)</label>
        <select id="batteryTypeChemistry" class="form-control" onchange="calculateTool()">
            <option value="gel_50" selected>بطارية جيل / تيوبلار Tublar (عمق تفريغ 50%)</option>
            <option value="lithium_85" >بطارية ليثيوم LiFePO4 (عمق تفريغ 85%)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="targetSysVoltage">جهد نظام الانفرتر المستهدف</label>
        <select id="targetSysVoltage" class="form-control" onchange="calculateTool()">
            <option value="12" >12 فولت</option>
            <option value="24" selected>24 فولت</option>
            <option value="48" >48 فولت</option>
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
  0 => 'الاستهلاك بالواط-ساعة (Wh) = مجموع قدرات الأجهزة × ساعات تشغيلها.',
  1 => 'سعة البطارية القابلة للاستخدام = سعة البطارية (Wh) × عمق التفريغ المسموح به × كفاءة التحويل.',
  2 => 'عدد البطاريات يجب أن يكون دائماً من مضاعفات جهد النظام (بطاريتان لنظام 24V، و 4 بطاريات لنظام 48V).',
),
        'يفترض بطاريات فردية بجهد اسمي 12 فولت موصولة بمحول مناسب.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'لماذا يُفضل نظام 24V أو 48V بدلاً من 12V؟',
    'a' => 'لأنه عند رفع الجهد يقل التيار المار في الأسلاك للنصف أو للربع، مما يقلل من فواقد الطاقة ويسمح باستخدام كابلات أقل سماكة ويحمي الانفرتر من السخونة الزائدة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'battery-wiring-calculator',
  1 => 'battery-charging-time-calculator',
  2 => 'solar-batteries-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const wh = Math.max(200, parseFloat(document.getElementById('dailyEnergyNeedWh').value) || 3000);
            const ah = parseFloat(document.getElementById('selectedBatteryAh').value) || 200;
            const chem = document.getElementById('batteryTypeChemistry').value;
            const sysVolt = parseFloat(document.getElementById('targetSysVoltage').value) || 24;

            const dod = chem === 'lithium_85' ? 0.85 : 0.50;
            const inverterLosses = 0.85; // كفاءة الانفرتر

            // سعة البطارية الواحدة بالواط-ساعة
            const singleBatteryWh = ah * 12; // معظم البطاريات الفردية 12V
            const singleBatteryUsableWh = singleBatteryWh * dod * inverterLosses;
            const rawCount = wh / singleBatteryUsableWh;
            // يجب أن يكون العدد مضاعفاً لجهد النظام
            const seriesCount = sysVolt / 12;
            let finalCount = Math.ceil(rawCount / seriesCount) * seriesCount;
            if (finalCount < seriesCount) finalCount = seriesCount;

            const totalAhBank = (finalCount / seriesCount) * ah;

            setPrimaryResult(finalCount + ' بطاريات (سعة ' + ah + ' Ah)', 'عدد البطاريات المطلوبة');
            showResultArea();

            setDetailStats([
                { label: 'سعة بنك البطاريات عند ' + sysVolt + 'V', value: totalAhBank.toFixed(0) + ' Ah (' + (totalAhBank * sysVolt) + ' Wh)', color: '#3b82f6' },
                { label: 'طريقة الربط المطلوبة', value: seriesCount + ' على التوالي × ' + (finalCount / seriesCount) + ' توازي', color: '#10b981' },
                { label: 'عمق التفريغ المعتمد (DoD)', value: (dod * 100) + '%', color: '#f59e0b' },
                { label: 'الطاقة القابلة للاستخدام يومياً', value: Math.round(finalCount * singleBatteryUsableWh) + ' Wh', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية استهلاك <strong>${wh} واط-ساعة</strong> على نظام <strong>${sysVolt} فولت</strong>، تحتاج إلى <strong>${finalCount} بطاريات سعة ${ah} Ah</strong> (12V) بنظام توصيل <strong>${seriesCount} توالي × ${finalCount/seriesCount} توازي</strong>.</p>
            `);
        
        saveLastInputs('battery-count-calculator');
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
    restoreLastInputs('battery-count-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>