<?php
/**
 * أداة: تنسيق وتجميل استعلامات SQL (SQL Formatter)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'sql-formatter';
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
        <label class="form-label" for="rawSqlInput">أدخل استعلام SQL المراد تنسيقه</label>
        <textarea id="rawSqlInput" class="form-control" rows="7" placeholder="ضع استعلام SQL هنا..." oninput="calculateTool()">select u.id, u.name, o.total_price from users u left join orders o on u.id = o.user_id where u.status = 'active' and o.created_at >= '2025-01-01' order by o.total_price desc limit 20;</textarea>
    </div>
    <div class="form-group">
        <label class="form-label" for="capitalizeKeywords">تكبير الكلمات المحجوزة (UPPERCASE Keywords)</label>
        <select id="capitalizeKeywords" class="form-control" onchange="calculateTool()">
            <option value="yes" selected>نعم، تكبير الكلمات المحجوزة (موصى به)</option>
            <option value="no" >لا، تركها كما هي</option>
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
  0 => 'يكبر كلمات SQL القياسية مثل SELECT و FROM و WHERE و JOIN لتسهيل تمييزها عن أسماء الجداول والحقول.',
  1 => 'يفصل أجزاء الاستعلام في أسطر مستقلة تجعل الاستعلامات الطويلة واضحة ومقروءة.',
),
        'متوافق مع محركات MySQL و PostgreSQL و SQL Server و SQLite.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'هل يفيد تكبير كلمات SQL في سرعة الأداء؟',
    'a' => 'قواعد البيانات تعالج الكلمات دون حساسية لحالة الأحرف، ولكن التكبير هو المعيار العالمي المعتمد بين مهندسي البيانات لتسهيل قراءة الاستعلامات ومراجعتها.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'sql-minifier',
  1 => 'json-formatter',
  2 => 'text-diff-checker',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>

<script>
function calculateTool() {
    try {
        
            const sql = document.getElementById('rawSqlInput').value.trim();
            const upper = document.getElementById('capitalizeKeywords').value === 'yes';

            const keywords = [
                'SELECT', 'FROM', 'WHERE', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'JOIN',
                'ON', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 'INSERT INTO', 'VALUES',
                'UPDATE', 'SET', 'DELETE', 'AND', 'OR', 'NOT', 'IN', 'IS NULL', 'IS NOT NULL',
                'UNION', 'UNION ALL', 'DISTINCT', 'AS', 'COUNT', 'SUM', 'AVG', 'MAX', 'MIN',
                'DESC', 'ASC', 'OFFSET', 'CASE', 'WHEN', 'THEN', 'ELSE', 'END'
            ];

            let formatted = sql;

            // تكبير الكلمات المحجوزة
            if (upper) {
                keywords.forEach(kw => {
                    const regex = new RegExp('\\b' + kw + '\\b', 'gi');
                    formatted = formatted.replace(regex, kw);
                });
            }

            // إضافة فواصل أسطر قبل الكلمات الأساسية
            const breakKeywords = ['SELECT', 'FROM', 'WHERE', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 'SET', 'VALUES'];
            breakKeywords.forEach(kw => {
                const regex = new RegExp('\\s+(' + kw + ')\\s+', 'gi');
                formatted = formatted.replace(regex, '\n$1 ');
            });

            // تنسيق الفواصل في جملة SELECT
            formatted = formatted.replace(/,\s*/g, ',\n  ');
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق استعلام SQL باحترافية', 'حالة الاستعلام');
            showResultArea();

            setDetailStats([
                { label: 'عدد أسطر الاستعلام المنسق', value: formatted.split(/\n/).length + ' أسطر', color: '#10b981' },
                { label: 'تكبير الكلمات المفتاحية', value: upper ? 'مفعل (UPPERCASE)' : 'معطل', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style="margin-top:1rem">
                    <label class="form-label">استعلام SQL المنسق الجاهز للتشغيل:</label>
                    <textarea class="form-control" rows="8" style="font-family:monospace;direction:ltr" readonly>${formatted}</textarea>
                </div>
            `);
        
        saveLastInputs('sql-formatter');
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
    restoreLastInputs('sql-formatter');
    calculateTool();
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>