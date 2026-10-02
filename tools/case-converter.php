<?php
/**
 * أداة: محول تسمية المتغيرات البرمجية (Case Converter)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'case-converter';
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
        <label class="form-label" for="caseTextInput">أدخل اسم المتغير أو العبارة الإنجليزية</label>
        <input type="text" id="caseTextInput" class="form-control" value="user profile data service"    placeholder="user profile data service" oninput="calculateTool()">
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
  0 => 'التحويل التلقائي بين كافة أنماط التسمية المستخدمة في أشهر لغات البرمجة وأطر العمل.',
  1 => 'يدعم الإدخال بأي صيغة سابقة ويفككها لكلمات مفردة بدقة.',
),
        'مخصص للأحرف والكلمات اللاتينية والإنجليزية.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'ما هو المعيار المعتمد في بايثون وجافاسكريبت؟',
    'a' => 'في بايثون المعيار الرسمي هو snake_case لأسماء الدوال والمتغيرات، بينما في جافاسكريبت وتايب سكريبت المعيار هو camelCase.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'text-formatter',
  1 => 'slug-converter',
  2 => 'regex-tester',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const text = document.getElementById('caseTextInput').value.trim();
            if (!text) {
                setPrimaryResult('أدخل النص للتحويل', 'الحالة');
                showResultArea();
                return;
            }

            // تقسيم النص إلى كلمات
            const words = text
                .replace(/([a-z])([A-Z])/g, '$1 $2')
                .replace(/[-_]/g, ' ')
                .split(/\s+/)
                .map(w => w.toLowerCase());

            const camelCase = words.map((w, i) => i === 0 ? w : w.charAt(0).toUpperCase() + w.slice(1)).join('');
            const pascalCase = words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('');
            const snakeCase = words.join('_');
            const kebabCase = words.join('-');
            const constantCase = words.join('_').toUpperCase();
            const titleCase = words.map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');

            setPrimaryResult(camelCase + ' | ' + snakeCase, 'صيغ التسمية البرمجية');
            showResultArea();

            setDetailStats([
                { label: 'camelCase (JS/TS)', value: camelCase, color: '#3b82f6' },
                { label: 'snake_case (Python/SQL)', value: snakeCase, color: '#10b981' },
                { label: 'kebab-case (CSS/URLs)', value: kebabCase, color: '#f59e0b' },
                { label: 'PascalCase (React/C#)', value: pascalCase, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem;display:flex;flex-direction:column;gap:0.5rem">
                    <div class="d-flex justify-between align-center p-1" style="background:var(--bg-glass);border-radius:6px">
                        <span><strong>camelCase:</strong> <code>${camelCase}</code></span>
                        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('${camelCase}')"><i class="fas fa-copy"></i></button>
                    </div>
                    <div class="d-flex justify-between align-center p-1" style="background:var(--bg-glass);border-radius:6px">
                        <span><strong>snake_case:</strong> <code>${snakeCase}</code></span>
                        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('${snakeCase}')"><i class="fas fa-copy"></i></button>
                    </div>
                    <div class="d-flex justify-between align-center p-1" style="background:var(--bg-glass);border-radius:6px">
                        <span><strong>kebab-case:</strong> <code>${kebabCase}</code></span>
                        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('${kebabCase}')"><i class="fas fa-copy"></i></button>
                    </div>
                    <div class="d-flex justify-between align-center p-1" style="background:var(--bg-glass);border-radius:6px">
                        <span><strong>PascalCase:</strong> <code>${pascalCase}</code></span>
                        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('${pascalCase}')"><i class="fas fa-copy"></i></button>
                    </div>
                    <div class="d-flex justify-between align-center p-1" style="background:var(--bg-glass);border-radius:6px">
                        <span><strong>CONSTANT_CASE:</strong> <code>${constantCase}</code></span>
                        <button class="btn btn-ghost btn-sm" onclick="navigator.clipboard.writeText('${constantCase}')"><i class="fas fa-copy"></i></button>
                    </div>
                </div>
            `);
        
        saveLastInputs('case-converter');
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
    restoreLastInputs('case-converter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>