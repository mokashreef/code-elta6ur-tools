<?php
/**
 * أداة: حاسبة البطاريات للطاقة الشمسية
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'solar-batteries-calculator';
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
        <label class="form-label" for="nightLoadKwh">الاستهلاك الليلي المطلوب تغطيته من البطاريات (kWh)</label>
        <input type="number" id="nightLoadKwh" class="form-control" value="8" min="0.5"  step="0.5"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="solarBattType">نوع تقنية البطاريات</label>
        <select id="solarBattType" class="form-control" onchange="calculateTool()">
            <option value="lithium" selected>ليثيوم فوسفات الحديد (LiFePO4) - تفريغ 85% وعمر 10 سنوات (الخيار الأفضل)</option>
            <option value="tubular_gel" >جيل أو تيوبلار عميق التفريغ (Tubular Gel) - تفريغ 50% وعمر 3-4 سنوات</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="solarBattVolt">جهد نظام الانفرتر</label>
        <select id="solarBattVolt" class="form-control" onchange="calculateTool()">
            <option value="24" >24 فولت (للمنظومات الصغيرة والمتوسطة حتى 3kW)</option>
            <option value="48" selected>48 فولت (للمنظومات المنزلية الكبيرة 5kW فما فوق - القياسي)</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="solarBattAhSize">سعة البطارية الواحدة المتوفرة</label>
        <select id="solarBattAhSize" class="form-control" onchange="calculateTool()">
            <option value="100" selected>100 أمبير-ساعة Ah</option>
            <option value="200" >200 أمبير-ساعة Ah</option>
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
  0 => 'بطاريات الليثيوم (LiFePO4) توفر أكثر من 5000 إلى 6000 دورة شحن وتفريغ، مقارنة بـ 1200 دورة فقط لبطاريات الجيل والرصاص.',
  1 => 'حجم البطارية يجب أن يغطي الأحمال الليلية الأساسية وأيام الطقس الغائم الجزئي.',
),
        'يفترض تغذية أحمال الإضاءة والتبريد الأساسية أثناء غياب الشمس.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل بطاريات الليثيوم آمنة داخل المنازل؟',
    'a' => 'نعم؛ خلايا ليثيوم فوسفات الحديد (LiFePO4) هي أكثر تقنيات الليثيوم أماناً واستقراراً كيميائياً في العالم، وتأتي بنظام إدارة ذكي مدمج (BMS) يمنع الشحن الزائد والحرارة والتماس الكهربائي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'solar-panels-calculator',
  1 => 'battery-count-calculator',
  2 => 'solar-system-cost-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const kwh = Math.max(0.5, parseFloat(document.getElementById('nightLoadKwh').value) || 8);
            const type = document.getElementById('solarBattType').value;
            const volt = parseFloat(document.getElementById('solarBattVolt').value) || 48;
            const ah = parseFloat(document.getElementById('solarBattAhSize').value) || 100;

            const dod = type === 'lithium' ? 0.85 : 0.50;
            const inverterEff = 0.90;

            const neededWh = (kwh * 1000) / (dod * inverterEff);
            const totalAhAtSysVolt = neededWh / volt;

            // بطارية الليثيوم 48V 100Ah تمثل وحدة 5 kWh (Wall Mount)
            const lithiumModules5kwh = Math.ceil(kwh / (5 * dod * inverterEff));
            const gelBatteriesCount = Math.ceil((neededWh / (ah * 12)) / (volt / 12)) * (volt / 12);

            let primaryResultText = '';
            if (type === 'lithium') {
                primaryResultText = lithiumModules5kwh + ' بطارية ليثيوم حائطية (5 kWh / 48V)';
            } else {
                primaryResultText = gelBatteriesCount + ' بطاريات جيل (' + ah + 'Ah / 12V)';
            }

            setPrimaryResult(primaryResultText, 'سعة وبنك البطاريات المطلوب');
            showResultArea();

            setDetailStats([
                { label: 'السعة الإجمالية المطلوبة لبنك البطاريات', value: Math.ceil(totalAhAtSysVolt) + ' Ah @ ' + volt + 'V', color: '#3b82f6' },
                { label: 'إجمالي طاقة التخزين الاسمية', value: (neededWh / 1000).toFixed(1) + ' kWh', color: '#10b981' },
                { label: 'عمق التفريغ الآمن المعتمد', value: (dod * 100) + '%', color: '#f59e0b' },
                { label: 'العمر الافتراضي المتوقع للبطاريات', value: type === 'lithium' ? '8 إلى 12 سنة (6000 دورة)' : '2 إلى 4 سنوات (1200 دورة)', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتغطية استهلاك ليلي <strong>${kwh} kWh</strong> مع مراعاة عمق التفريغ وكفاءة الانفرتر، تحتاج إلى ${type === 'lithium' ? '<strong>' + lithiumModules5kwh + ' وحدات بطاريات ليثيوم 5.12 kWh</strong>' : '<strong>' + gelBatteriesCount + ' بطارية جيل 12V سعة ' + ah + 'Ah</strong>'}.</p>
            `);
        
        saveLastInputs('solar-batteries-calculator');
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
    restoreLastInputs('solar-batteries-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>