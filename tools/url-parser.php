<?php
/**
 * أداة: تحليل الروابط وفصل المعاملات (URL Parser & Query Params)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'url-parser';
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
        <label class="form-label" for="urlToParseInput">أدخل الرابط المراد تحليله وفحصه</label>
        <input type="text" id="urlToParseInput" class="form-control" value="https://www.example.com:8080/products/shoes?category=running&size=43&sort=price_asc#reviews"    placeholder="https://..." oninput="calculateTool()">
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
  0 => 'يفكك الرابط بدقة إلى: البروتوكول، المنفذ (Port)، النطاق، المسار، ومعاملات البحث (Query Parameters)، والمرسى الداخلي (Hash).',
  1 => 'مفيد جداً للمطورين لفحص معاملات الحملات التسويقية (UTM Parameters) واختبار تكامل الـ APIs.',
),
        'الرابط يجب أن يكون مكتملاً بالبروتوكول (http:// أو https://).'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هي معاملات UTM في الروابط؟',
    'a' => 'هي معاملات خاصة مثل utm_source و utm_campaign تُضاف لنهاية الرابط لتتبع مصدر الزيارات في Google Analytics.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'url-encode',
  1 => 'extract-urls-from-text',
  2 => 'qr-generator',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const urlStr = document.getElementById('urlToParseInput').value.trim();
            let parsedUrl;

            try {
                parsedUrl = new URL(urlStr);
            } catch (e) {
                alert('الرابط غير صالح! يرجى إدخال رابط يبدأ بـ https:// أو http://');
                return;
            }

            const params = [];
            parsedUrl.searchParams.forEach((val, key) => {
                params.push({ key, val });
            });

            setPrimaryResult(parsedUrl.hostname, 'النطاق الأساسي (Domain / Host)');
            showResultArea();

            setDetailStats([
                { label: 'البروتوكول (Protocol)', value: parsedUrl.protocol, color: '#10b981' },
                { label: 'اسم النطاق (Host)', value: parsedUrl.hostname, color: '#3b82f6' },
                { label: 'مسار الصفحة (Pathname)', value: parsedUrl.pathname, color: '#f59e0b' },
                { label: 'عدد معاملات البحث (Query Params)', value: params.length + ' معاملات', color: '#8b5cf6' }
            ]);

            let paramsHtml = '';
            if (params.length > 0) {
                paramsHtml = '<h4 style="margin:1rem 0 0.5rem">معاملات الرابط (Query Parameters):</h4><table class="table" style="width:100%"><thead><tr><th>المفتاح (Key)</th><th>القيمة (Value)</th></tr></thead><tbody>';
                params.forEach(p => {
                    paramsHtml += `<tr><td><code>${p.key}</code></td><td><strong>${p.val}</strong></td></tr>`;
                });
                paramsHtml += '</tbody></table>';
            } else {
                paramsHtml = '<p style="color:var(--text-muted);margin-top:1rem">لا يحتوي هذا الرابط على معاملات بحث (Query Parameters).</p>';
            }

            if (parsedUrl.hash) {
                paramsHtml += `<p style="margin-top:0.5rem"><strong>المرسى الداخلي (Hash Fragment):</strong> <code>${parsedUrl.hash}</code></p>`;
            }

            setResultContent(paramsHtml);
        
        saveLastInputs('url-parser');
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
    restoreLastInputs('url-parser');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>