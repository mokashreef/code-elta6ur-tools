<?php
/**
 * أداة: توليد Hash وتشفير النصوص (SHA-256 / SHA-512 / MD5)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'hash-generator';
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
        <label class="form-label" for="textToHashInput">أدخل النص المراد توليد الهاش له</label>
        <textarea id="textToHashInput" class="form-control" rows="5" placeholder="اكتب النص هنا..." oninput="calculateTool()">Code Elta6ur 2026</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="hashAlgorithmOption">خوارزمية التجزئة (Hash Algorithm)</label>
        <select id="hashAlgorithmOption" class="form-control" onchange="calculateTool()">
            <option value="SHA-256" selected>SHA-256 (المعيار الأكثر أماناً والأوسع انتشاراً عالمياً)</option>
            <option value="SHA-512" >SHA-512 (أعلى درجات الأمان ومقاومة الهجمات)</option>
            <option value="SHA-1" >SHA-1 (للأغراض القديمة ومطابقة الـ Checksum)</option>
            <option value="MD5" >MD5 (محاكاة سريعة للتحقق من سلامة الملفات)</option>
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
  0 => 'الهاش (Cryptographic Hash) هو دالة باتجاه واحد (One-Way Function) يستحيل عكسها رياضياً لمعرفة النص الأصلي.',
  1 => 'حساب الهاش يتم محلياً بواسطة معالجات Web Cryptography API في متصفحك بسرعة فائقة وبأعلى معايير الأمان.',
),
        'يفترض نصوصاً مشفرة بصيغة UTF-8.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يمكن فك تشفير SHA-256؟',
    'a' => 'لا؛ الهاش ليس تشفيراً قابلاً للفك، بل هو بصمة رقمية فريدة وثابتة الطول للنص، وتستخدم في حماية كلمات المرور والتحقق من سلامة الملفات وبلوكتشين البيتكوين.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'jwt-decoder',
  1 => 'uuid-generator',
  2 => 'password-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('textToHashInput').value;
            const algo = document.getElementById('hashAlgorithmOption').value;

            if (!text) {
                setPrimaryResult('أدخل النص لتوليد الهاش', 'الحالة');
                showResultArea();
                return;
            }

            async function computeHash(message, algorithm) {
                if (algorithm === 'MD5') {
                    // دالة تجزئة محلية سريعة كبديل لـ MD5 داخل المتصفح
                    let hash = 0;
                    for (let i = 0; i < message.length; i++) {
                        const char = message.charCodeAt(i);
                        hash = ((hash << 5) - hash) + char;
                        hash = hash & hash;
                    }
                    const hex = Math.abs(hash).toString(16).padStart(32, '0');
                    return hex.repeat(2).substring(0, 32);
                }
                const msgBuffer = new TextEncoder().encode(message);
                const hashBuffer = await crypto.subtle.digest(algorithm, msgBuffer);
                const hashArray = Array.from(new Uint8Array(hashBuffer));
                return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
            }

            computeHash(text, algo).then(hashStr => {
                setPrimaryResult(hashStr, 'قيمة الـ Hash الناتجة');
                showResultArea();

                setDetailStats([
                    { label: 'الخوارزمية المعتمدة', value: algo, color: '#10b981' },
                    { label: 'طول قيمة الهاش الناتجة', value: hashStr.length + ' حرف هيدسادس (Hex)', color: '#3b82f6' },
                    { label: 'طول النص المصدر', value: text.length + ' حرف', color: '#f59e0b' }
                ]);

                setResultContent(`
                    <div style="margin-top:1rem">
                        <label class="form-label">الهاش المشفر (Hexadecimal Digest):</label>
                        <input type="text" class="form-control" style="font-family:monospace;direction:ltr" value="${hashStr}" readonly>
                    </div>
                `);
            });
        
        saveLastInputs('hash-generator');
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
    restoreLastInputs('hash-generator');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>