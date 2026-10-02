<?php
/**
 * أداة: تحليل User Agent ومعلومات المتصفح ونظام التشغيل
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'user-agent-parser';
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
        <label class="form-label" for="userAgentInput">سلسلة User Agent (تم جلب متصفحك الحالي تلقائياً)</label>
        <textarea id="userAgentInput" class="form-control" rows="4" placeholder="Mozilla/5.0..." oninput="calculateTool()"></textarea>
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
  0 => 'يحلل بيانات الترويسة التي يرسلها المتصفح للسيرفر لتحديد إمكانيات الجهاز وإرسال الصفحة المتوافقة معه.',
  1 => 'يساعد المطورين في فحص إحصائيات زوار الموقع والتأكد من دعم المتصفحات المختلفة.',
),
        'يفترض سلاسل User Agent قياسية صادرة من المتصفحات الحديثة.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يحتوي الـ User Agent على معلومات شخصية؟',
    'a' => 'لا؛ فهو يحتوي فقط على الإصدار البرمجي للمتصفح ونوع نواة نظام التشغيل ولا يكشف هويتك أو موقعك الجغرافي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'url-parser',
  1 => 'case-converter',
  2 => 'timestamp-converter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            let ua = document.getElementById('userAgentInput').value.trim();
            if (!ua) {
                ua = navigator.userAgent;
                document.getElementById('userAgentInput').value = ua;
            }

            // فحص المتصفح
            let browser = 'متصفح غير معروف';
            if (ua.includes('Edg/')) browser = 'Microsoft Edge';
            else if (ua.includes('Chrome/') && !ua.includes('Edg/')) browser = 'Google Chrome';
            else if (ua.includes('Safari/') && !ua.includes('Chrome/')) browser = 'Apple Safari';
            else if (ua.includes('Firefox/')) browser = 'Mozilla Firefox';
            else if (ua.includes('MSIE') || ua.includes('Trident/')) browser = 'Internet Explorer';

            // فحص نظام التشغيل
            let os = 'نظام غير معروف';
            if (ua.includes('Windows NT 10.0')) os = 'Windows 10 / 11';
            else if (ua.includes('Windows NT')) os = 'Windows';
            else if (ua.includes('Macintosh') || ua.includes('Mac OS X')) os = 'macOS (Apple)';
            else if (ua.includes('iPhone') || ua.includes('iPad')) os = 'iOS (Apple iPhone/iPad)';
            else if (ua.includes('Android')) os = 'Android';
            else if (ua.includes('Linux')) os = 'Linux';

            // نوع الجهاز
            let device = 'كمبيوتر مكتبي / لابتوب 💻';
            if (ua.includes('Mobile') || ua.includes('Android') || ua.includes('iPhone')) device = 'هاتف ذكي 📱';
            if (ua.includes('iPad') || ua.includes('Tablet')) device = 'جهاز لوحي (تابلت) 📟';

            setPrimaryResult(browser + ' على ' + os, 'المتصفح ونظام التشغيل المكتشف');
            showResultArea();

            setDetailStats([
                { label: 'المتصفح المكتشف', value: browser, color: '#10b981' },
                { label: 'نظام التشغيل (OS)', value: os, color: '#3b82f6' },
                { label: 'نوع الجهاز المقدر', value: device, color: '#f59e0b' },
                { label: 'لغة المتصفح المفضلة', value: navigator.language || 'ar', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>بياناتك المستخرجة: <strong>${browser}</strong> يعمل على بيئة <strong>${os}</strong> من خلال <strong>${device}</strong>.</p>
            `);
        
        saveLastInputs('user-agent-parser');
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
    restoreLastInputs('user-agent-parser');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>