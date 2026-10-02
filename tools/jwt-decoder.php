<?php
/**
 * أداة: فك تشفير وقراءة JWT Token (Header & Payload)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'jwt-decoder';
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
        <label class="form-label" for="jwtTokenInput">ألصق رمز الـ JWT Token هنا (المكون من 3 أجزاء تفصلها نقاط)</label>
        <textarea id="jwtTokenInput" class="form-control" rows="6" placeholder="eyJhbGciOi..." oninput="calculateTool()">eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Ik1vaGFtbWFkIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMiwiZXhwIjoxODMxNTQ5MDIyfQ.dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk</textarea>
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
  0 => 'الـ JWT لا يقوم بتشفير البيانات سرياً بل يقوم بترميزها بصيغة Base64Url مع توقيع رقمي.',
  1 => 'فك التشفير يتم محلياً بالكامل داخل متصفحك دون إرسال التوكن إلى أي سيرفر خارجي حفاظاً على خصوصيتك وأمان بياناتك.',
),
        'الأداة تقرأ البيانات ولا تتحقق من صحة التوقيع (Signature Verification) لعدم توفر المفتاح السري Secret Key لديك.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل وضع كلمات المرور داخل JWT آمن؟',
    'a' => 'ممنوع نهائياً؛ لأن أي شخص يمتلك التوكن يستطيع فك ترميزه وقراءة محتويات الـ Payload بسهولة كما ترى في هذه الأداة.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'base64-converter',
  1 => 'json-formatter',
  2 => 'hash-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const token = document.getElementById('jwtTokenInput').value.trim();
            const parts = token.split('.');

            if (parts.length !== 3) {
                alert('رمز JWT غير صالح! رمز الـ JWT يجب أن يتكون من 3 أجزاء مفصولة بنقاط (Header.Payload.Signature).');
                return;
            }

            function base64UrlDecode(str) {
                let base64 = str.replace(/-/g, '+').replace(/_/g, '/');
                while (base64.length % 4) { base64 += '='; }
                return decodeURIComponent(atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
            }

            let headerObj, payloadObj;
            try {
                headerObj = JSON.parse(base64UrlDecode(parts[0]));
                payloadObj = JSON.parse(base64UrlDecode(parts[1]));
            } catch (e) {
                alert('فشل في فك تشفير محتويات الـ Token: ' + e.message);
                return;
            }

            let expiryInfo = 'غير محدد (No exp)';
            let isExpired = false;
            if (payloadObj.exp) {
                const expDate = new Date(payloadObj.exp * 1000);
                isExpired = expDate < new Date();
                expiryInfo = expDate.toLocaleString('ar-EG') + (isExpired ? ' (منتهي الصلاحية ❌)' : ' (صالح ومفعل ✅)');
            }

            setPrimaryResult('تم فك تشفير الـ Token بنجاح (' + (headerObj.alg || 'JWT') + ')', 'حالة الـ Token');
            showResultArea();

            setDetailStats([
                { label: 'حالة الصلاحية والانتهاء', value: isExpired ? 'منتهي الصلاحية ❌' : 'صالح ونشط ✅', color: isExpired ? '#ef4444' : '#10b981' },
                { label: 'تاريخ انتهاء الصلاحية (exp)', value: expiryInfo, color: '#3b82f6' },
                { label: 'خوارزمية التوقيع (Algorithm)', value: headerObj.alg || 'غير محدد', color: '#f59e0b' },
                { label: 'نوع الرمز (Type)', value: headerObj.typ || 'JWT', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem;display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem">
                    <div>
                        <label class="form-label" style="color:#f59e0b"><i class="fas fa-heading"></i> الترويسة (Header):</label>
                        <textarea class="form-control" rows="6" style="font-family:monospace;direction:ltr" readonly>${JSON.stringify(headerObj, null, 2)}</textarea>
                    </div>
                    <div>
                        <label class="form-label" style="color:#10b981"><i class="fas fa-database"></i> البيانات والحمولة (Payload Claims):</label>
                        <textarea class="form-control" rows="6" style="font-family:monospace;direction:ltr" readonly>${JSON.stringify(payloadObj, null, 2)}</textarea>
                    </div>
                </div>
            `);
        
        saveLastInputs('jwt-decoder');
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
    restoreLastInputs('jwt-decoder');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>