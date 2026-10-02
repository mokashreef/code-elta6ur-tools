<?php
/**
 * أداة: مولد رموز الاستجابة السريعة (QR Code Generator)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'qr-generator';
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
        <label class="form-label" for="qrContentInput">النص أو الرابط المراد تحويله إلى QR Code</label>
        <textarea id="qrContentInput" class="form-control" rows="4" placeholder="ضع الرابط أو النص أو رقم الهاتف هنا..." oninput="calculateTool()">https://example.com</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="qrSizeOption">مقاس الصورة (بكسل)</label>
        <select id="qrSizeOption" class="form-control" onchange="calculateTool()">
            <option value="200" >صغير (200×200 بكسل)</option>
            <option value="300" selected>متوسط (300×300 بكسل - موصى به)</option>
            <option value="500" >كبير عالي الدقة (500×500 بكسل للطباعة)</option>
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
  0 => 'يولد رمز QR عالي الجودة يدعم الروابط، شبكات الواي فاي، أرقام الهواتف، وبطاقات الأعمال vCard.',
  1 => 'قابل للمسح الفوري بواسطة كاميرات جميع الهواتف الذكية (iOS و Android).',
),
        'الرمز يتم إنشاؤه عبر API قياسي مفتوح المصدر ومستقر.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل تنتهي صلاحية كود الـ QR بعد فترة؟',
    'a' => 'لا؛ كود الـ QR الثابت (Static QR) لا تنتهي صلاحيته مدى الحياة لأنه يحتوي البيانات مشفرة بصرياً في مربعات الصورة ذاتها.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'url-encode',
  1 => 'url-parser',
  2 => 'random-string-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const content = document.getElementById('qrContentInput').value.trim();
            const size = document.getElementById('qrSizeOption').value || '300';

            if (!content) {
                setPrimaryResult('أدخل النص أو الرابط أولاً', 'الحالة');
                showResultArea();
                return;
            }

            const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=' + size + 'x' + size + '&data=' + encodeURIComponent(content);

            setPrimaryResult('تم توليد كود QR بنجاح', 'رمز الاستجابة السريعة');
            showResultArea();

            setDetailStats([
                { label: 'أبعاد الصورة الناتجة', value: size + ' × ' + size + ' px', color: '#10b981' },
                { label: 'عدد محارف المحتوى', value: content.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1.5rem;text-align:center">
                    <div style="display:inline-block;padding:1rem;background:#fff;border-radius:var(--radius-md);box-shadow:0 8px 24px rgba(0,0,0,0.25)">
                        <img src="${qrUrl}" alt="QR Code" style="max-width:100%;height:auto;display:block">
                    </div>
                    <div style="margin-top:1rem">
                        <a href="${qrUrl}" target="_blank" download="qrcode.png" class="btn btn-primary">
                            <i class="fas fa-download"></i> تنزيل صورة QR كملف PNG
                        </a>
                    </div>
                </div>
            `);
        
        saveLastInputs('qr-generator');
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
    restoreLastInputs('qr-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>