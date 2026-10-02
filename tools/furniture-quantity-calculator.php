<?php
/**
 * أداة: حاسبة كمية الأثاث المطلوبة للمنزل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'furniture-quantity-calculator';
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
        <label class="form-label" for="bedroomsCount">عدد غرف النوم</label>
        <input type="number" id="bedroomsCount" class="form-control" value="3" min="1" max="10" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="familyMembers">عدد أفراد الأسرة المقيمين</label>
        <input type="number" id="familyMembers" class="form-control" value="4" min="1" max="20" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="livingRoomsCount">عدد الصالات والمجالس</label>
        <input type="number" id="livingRoomsCount" class="form-control" value="2" min="1" max="5" step="1"  oninput="calculateTool()">
    </div>
    <div class="form-group">
        <label class="form-label" for="diningType">نوع طاولة الطعام المفضلة</label>
        <select id="diningType" class="form-control" onchange="calculateTool()">
            <option value="family" >طاولة عائلية تسع أفراد الأسرة فقط</option>
            <option value="guests" selected>طاولة كبيرة تسع الأسرة والضيوف (أفراد الأسرة + 4 كراسي)</option>
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
  0 => 'عدد الأسرة ودواليب الملابس يُحسب حسب عدد الأفراد وتوزيع الغرف.',
  1 => 'الصالون الرئيسي يحتاج كنب يتسع لـ 7 إلى 9 أشخاص كمعيار للضيافة العربية.',
),
        'التقدير يلبي الاحتياجات الأساسية مع مراعاة استقبال الضيوف.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أمنع تكديس الأثاث في الغرف؟',
    'a' => 'التزم بقاعدة ألا يزيد الأثاث عن 40% إلى 45% من مساحة الغرفة الإجمالية لترك مسارات حركة مريحة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'room-furniture-area-calculator',
  1 => 'apartment-furnishing-cost-calculator',
  2 => 'carpet-area-calculator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const beds = Math.max(1, parseInt(document.getElementById('bedroomsCount').value) || 3);
            const family = Math.max(1, parseInt(document.getElementById('familyMembers').value) || 4);
            const living = Math.max(1, parseInt(document.getElementById('livingRoomsCount').value) || 2);
            const dining = document.getElementById('diningType').value;

            const totalBeds = family;
            const wardrobes = beds;
            const livingSeats = living * 7; // متوسط مقاعد الكنب
            const diningChairs = dining === 'guests' ? family + 4 : family;

            setPrimaryResult(livingSeats + ' مقعد كنب + ' + diningChairs + ' كراسي طعام', 'كمية المقاعد والجلسات المقترحة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسِرّة المطلوبة', value: totalBeds + ' سرير', color: '#3b82f6' },
                { label: 'عدد خزائن الملابس (دولاب)', value: wardrobes + ' دولاب', color: '#10b981' },
                { label: 'سعة طقم كنب الصالات والمجالس', value: livingSeats + ' أشخاص', color: '#f59e0b' },
                { label: 'كراسي طاولة الطعام', value: diningChairs + ' كراسي', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>لتأثيث المنزل بشكل مريح لأسرة من <strong>${family} أفراد</strong> في <strong>${beds} غرف نوم</strong> و <strong>${living} صالات</strong>، تحتاج إلى <strong>${totalBeds} أسرة</strong>، و <strong>${wardrobes} دواليب ملابس</strong>، وأطقم كنب تتسع لـ <strong>${livingSeats} شخصاً</strong>، وطاولة طعام مع <strong>${diningChairs} كراسٍ</strong>.</p>
            `);
        
        saveLastInputs('furniture-quantity-calculator');
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
    restoreLastInputs('furniture-quantity-calculator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>