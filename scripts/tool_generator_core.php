<?php
/**
 * النواة المشتركة لتوليد صفحات الأدوات (Tool Generator Core)
 * Code Elta6ur Tools Platform
 */
require_once __DIR__ . '/../includes/tools_registry.php';

function generateToolFile($slug, $def, $outputDir = null) {
    if (!$outputDir) {
        $outputDir = __DIR__ . '/../tools';
    }
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    $filePath = $outputDir . '/' . $slug . '.php';
    $title = $def['title'];
    $isFinancial = $def['isFinancial'] ?? false;
    $inputs = $def['inputs'] ?? [];
    $calcJs = $def['calcJs'] ?? '';
    $customHtml = $def['customHtml'] ?? '';
    $customJs = $def['customJs'] ?? '';
    $points = $def['points'] ?? [];
    $assumptions = $def['assumptions'] ?? '';
    $faqs = $def['faqs'] ?? [];
    $related = $def['related'] ?? [];

    $pointsExport = var_export($points, true);
    $faqsExport = var_export($faqs, true);
    $relatedExport = var_export($related, true);

    $htmlInputs = '';
    if (!empty($inputs)) {
        foreach ($inputs as $inp) {
            $id = $inp['id'];
            $label = $inp['label'];
            $type = $inp['type'] ?? 'number';
            $default = $inp['default'] ?? '';
            $min = isset($inp['min']) ? "min=\"{$inp['min']}\"" : '';
            $max = isset($inp['max']) ? "max=\"{$inp['max']}\"" : '';
            $step = isset($inp['step']) ? "step=\"{$inp['step']}\"" : '';

            $htmlInputs .= "    <div class=\"form-group\">\n";
            $htmlInputs .= "        <label class=\"form-label\" for=\"{$id}\">{$label}</label>\n";
            if ($type === 'select') {
                $htmlInputs .= "        <select id=\"{$id}\" class=\"form-control\" onchange=\"calculateTool()\">\n";
                foreach ($inp['options'] as $optVal => $optLabel) {
                    $sel = ($optVal == $default) ? 'selected' : '';
                    $htmlInputs .= "            <option value=\"{$optVal}\" {$sel}>{$optLabel}</option>\n";
                }
                $htmlInputs .= "        </select>\n";
            } elseif ($type === 'textarea') {
                $rows = $inp['rows'] ?? 5;
                $placeholder = $inp['placeholder'] ?? '';
                $htmlInputs .= "        <textarea id=\"{$id}\" class=\"form-control\" rows=\"{$rows}\" placeholder=\"{$placeholder}\" oninput=\"calculateTool()\">{$default}</textarea>\n";
            } else {
                $placeholder = isset($inp['placeholder']) ? "placeholder=\"{$inp['placeholder']}\"" : '';
                $htmlInputs .= "        <input type=\"{$type}\" id=\"{$id}\" class=\"form-control\" value=\"{$default}\" {$min} {$max} {$step} {$placeholder} oninput=\"calculateTool()\">\n";
            }
            $htmlInputs .= "    </div>\n";
        }
    }

    $currencySelector = $isFinancial ? "    <?php renderCurrencySelector('calcCurrency', 'SAR', 'العملة المفضلة للنتائج'); ?>\n" : "";

    $bodyContent = "";
    if (!empty($customHtml)) {
        $bodyContent = $customHtml;
    } else {
        $bodyContent = <<<HTML
    <div class="tool-content-grid">
        <!-- قسم إدخال البيانات -->
        <div class="tool-card card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h text-accent"></i> أدخل البيانات المطلوبة</h3>
            </div>
            <div class="card-body">
{$currencySelector}{$htmlInputs}
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
HTML;
    }

    $scriptBlock = "";
    if (!empty($customJs)) {
        $scriptBlock = $customJs;
    } else {
        $scriptBlock = <<<JS
<script>
function calculateTool() {
    try {
        {$calcJs}
        saveLastInputs('{$slug}');
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
    restoreLastInputs('{$slug}');
    calculateTool();
});
</script>
JS;
    }

    $code = <<<PHP
<?php
/**
 * أداة: {$title}
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
\$slug = '{$slug}';
\$tool = getToolBySlug(\$slug);
if (!\$tool) {
    redirect('index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container tool-container">
    <?php renderToolHeader(\$tool); ?>

{$bodyContent}

    <!-- قسم الشرح والمعادلات -->
    <?php 
    renderToolExplanation(
        'طريقة الحساب والمعادلات المستخدمة',
        {$pointsExport},
        '{$assumptions}'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ({$faqsExport}); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools({$relatedExport}); ?>
</div>

<?php renderToolScriptHelpers(); ?>

{$scriptBlock}

<?php include __DIR__ . '/../includes/footer.php'; ?>
PHP;

    file_put_contents($filePath, $code);
    echo "Generated: tools/{$slug}.php\n";
}
