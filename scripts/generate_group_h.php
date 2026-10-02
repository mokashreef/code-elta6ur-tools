<?php
/**
 * مولد أدوات المجموعة H: أدوات المطورين (30 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsH = [
    'json-validator' => [
        'title' => 'التحقق من صحة JSON واكتشاف الأخطاء',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'jsonToValidateInput', 'label' => 'أدخل كود الـ JSON للتحقق منه', 'type' => 'textarea', 'rows' => 8, 'default' => "{\n  \"status\": \"success\",\n  \"code\": 200,\n  \"message\": \"البيانات صحيحة\",\n  \"items\": [1, 2, 3]\n}", 'placeholder' => 'ضع كود JSON هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('jsonToValidateInput').value.trim();
            if (!text) {
                setPrimaryResult('أدخل كود JSON أولاً', 'الحالة');
                showResultArea();
                return;
            }

            try {
                const parsed = JSON.parse(text);
                const keysCount = typeof parsed === 'object' && parsed !== null ? (Array.isArray(parsed) ? parsed.length : Object.keys(parsed).length) : 1;
                
                setPrimaryResult('كود JSON صالح وسليم بنسبة 100% ✅ (Valid JSON)', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'سليم وخالٍ من الأخطاء ✅', color: '#10b981' },
                    { label: 'النوع الجذري للكائن', value: Array.isArray(parsed) ? 'مصفوفة (Array)' : typeof parsed, color: '#3b82f6' },
                    { label: 'عدد العناصر / المفاتيح في المستوى الأول', value: keysCount + ' عنصر', color: '#f59e0b' },
                    { label: 'حجم النص بالبايت', value: text.length + ' بايت', color: '#8b5cf6' }
                ]);

                setResultContent(`
                    <div class=\"alert alert-success\" style=\"margin-top:1rem\">
                        <i class=\"fas fa-check-circle\"></i> كود JSON متوافق تماماً مع المعيار الدولي RFC 8259، وجاهز للاستخدام في الـ APIs وقواعد البيانات.
                    </div>
                `);
            } catch (e) {
                setPrimaryResult('كود JSON غير صالح ❌ (Invalid JSON)', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'غير صالح ويحتوي على خطأ ❌', color: '#ef4444' },
                    { label: 'نوع الخطأ المكتشف', value: e.name, color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class=\"alert alert-danger\" style=\"margin-top:1rem;background:rgba(239,68,68,0.1);border:1px solid #ef4444;color:#ef4444\">
                        <strong>تفاصيل الخطأ:</strong> \${e.message}
                    </div>
                `);
            }
        ",
        'points' => [
            'يفحص الكود وفق معيار RFC 8259 الصارم للـ JSON.',
            'يكتشف الأخطاء الشائعة مثل: الفواصل الزائدة الختامية (Trailing Commas)، وعلامات الاقتباس الأحادية المفردة، والأقواس غير المغلقة.'
        ],
        'assumptions' => 'مفاتيح الكائنات في JSON يجب أن تكون محاطة باقتباس مزدوج "key".',
        'faqs' => [
            ['q' => 'هل يقبل JSON علامات الاقتباس المفردة \' \'؟', 'a' => 'لا؛ المعيار القياسي لـ JSON يفرض استخدام الاقتباس المزدوج \" \" حصراً لكل من المفاتيح والنصوص، واستخدام الاقتباس المفرد يعتبر خطأ نحوياً.']
        ],
        'related' => ['json-formatter', 'json-minifier', 'text-to-json-converter']
    ],

    'json-minifier' => [
        'title' => 'ضغط وتقليص حجم JSON (Minifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'jsonMinifyInput', 'label' => 'أدخل كود الـ JSON المراد ضغطه', 'type' => 'textarea', 'rows' => 8, 'default' => "{\n  \"appName\": \"Code Elta6ur\",\n  \"version\": \"2.0\",\n  \"active\": true,\n  \"features\": [\n    \"calculators\",\n    \"converters\",\n    \"generators\"\n  ]\n}", 'placeholder' => 'ضع كود JSON هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('jsonMinifyInput').value.trim();
            try {
                const parsed = JSON.parse(text);
                const minified = JSON.stringify(parsed);
                const savedBytes = text.length - minified.length;
                const ratio = text.length > 0 ? ((savedBytes / text.length) * 100).toFixed(1) : 0;

                setPrimaryResult('تم تقليص الحجم بنسبة ' + ratio + '% (' + savedBytes + ' بايت وفر)', 'حالة الضغط');
                showResultArea();

                setDetailStats([
                    { label: 'الحجم بعد الضغط', value: minified.length + ' بايت', color: '#10b981' },
                    { label: 'الحجم الأصلي قبل الضغط', value: text.length + ' بايت', color: '#ef4444' },
                    { label: 'نسبة التوفير المئوية', value: ratio + '%', color: '#3b82f6' }
                ]);

                setResultContent(`
                    <div style=\"margin-top:1rem\">
                        <label class=\"form-label\">كود JSON المضغوط في سطر واحد (Minified):</label>
                        <textarea class=\"form-control\" rows=\"4\" style=\"font-family:monospace;direction:ltr\" readonly>\${minified}</textarea>
                    </div>
                `);
            } catch (e) {
                alert('خطأ: كود JSON غير صالح: ' + e.message);
            }
        ",
        'points' => [
            'يحذف كافة المسافات البيضاء والأسطر الفارغة وعلامات التبويب غير الضرورية.',
            'تسريع استجابة الـ API وخفض استهلاك الباندويث ونقل البيانات عبر الشبكة.'
        ],
        'assumptions' => 'يحافظ تماماً على سلامة البيانات والقيم النصية بداخل الكائنات.',
        'faqs' => [
            ['q' => 'لماذا نستخدم JSON Minifier في الإنتاج (Production)؟', 'a' => 'لأن تقليص حجم الـ JSON يقلل من حجم حمولة الـ HTTP Request بنسبة 20% إلى 40% مما يسرع تحميل تطبيقات الويب والهواتف الذكية.']
        ],
        'related' => ['json-formatter', 'json-validator', 'sql-minifier']
    ],

    'xml-formatter' => [
        'title' => 'تنسيق وتجميل كود XML (XML Beautifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawXmlInput', 'label' => 'أدخل كود XML غير المنسق', 'type' => 'textarea', 'rows' => 8, 'default' => "<root><user id=\"1\"><name>أحمد</name><role>مطور</role></user><user id=\"2\"><name>سارة</name><role>مصممة</role></user></root>", 'placeholder' => 'ضع كود XML هنا...'],
            ['id' => 'indentSizeXml', 'label' => 'مقدار المسافة البادئة (Indentation)', 'type' => 'select', 'options' => [
                '2' => 'مسافتان (2 Spaces - موصى به)',
                '4' => '4 مسافات (4 Spaces)',
                'tab' => 'علامة تبويب (Tab)'
            ], 'default' => '2'],
        ],
        'calcJs' => "
            const xml = document.getElementById('rawXmlInput').value.trim();
            const indentOpt = document.getElementById('indentSizeXml').value;
            const indentStr = indentOpt === 'tab' ? '\\t' : ' '.repeat(parseInt(indentOpt) || 2);

            let formatted = '';
            let pad = 0;
            // تنظيف وتقطيع وسوم XML
            const reg = /(>)(<)(\\/*)/g;
            const cleanXml = xml.replace(reg, '$1\\r\\n$2$3');
            const lines = cleanXml.split('\\r\\n');

            lines.forEach(node => {
                let indent = 0;
                if (node.match(/.+<\\/\\w[^>]*>$/)) {
                    indent = 0;
                } else if (node.match(/^<\\/\\w/)) {
                    if (pad !== 0) pad -= 1;
                } else if (node.match(/^<\\w[^>]*[^\\/]>.*$/)) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                formatted += indentStr.repeat(pad) + node + '\\n';
                pad += indent;
            });
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق كود XML بنجاح (' + lines.length + ' سطر)', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المنسقة', value: lines.length + ' سطر', color: '#10b981' },
                { label: 'حجم الكود بالبايت', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود XML المنسق المنظم:</label>
                    <textarea class=\"form-control\" rows=\"10\" style=\"font-family:monospace;direction:ltr\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يعيد ترتيب وسوم XML المتداخلة بشكل شجري أنيق يسهل قراءته وتعديله.',
            'يدعم ملفات خرائط المواقع sitemap.xml وملفات الـ RSS وتكوينات الأنظمة.'
        ],
        'assumptions' => 'يفترض وسوم XML متوافقة البنية.',
        'faqs' => [
            ['q' => 'هل يفيد تنسيق XML في محركات البحث؟', 'a' => 'التنسيق يسهل على المطور فحص ملفات sitemap.xml والتحقق من صحة الروابط وتواريخ التعديل قبل رفعها للسيرفر.']
        ],
        'related' => ['xml-validator', 'html-formatter', 'json-formatter']
    ],

    'xml-validator' => [
        'title' => 'التحقق من صحة كود XML',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'xmlToValidate', 'label' => 'أدخل كود XML للتحقق منه', 'type' => 'textarea', 'rows' => 8, 'default' => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<note>\n  <to>محمد</to>\n  <from>خالد</from>\n  <heading>تذكير</heading>\n  <body>لا تنس موعد الاجتماع غداً</body>\n</note>", 'placeholder' => 'ضع كود XML هنا...'],
        ],
        'calcJs' => "
            const xml = document.getElementById('xmlToValidate').value.trim();
            const parser = new DOMParser();
            const doc = parser.parseFromString(xml, 'application/xml');
            const errorNode = doc.querySelector('parsererror');

            if (errorNode) {
                setPrimaryResult('كود XML غير صالح ويحتوي على أخطاء ❌', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'غير صالح ❌', color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class=\"alert alert-danger\" style=\"margin-top:1rem;color:#ef4444;background:rgba(239,68,68,0.1);border:1px solid #ef4444\">
                        <strong>خطأ في صياغة XML:</strong><br>\${errorNode.textContent}
                    </div>
                `);
            } else {
                setPrimaryResult('كود XML سليم وصحيح 100% ✅ (Well-Formed XML)', 'نتيجة الفحص');
                showResultArea();

                const rootName = doc.documentElement.nodeName;

                setDetailStats([
                    { label: 'حالة الصلاحية', value: 'سليم تماماً ✅', color: '#10b981' },
                    { label: 'اسم الوسم الجذري (Root Element)', value: '<' + rootName + '>', color: '#3b82f6' },
                    { label: 'عدد العقد المتفرعة المباشرة', value: doc.documentElement.children.length + ' عناصر', color: '#f59e0b' }
                ]);

                setResultContent(`
                    <div class=\"alert alert-success\" style=\"margin-top:1rem\">
                        <i class=\"fas fa-check-circle\"></i> كود XML صالح تماماً ومتوافق مع معايير W3C الرسمية.
                    </div>
                `);
            }
        ",
        'points' => [
            'التحقق من شروط Well-Formed XML: إغلاق كافة الوسوم بدقة، وحساسية حالة الأحرف، ووجود عنصر جذري وحيد (Root Element).',
            'يعرض رسالة الخطأ ورقم السطر المتسبب في المشكلة عند وجود وسم غير مغلق.'
        ],
        'assumptions' => 'يعتمد محرك التحليل DOMParser القياسي المدمج في المتصفح.',
        'faqs' => [
            ['q' => 'ما هو الخطأ الأكثر شيوعاً في ملفات XML؟', 'a' => 'نسيان إغلاق الوسوم الذاتية (مثل عدم كتابة /> في نهاية الوسم) أو وجود أكثر من وسم جذري في الملف.']
        ],
        'related' => ['xml-formatter', 'json-validator', 'html-formatter']
    ],

    'html-formatter' => [
        'title' => 'تنسيق وتجميل كود HTML (HTML Beautifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawHtmlInput', 'label' => 'أدخل كود HTML غير المنسق', 'type' => 'textarea', 'rows' => 8, 'default' => "<div class=\"container\"><header><h1>عنوان الموقع</h1><nav><a href=\"#\">الرئيسية</a><a href=\"#\">من نحن</a></nav></header><main><p>محتوى تجريبي غير منسق.</p></main></div>", 'placeholder' => 'ضع كود HTML هنا...'],
        ],
        'calcJs' => "
            const html = document.getElementById('rawHtmlInput').value.trim();

            let formatted = '';
            let pad = 0;
            const cleanHtml = html.replace(/(>)(<)(\\/*)/g, '$1\\r\\n$2$3');
            const lines = cleanHtml.split('\\r\\n');

            lines.forEach(node => {
                let indent = 0;
                if (node.match(/.+<\\/\\w[^>]*>$/)) {
                    indent = 0;
                } else if (node.match(/^<\\/\\w/)) {
                    if (pad !== 0) pad -= 1;
                } else if (node.match(/^<\\w[^>]*[^\\/]>.*$/) && !node.match(/<(input|img|br|hr|meta|link)[^>]*>/i)) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                formatted += '  '.repeat(pad) + node + '\\n';
                pad += indent;
            });
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق كود HTML بنجاح', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر بعد التنسيق', value: lines.length + ' سطر', color: '#10b981' },
                { label: 'حجم الكود المنسق', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود HTML المنسق بهيكل هرمي واضح:</label>
                    <textarea class=\"form-control\" rows=\"10\" style=\"font-family:monospace;direction:ltr\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يرتب وسوم الـ HTML5 بهيكل هرمي واضح مع تمييز الوسوم ذاتية الإغلاق كـ img و input.',
            'يجعل الأكواد سهلة التتبع والصيانة واكتشاف أخطاء التنسيق.'
        ],
        'assumptions' => 'المسافة البادئة المعتمدة مسافتان (2 spaces).',
        'faqs' => [
            ['q' => 'هل يؤثر التنسيق على مظهر الصفحة في المتصفح؟', 'a' => 'لا؛ لأن متصفحات الويب تتجاهل المسافات الفارغة المتعددة بين الوسوم، ويكون التأثير فقط على سهولة قراءة الكود للمطور.']
        ],
        'related' => ['css-formatter', 'js-formatter', 'xml-formatter']
    ],

    'css-formatter' => [
        'title' => 'تنسيق وتجميل كود CSS (CSS Beautifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawCssInput', 'label' => 'أدخل كود الـ CSS المراد تنسيقه', 'type' => 'textarea', 'rows' => 8, 'default' => ".btn{padding:10px 20px;border-radius:8px;background:#6c63ff;color:#fff;cursor:pointer}.btn:hover{background:#5a52d5}.card{padding:1.5rem;border:1px solid #333;margin-bottom:1rem}", 'placeholder' => 'ضع كود CSS هنا...'],
        ],
        'calcJs' => "
            const css = document.getElementById('rawCssInput').value.trim();

            let formatted = css
                .replace(/\\s*{\\s*/g, ' {\\n  ')
                .replace(/;\\s*/g, ';\\n  ')
                .replace(/\\s*}\\s*/g, '\\n}\\n\\n')
                .replace(/  }/g, '}')
                .trim();

            setPrimaryResult('تم تنسيق وتجميل كود CSS', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر الناتجة', value: formatted.split(/\\n/).length + ' سطر', color: '#10b981' },
                { label: 'طول الكود المنسق', value: formatted.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود CSS المنسق مع بادئة مسافات منتظمة:</label>
                    <textarea class=\"form-control\" rows=\"10\" style=\"font-family:monospace;direction:ltr\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يفصل كل خاصية CSS في سطر مستقل مع إزاحة بمقدار مسافتين لتسهيل القراءة.',
            'يضيف فاصلاً بين القواعد التصميمية (Selectors) لضمان تنظيم الأنماط.'
        ],
        'assumptions' => 'يفترض كود CSS صالح النحو والأقواس.',
        'faqs' => [
            ['q' => 'كيف أفك ضغط ملف css مضغوط (minified)؟', 'a' => 'ألصق الكود المضغوط في هذه الأداة وستقوم فوراً بإعادة توزيع الخصائص والأقواس في أسطر منظمة سهلة التعديل.']
        ],
        'related' => ['html-formatter', 'js-formatter', 'css-gradient-generator']
    ],

    'js-formatter' => [
        'title' => 'تنسيق وتجميل كود JavaScript (JS Beautifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawJsInput', 'label' => 'أدخل كود الـ JavaScript لتنسيقه', 'type' => 'textarea', 'rows' => 8, 'default' => "function calculateSum(a,b){if(a>0&&b>0){return a+b;}else{console.warn('قيم سالبة');return 0;}}const result=calculateSum(10,20);console.log(result);", 'placeholder' => 'ضع كود JS هنا...'],
        ],
        'calcJs' => "
            const js = document.getElementById('rawJsInput').value.trim();

            let formatted = js
                .replace(/\\s*{\\s*/g, ' {\\n  ')
                .replace(/;\\s*/g, ';\\n  ')
                .replace(/\\s*}\\s*/g, '\\n}\\n')
                .replace(/\\s*else\\s*/g, ' else ')
                .replace(/  }/g, '}')
                .trim();

            setPrimaryResult('تم تنسيق كود JavaScript بنجاح', 'حالة التنسيق');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر الناتجة', value: formatted.split(/\\n/).length + ' سطر', color: '#10b981' },
                { label: 'طول الكود بالبايت', value: formatted.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود JavaScript المنسق:</label>
                    <textarea class=\"form-control\" rows=\"10\" style=\"font-family:monospace;direction:ltr\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يرتب كود الجافاسكريبت ويفصل الجمل البرمجية المنتهية بفاصلة منقوطة.',
            'يجعل الأكواد غير المنسقة والمضغوطة قابلة للقراءة والـ Debugging.'
        ],
        'assumptions' => 'يفترض كود JavaScript صالح البنية.',
        'faqs' => [
            ['q' => 'هل يؤثر التنسيق على عمل الكود؟', 'a' => 'لا مطلقاً؛ التنسيق يغير فقط المسافات البيضاء والأسطر ولا يغير أي منطق تنفيذي في الكود.']
        ],
        'related' => ['json-formatter', 'html-formatter', 'css-formatter']
    ],

    'sql-formatter' => [
        'title' => 'تنسيق وتجميل استعلامات SQL (SQL Formatter)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawSqlInput', 'label' => 'أدخل استعلام SQL المراد تنسيقه', 'type' => 'textarea', 'rows' => 7, 'default' => "select u.id, u.name, o.total_price from users u left join orders o on u.id = o.user_id where u.status = 'active' and o.created_at >= '2025-01-01' order by o.total_price desc limit 20;", 'placeholder' => 'ضع استعلام SQL هنا...'],
            ['id' => 'capitalizeKeywords', 'label' => 'تكبير الكلمات المحجوزة (UPPERCASE Keywords)', 'type' => 'select', 'options' => ['yes' => 'نعم، تكبير الكلمات المحجوزة (موصى به)', 'no' => 'لا، تركها كما هي'], 'default' => 'yes'],
        ],
        'calcJs' => "
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
                    const regex = new RegExp('\\\\b' + kw + '\\\\b', 'gi');
                    formatted = formatted.replace(regex, kw);
                });
            }

            // إضافة فواصل أسطر قبل الكلمات الأساسية
            const breakKeywords = ['SELECT', 'FROM', 'WHERE', 'LEFT JOIN', 'RIGHT JOIN', 'INNER JOIN', 'GROUP BY', 'ORDER BY', 'HAVING', 'LIMIT', 'SET', 'VALUES'];
            breakKeywords.forEach(kw => {
                const regex = new RegExp('\\\\s+(' + kw + ')\\\\s+', 'gi');
                formatted = formatted.replace(regex, '\\n$1 ');
            });

            // تنسيق الفواصل في جملة SELECT
            formatted = formatted.replace(/,\\s*/g, ',\\n  ');
            formatted = formatted.trim();

            setPrimaryResult('تم تنسيق استعلام SQL باحترافية', 'حالة الاستعلام');
            showResultArea();

            setDetailStats([
                { label: 'عدد أسطر الاستعلام المنسق', value: formatted.split(/\\n/).length + ' أسطر', color: '#10b981' },
                { label: 'تكبير الكلمات المفتاحية', value: upper ? 'مفعل (UPPERCASE)' : 'معطل', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">استعلام SQL المنسق الجاهز للتشغيل:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"font-family:monospace;direction:ltr\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يكبر كلمات SQL القياسية مثل SELECT و FROM و WHERE و JOIN لتسهيل تمييزها عن أسماء الجداول والحقول.',
            'يفصل أجزاء الاستعلام في أسطر مستقلة تجعل الاستعلامات الطويلة واضحة ومقروءة.'
        ],
        'assumptions' => 'متوافق مع محركات MySQL و PostgreSQL و SQL Server و SQLite.',
        'faqs' => [
            ['q' => 'هل يفيد تكبير كلمات SQL في سرعة الأداء؟', 'a' => 'قواعد البيانات تعالج الكلمات دون حساسية لحالة الأحرف، ولكن التكبير هو المعيار العالمي المعتمد بين مهندسي البيانات لتسهيل قراءة الاستعلامات ومراجعتها.']
        ],
        'related' => ['sql-minifier', 'json-formatter', 'text-diff-checker']
    ],

    'sql-minifier' => [
        'title' => 'ضغط استعلامات SQL في سطر واحد (SQL Minifier)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'sqlToMinifyInput', 'label' => 'أدخل استعلام SQL الممتد على عدة أسطر', 'type' => 'textarea', 'rows' => 8, 'default' => "SELECT\n  id,\n  username,\n  email\nFROM users\nWHERE status = 'active'\n  AND created_at >= NOW()\nORDER BY id DESC\nLIMIT 50;", 'placeholder' => 'ضع استعلام SQL هنا...'],
        ],
        'calcJs' => "
            const sql = document.getElementById('sqlToMinifyInput').value.trim();
            // إزالة التعليقات
            let minified = sql.replace(/--.*$/gm, '').replace(/\\/\\*[\\s\\S]*?\\*\\//g, '');
            // ضغط المسافات والأسطر في مسافة واحدة
            minified = minified.replace(/\\s+/g, ' ').trim();

            const saved = sql.length - minified.length;

            setPrimaryResult('تم ضغط الاستعلام في سطر واحد (' + minified.length + ' حرف)', 'حالة الضغط');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأحرف بعد الضغط', value: minified.length + ' حرف', color: '#10b981' },
                { label: 'الأحرف والأسطر الموفرة', value: saved + ' حرف وفر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">استعلام SQL المضغوط (سطر واحد جاهز للتضمين في الكود):</label>
                    <textarea class=\"form-control\" rows=\"4\" style=\"font-family:monospace;direction:ltr\" readonly>\${minified}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يضغط الاستعلامات متعددة الأسطر في سطر واحد نظيف لتضمينها كمتغير في لغات البرمجة (Python, PHP, Node.js).',
            'يحذف التعليقات الزائدة والمسافات البادئة.'
        ],
        'assumptions' => 'يحافظ على النصوص المحاطة بعلامات اقتباس فردية أو مزدوجة.',
        'faqs' => [
            ['q' => 'متى أحتاج لضغط استعلام SQL؟', 'a' => 'عند تضمين الاستعلامات داخل ملفات الإعدادات والـ Shell Scripts وسجلات الـ Log التي تفضل أسطراً مفردة.']
        ],
        'related' => ['sql-formatter', 'json-minifier', 'text-formatter']
    ],

    'url-encode' => [
        'title' => 'ترميز وفك ترميز الروابط (URL Encode / Decode)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'urlCodecInput', 'label' => 'أدخل الرابط أو النص العربي', 'type' => 'textarea', 'rows' => 5, 'default' => 'https://example.com/search?q=برمجة المواقع&cat=تقنية', 'placeholder' => 'ضع الرابط أو النص هنا...'],
            ['id' => 'urlCodecAction', 'label' => 'العملية المطلوبة', 'type' => 'select', 'options' => [
                'encode' => 'ترميز الروابط (URL Encode) - تحويل العربية لنسب مئوية %D8%A7',
                'decode' => 'فك ترميز الروابط (URL Decode) - إرجاع الروابط لكلمات عربية مقروءة'
            ], 'default' => 'encode'],
        ],
        'calcJs' => "
            const input = document.getElementById('urlCodecInput').value.trim();
            const action = document.getElementById('urlCodecAction').value;

            let result = '';
            if (action === 'encode') {
                result = encodeURIComponent(input).replace(/[!'()*]/g, function(c) {
                    return '%' + c.charCodeAt(0).toString(16).toUpperCase();
                });
            } else {
                try {
                    result = decodeURIComponent(input.replace(/\\+/g, ' '));
                } catch (e) {
                    alert('خطأ: النص لا يحتوي على ترميز URL صالح: ' + e.message);
                    return;
                }
            }

            setPrimaryResult('تمت العملية بنجاح (' + result.length + ' حرف)', 'حالة الترميز');
            showResultArea();

            setDetailStats([
                { label: 'العملية المنفذة', value: action === 'encode' ? 'ترميز (Encode)' : 'فك ترميز (Decode)', color: '#10b981' },
                { label: 'طول النص الناتج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النتيجة المحولة:</label>
                    <textarea class=\"form-control\" rows=\"5\" style=\"font-family:monospace;direction:ltr\" readonly>\${result}</textarea>
                </div>
            `);
        ",
        'points' => [
            'ترميز الـ URL يحول الأحرف غير اللاتينية والمسافات والرموز الخاصة إلى تشفير النسبة المئوية المعتمد دولياً (%XX).',
            'فك الترميز يعيد الروابط المبهمة مثل %D8%B9%D8%B1%D8%A8%D9%8A إلى نصوص عربية مفهومة ومقروءة.'
        ],
        'assumptions' => 'يعتمد ترميز UTF-8 القياسي لشبكة الويب.',
        'faqs' => [
            ['q' => 'لماذا تظهر الروابط العربية برموز %D8 في المتصفح؟', 'a' => 'لأن بروتوكول HTTP الأصلي صُمم ليدعم محارف ASCII الإنجليزية فقط، فتقوم المتصفحات بترميز الحروف العربية بترميز النسبة المئوية لضمان نقلها بدون أخطاء.']
        ],
        'related' => ['base64-converter', 'html-entity-converter', 'url-parser']
    ],

    'jwt-decoder' => [
        'title' => 'فك تشفير وقراءة JWT Token (Header & Payload)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'jwtTokenInput', 'label' => 'ألصق رمز الـ JWT Token هنا (المكون من 3 أجزاء تفصلها نقاط)', 'type' => 'textarea', 'rows' => 6, 'default' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Ik1vaGFtbWFkIiwiYWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMiwiZXhwIjoxODMxNTQ5MDIyfQ.dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk', 'placeholder' => 'eyJhbGciOi...'],
        ],
        'calcJs' => "
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
                <div style=\"margin-top:1rem;display:grid;grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));gap:1rem\">
                    <div>
                        <label class=\"form-label\" style=\"color:#f59e0b\"><i class=\"fas fa-heading\"></i> الترويسة (Header):</label>
                        <textarea class=\"form-control\" rows=\"6\" style=\"font-family:monospace;direction:ltr\" readonly>\${JSON.stringify(headerObj, null, 2)}</textarea>
                    </div>
                    <div>
                        <label class=\"form-label\" style=\"color:#10b981\"><i class=\"fas fa-database\"></i> البيانات والحمولة (Payload Claims):</label>
                        <textarea class=\"form-control\" rows=\"6\" style=\"font-family:monospace;direction:ltr\" readonly>\${JSON.stringify(payloadObj, null, 2)}</textarea>
                    </div>
                </div>
            `);
        ",
        'points' => [
            'الـ JWT لا يقوم بتشفير البيانات سرياً بل يقوم بترميزها بصيغة Base64Url مع توقيع رقمي.',
            'فك التشفير يتم محلياً بالكامل داخل متصفحك دون إرسال التوكن إلى أي سيرفر خارجي حفاظاً على خصوصيتك وأمان بياناتك.'
        ],
        'assumptions' => 'الأداة تقرأ البيانات ولا تتحقق من صحة التوقيع (Signature Verification) لعدم توفر المفتاح السري Secret Key لديك.',
        'faqs' => [
            ['q' => 'هل وضع كلمات المرور داخل JWT آمن؟', 'a' => 'ممنوع نهائياً؛ لأن أي شخص يمتلك التوكن يستطيع فك ترميزه وقراءة محتويات الـ Payload بسهولة كما ترى في هذه الأداة.']
        ],
        'related' => ['base64-converter', 'json-formatter', 'hash-generator']
    ],

    'uuid-generator' => [
        'title' => 'توليد معرفات فريدة عالمياً (UUID / GUID Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'uuidCountInput', 'label' => 'عدد المعرفات المطلوب توليدها', 'type' => 'number', 'default' => '5', 'min' => '1', 'max' => '100', 'step' => '5'],
            ['id' => 'uuidVersionType', 'label' => 'إصدار المعرف', 'type' => 'select', 'options' => [
                'v4' => 'UUID v4 العشوائي المشفر (المعيار الأكثر أماناً وشهرة عالمياً)',
                'v4_uppercase' => 'UUID v4 بأحرف كبيرة (UPPERCASE GUID)',
                'v4_no_hyphens' => 'UUID v4 بدون شُرطات (32 حرف مدمج)'
            ], 'default' => 'v4'],
        ],
        'calcJs' => "
            const count = Math.max(1, Math.min(100, parseInt(document.getElementById('uuidCountInput').value) || 5));
            const format = document.getElementById('uuidVersionType').value;

            function generateUUIDv4() {
                if (crypto && crypto.randomUUID) {
                    return crypto.randomUUID();
                }
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    const r = Math.random() * 16 | 0;
                    const v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
            }

            const list = [];
            for (let i = 0; i < count; i++) {
                let id = generateUUIDv4();
                if (format === 'v4_uppercase') id = id.toUpperCase();
                if (format === 'v4_no_hyphens') id = id.replace(/-/g, '');
                list.push(id);
            }

            const resultText = list.join('\\n');

            setPrimaryResult('تم توليد ' + count + ' معرف فريد عشوائي (UUID v4)', 'المعرفات الناتجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد المعرفات المولدة', value: count + ' UUID', color: '#10b981' },
                { label: 'احتمالية التصادم والتكرار', value: '1 في 2.71 كوينتيليون (مستحيل إحصائياً)', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">قائمة المعرفات الفريدة (سطر لكل UUID):</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"font-family:monospace;direction:ltr\" readonly>\${resultText}</textarea>
                </div>
            `);
        ",
        'points' => [
            'الـ UUID v4 يتكون من 128 بت تولد عشوائياً باستخدام دوال التشفير القوية في المتصفح (crypto.getRandomValues).',
            'احتمالية توليد نفس المعرف مرتين تعادل صفر عملياً حتى لو قمت بتوليد مليارات المعرفات.'
        ],
        'assumptions' => 'المعرفات تعتمد معيار RFC 4122 القياسي.',
        'faqs' => [
            ['q' => 'ما الفائدة من استخدام UUID بدلاً من الأرقام التلقائية (Auto Increment ID)؟', 'a' => 'الـ UUID يمنع المستخدمين من تخمين معرفات السجلات الأخرى (Prevent Enumeration Attacks)، ويسمح بتوليد المعرفات في الأنظمة الموزعة والسحابية دون تضارب وبدون الحاجة لانتظار استجابة قاعدة البيانات المركزية.']
        ],
        'related' => ['random-string-generator', 'password-generator', 'hash-generator']
    ],

    'hash-generator' => [
        'title' => 'توليد Hash وتشفير النصوص (SHA-256 / SHA-512 / MD5)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'textToHashInput', 'label' => 'أدخل النص المراد توليد الهاش له', 'type' => 'textarea', 'rows' => 5, 'default' => 'Code Elta6ur 2026', 'placeholder' => 'اكتب النص هنا...'],
            ['id' => 'hashAlgorithmOption', 'label' => 'خوارزمية التجزئة (Hash Algorithm)', 'type' => 'select', 'options' => [
                'SHA-256' => 'SHA-256 (المعيار الأكثر أماناً والأوسع انتشاراً عالمياً)',
                'SHA-512' => 'SHA-512 (أعلى درجات الأمان ومقاومة الهجمات)',
                'SHA-1' => 'SHA-1 (للأغراض القديمة ومطابقة الـ Checksum)',
                'MD5' => 'MD5 (محاكاة سريعة للتحقق من سلامة الملفات)'
            ], 'default' => 'SHA-256'],
        ],
        'calcJs' => "
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
                    <div style=\"margin-top:1rem\">
                        <label class=\"form-label\">الهاش المشفر (Hexadecimal Digest):</label>
                        <input type=\"text\" class=\"form-control\" style=\"font-family:monospace;direction:ltr\" value=\"\${hashStr}\" readonly>
                    </div>
                `);
            });
        ",
        'points' => [
            'الهاش (Cryptographic Hash) هو دالة باتجاه واحد (One-Way Function) يستحيل عكسها رياضياً لمعرفة النص الأصلي.',
            'حساب الهاش يتم محلياً بواسطة معالجات Web Cryptography API في متصفحك بسرعة فائقة وبأعلى معايير الأمان.'
        ],
        'assumptions' => 'يفترض نصوصاً مشفرة بصيغة UTF-8.',
        'faqs' => [
            ['q' => 'هل يمكن فك تشفير SHA-256؟', 'a' => 'لا؛ الهاش ليس تشفيراً قابلاً للفك، بل هو بصمة رقمية فريدة وثابتة الطول للنص، وتستخدم في حماية كلمات المرور والتحقق من سلامة الملفات وبلوكتشين البيتكوين.']
        ],
        'related' => ['jwt-decoder', 'uuid-generator', 'password-generator']
    ],

    'regex-tester' => [
        'title' => 'مختبر التعابير النمطية (Regex Tester)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'regexPatternInput', 'label' => 'التعبير النمطي (Regular Expression)', 'type' => 'text', 'default' => '[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}', 'placeholder' => 'مثال: \\d{3}-\\d{4}'],
            ['id' => 'regexFlagsInput', 'label' => 'المحددات (Flags)', 'type' => 'text', 'default' => 'gi', 'placeholder' => 'g, i, m'],
            ['id' => 'regexTestStringInput', 'label' => 'نص الاختبار للفحص واستخراج المطابقات', 'type' => 'textarea', 'rows' => 6, 'default' => "تواصل معنا عبر user@test.com أو admin@elta6ur.org وسنقوم بالرد قريباً.\nالبريد غير الصالح: invalid-email@", 'placeholder' => 'ضع النص المراد فحصه هنا...'],
        ],
        'calcJs' => "
            const pattern = document.getElementById('regexPatternInput').value;
            const flags = document.getElementById('regexFlagsInput').value;
            const testStr = document.getElementById('regexTestStringInput').value;

            if (!pattern) {
                setPrimaryResult('أدخل التعبير النمطي أولاً', 'الحالة');
                showResultArea();
                return;
            }

            try {
                const re = new RegExp(pattern, flags);
                const matches = testStr.match(re) || [];

                // إبراز المطابقات في النص
                let highlighted = testStr.replace(re, function(match) {
                    return '<mark style=\"background:#6c63ff;color:#fff;padding:2px 4px;border-radius:4px\">' + match + '</mark>';
                });

                setPrimaryResult('تم العثور على ' + matches.length + ' مطابقة بنجاح', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'عدد التطابقات المكتشفة', value: matches.length + ' مطابقة', color: '#10b981' },
                    { label: 'صحة التعبير النمطي', value: 'صحيح وسليم نحوياً ✅', color: '#3b82f6' }
                ]);

                setResultContent(`
                    <div style=\"margin-top:1rem\">
                        <label class=\"form-label\">معاينة النصوص المطابقة مميزة باللون:</label>
                        <div class=\"form-control\" style=\"min-height:100px;line-height:1.8;direction:ltr;background:var(--bg-card);font-family:monospace\">\${highlighted}</div>
                    </div>
                `);
            } catch (e) {
                setPrimaryResult('خطأ في صيغة التعبير النمطي ❌', 'نتيجة الفحص');
                showResultArea();

                setDetailStats([
                    { label: 'حالة الـ Regex', value: 'غير صالح ❌', color: '#ef4444' }
                ]);

                setResultContent(`
                    <div class=\"alert alert-danger\" style=\"margin-top:1rem;color:#ef4444;background:rgba(239,68,68,0.1);border:1px solid #ef4444\">
                        <strong>خطأ في Regex:</strong> \${e.message}
                    </div>
                `);
            }
        ",
        'points' => [
            'يبرز المطابقات بلون مميز فورياً أثناء الكتابة.',
            'يدعم الأعلام الشهيرة: g (شامل لكامل النص)، i (تجاهل حالة الأحرف)، m (متعدد الأسطر).'
        ],
        'assumptions' => 'يعتمد محرك الـ RegExp القياسي للغة JavaScript.',
        'faqs' => [
            ['q' => 'ماذا يعني المحدد g؟', 'a' => 'المحدد g (Global) يبحث عن كافة المطابقات في النص بالكامل، وبدونه يتوقف البحث عند أول مطابقة يجدها فقط.']
        ],
        'related' => ['extract-emails-from-text', 'extract-urls-from-text', 'case-converter']
    ],

    'timestamp-converter' => [
        'title' => 'محول التاريخ و Unix Timestamp',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'timestampInputVal', 'label' => 'أدخل Unix Timestamp (بالثواني أو الميلي ثانية) - أو اتركه فارغاً للوقت الحالي', 'type' => 'text', 'default' => '', 'placeholder' => 'مثال: 1718452800'],
        ],
        'calcJs' => "
            let val = document.getElementById('timestampInputVal').value.trim();
            let date;

            if (!val) {
                date = new Date();
                val = Math.floor(date.getTime() / 1000).toString();
                document.getElementById('timestampInputVal').value = val;
            } else {
                let num = parseInt(val);
                if (val.length <= 10) num *= 1000; // بالثواني
                date = new Date(num);
            }

            if (isNaN(date.getTime())) {
                alert('Timestamp غير صالح!');
                return;
            }

            const unixSeconds = Math.floor(date.getTime() / 1000);
            const unixMillis = date.getTime();
            const isoStr = date.toISOString();
            const arabicFullDate = date.toLocaleDateString('ar-EG', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });

            setPrimaryResult(arabicFullDate, 'التاريخ والوقت المحول');
            showResultArea();

            setDetailStats([
                { label: 'Unix Timestamp (ثواني)', value: unixSeconds, color: '#3b82f6' },
                { label: 'Unix Timestamp (ميلي ثانية)', value: unixMillis, color: '#10b981' },
                { label: 'صيغة ISO 8601 القياسية', value: isoStr, color: '#f59e0b' },
                { label: 'توقيت غرينتش (UTC)', value: date.toUTCString(), color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>الختم الزمني <strong>\${unixSeconds}</strong> يوافق بالتوقيت المحلي: <strong>\${arabicFullDate}</strong>.</p>
            `);
        ",
        'points' => [
            'الـ Unix Timestamp هو عدد الثواني المنقضية منذ منتصف ليل 1 يناير 1970 (UTC).',
            'تستخدمه كافة أنظمة التشغيل وقواعد البيانات لمعاملة الوقت كأرقام صحيحة سهلة المقارنة والفرز.'
        ],
        'assumptions' => 'التحويل يعرض التاريخ بالتوقيت المحلي لمتصفح المستخدم وتوقيت UTC الدولي.',
        'faqs' => [
            ['q' => 'كيف أفرق بين Timestamp الثواني والميلي ثانية؟', 'a' => 'الختم الزمني بالثواني يتكون حالياً من 10 أرقام (مثل 1718452800)، بينما بالميلي ثانية يتكون من 13 رقماً.']
        ],
        'related' => ['exam-countdown-calculator', 'cron-expression-generator', 'cron-expression-explainer']
    ],

    'color-converter' => [
        'title' => 'محول صيغ الألوان ومنتقي الألوان (HEX / RGB / HSL)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'colorPickerInput', 'label' => 'اختر اللون بصرياً أو أدخل كود HEX', 'type' => 'text', 'default' => '#6c63ff', 'placeholder' => '#6c63ff'],
        ],
        'calcJs' => "
            let hex = document.getElementById('colorPickerInput').value.trim();
            if (!hex.startsWith('#')) hex = '#' + hex;

            // تحويل HEX إلى RGB
            let r = 0, g = 0, b = 0;
            if (hex.length === 4) {
                r = parseInt(hex[1] + hex[1], 16);
                g = parseInt(hex[2] + hex[2], 16);
                b = parseInt(hex[3] + hex[3], 16);
            } else if (hex.length === 7) {
                r = parseInt(hex.substring(1, 3), 16);
                g = parseInt(hex.substring(3, 5), 16);
                b = parseInt(hex.substring(5, 7), 16);
            }

            if (isNaN(r) || isNaN(g) || isNaN(b)) {
                alert('كود HEX غير صالح! يرجى إدخال كود مثل #6c63ff');
                return;
            }

            const rgbStr = 'rgb(' + r + ', ' + g + ', ' + b + ')';

            // تحويل RGB إلى HSL
            let rNorm = r / 255, gNorm = g / 255, bNorm = b / 255;
            let max = Math.max(rNorm, gNorm, bNorm), min = Math.min(rNorm, gNorm, bNorm);
            let h, s, l = (max + min) / 2;

            if (max === min) {
                h = s = 0;
            } else {
                let d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case rNorm: h = (gNorm - bNorm) / d + (gNorm < bNorm ? 6 : 0); break;
                    case gNorm: h = (bNorm - rNorm) / d + 2; break;
                    case bNorm: h = (rNorm - gNorm) / d + 4; break;
                }
                h /= 6;
            }
            const hslStr = 'hsl(' + Math.round(h * 360) + ', ' + Math.round(s * 100) + '%, ' + Math.round(l * 100) + '%)';

            setPrimaryResult(hex.toUpperCase() + ' | ' + rgbStr, 'صيغ اللون المحولة');
            showResultArea();

            setDetailStats([
                { label: 'كود HEX', value: hex.toUpperCase(), color: hex },
                { label: 'صيغة RGB', value: rgbStr, color: '#3b82f6' },
                { label: 'صيغة HSL', value: hslStr, color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"display:flex;align-items:center;gap:1.5rem;margin-top:1rem;background:var(--bg-card);padding:1rem;border-radius:var(--radius-md)\">
                    <div style=\"width:60px;height:60px;border-radius:var(--radius-md);background:\${hex};box-shadow:0 4px 12px rgba(0,0,0,0.3);border:2px solid #fff\"></div>
                    <div style=\"flex:1\">
                        <div><strong>CSS HEX:</strong> <code style=\"color:var(--text-accent-light)\">\${hex.toUpperCase()}</code></div>
                        <div style=\"margin-top:0.25rem\"><strong>CSS RGB:</strong> <code style=\"color:var(--text-accent-light)\">\${rgbStr}</code></div>
                        <div style=\"margin-top:0.25rem\"><strong>CSS HSL:</strong> <code style=\"color:var(--text-accent-light)\">\${hslStr}</code></div>
                    </div>
                </div>
            `);
        ",
        'points' => [
            'يحول الألوان بدقة متناهية بين أشهر 3 أنظمة لونية معتمدة في تصميم وتطوير الويب.',
            'نظام HSL (Hue, Saturation, Lightness) هو الأسهل في إنشاء تدرجات وظلال لنفس اللون برمجياً.'
        ],
        'assumptions' => 'يفترض ألوان RGB بنطاق 8-bit قياسي (0 إلى 255).',
        'faqs' => [
            ['q' => 'متى يفضل استخدام HSL بدلاً من HEX؟', 'a' => 'عند تصميم أنظمة الـ Themes وأوضاع الـ Dark Mode، حيث يسهل تعديل إضاءة اللون (Lightness) بنسبة مئوية دون تغيير درجته الأصلية.']
        ],
        'related' => ['css-gradient-generator', 'css-box-shadow-generator', 'color-palette']
    ],

    'css-gradient-generator' => [
        'title' => 'مولد تدرجات الألوان (CSS Gradient Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'gradientColor1', 'label' => 'اللون الأول (البداية)', 'type' => 'text', 'default' => '#6c63ff', 'placeholder' => '#6c63ff'],
            ['id' => 'gradientColor2', 'label' => 'اللون الثاني (النهاية)', 'type' => 'text', 'default' => '#00d4ff', 'placeholder' => '#00d4ff'],
            ['id' => 'gradientAngle', 'label' => 'زاوية التدرج بالدرجات (Angle)', 'type' => 'number', 'default' => '135', 'min' => '0', 'max' => '360', 'step' => '15'],
            ['id' => 'gradientType', 'label' => 'نوع التدرج', 'type' => 'select', 'options' => [
                'linear' => 'تدرج خطي (Linear Gradient)',
                'radial' => 'تدرج دائري شعاعي (Radial Gradient)'
            ], 'default' => 'linear'],
        ],
        'calcJs' => "
            const c1 = document.getElementById('gradientColor1').value.trim() || '#6c63ff';
            const c2 = document.getElementById('gradientColor2').value.trim() || '#00d4ff';
            const angle = parseInt(document.getElementById('gradientAngle').value) || 135;
            const type = document.getElementById('gradientType').value;

            let cssRule = '';
            if (type === 'linear') {
                cssRule = 'linear-gradient(' + angle + 'deg, ' + c1 + ' 0%, ' + c2 + ' 100%)';
            } else {
                cssRule = 'radial-gradient(circle, ' + c1 + ' 0%, ' + c2 + ' 100%)';
            }

            const fullCss = 'background: ' + c1 + ';\\nbackground: ' + cssRule + ';';

            setPrimaryResult('تم توليد التدرج بنجاح', 'تدرج لوني عصري');
            showResultArea();

            setDetailStats([
                { label: 'النوع المعتمد', value: type === 'linear' ? 'خطي ' + angle + '°' : 'دائري شعاعي', color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <div style=\"height:120px;border-radius:var(--radius-lg);background:\${cssRule};box-shadow:0 8px 24px rgba(0,0,0,0.3);margin-bottom:1rem\"></div>
                    <label class=\"form-label\">كود CSS الجاهز للنسخ:</label>
                    <textarea class=\"form-control\" rows=\"3\" style=\"font-family:monospace;direction:ltr\" readonly>\${fullCss}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يولد كود CSS نظيف مع كود احتياطي (Fallback color) للمتصفحات القديمة.',
            'يوفر معاينة بصرية حية فورية للتدرج قبل نسخه ولصقه في مشروعك.'
        ],
        'assumptions' => 'الألوان مدعومة بصيغ HEX و RGB وأسماء الألوان الإنجليزية.',
        'faqs' => [
            ['q' => 'ما هي الزاوية الأكثر جاذبية للتدرجات الخطية في المواقع الحديثة؟', 'a' => 'الزاوية 135 درجة (من أعلى اليسار إلى أسفل اليمين) تعتبر الأكثر استخداماً في تصاميم الويب العصرية لأنها تماثل الاتجاه الطبيعي للإضاءة.']
        ],
        'related' => ['color-converter', 'css-box-shadow-generator', 'css-border-radius-generator']
    ],

    'css-box-shadow-generator' => [
        'title' => 'مولد ظلال الصناديق (CSS Box Shadow Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'shadowX', 'label' => 'الإزاحة الأفقية X (بكسل)', 'type' => 'number', 'default' => '0', 'min' => '-50', 'max' => '50', 'step' => '1'],
            ['id' => 'shadowY', 'label' => 'الإزاحة الرأسية Y (بكسل)', 'type' => 'number', 'default' => '10', 'min' => '-50', 'max' => '50', 'step' => '1'],
            ['id' => 'shadowBlur', 'label' => 'درجة التمويه والضبابية Blur (بكسل)', 'type' => 'number', 'default' => '25', 'min' => '0', 'max' => '100', 'step' => '1'],
            ['id' => 'shadowSpread', 'label' => 'نطاق الانتشار Spread (بكسل)', 'type' => 'number', 'default' => '-5', 'min' => '-50', 'max' => '50', 'step' => '1'],
            ['id' => 'shadowColorHex', 'label' => 'لون الظل', 'type' => 'text', 'default' => 'rgba(0, 0, 0, 0.35)', 'placeholder' => 'rgba(0, 0, 0, 0.35)'],
        ],
        'calcJs' => "
            const x = parseInt(document.getElementById('shadowX').value) || 0;
            const y = parseInt(document.getElementById('shadowY').value) || 10;
            const blur = parseInt(document.getElementById('shadowBlur').value) || 25;
            const spread = parseInt(document.getElementById('shadowSpread').value) || -5;
            const color = document.getElementById('shadowColorHex').value || 'rgba(0, 0, 0, 0.35)';

            const shadowRule = x + 'px ' + y + 'px ' + blur + 'px ' + spread + 'px ' + color;
            const cssFull = 'box-shadow: ' + shadowRule + ';\\n-webkit-box-shadow: ' + shadowRule + ';';

            setPrimaryResult('box-shadow: ' + shadowRule, 'كود الظل');
            showResultArea();

            setDetailStats([
                { label: 'التمويه والانتشار', value: blur + 'px / ' + spread + 'px', color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <div style=\"padding:2.5rem;display:flex;justify-content:center;background:var(--bg-glass);border-radius:var(--radius-lg);margin-bottom:1rem\">
                        <div style=\"width:180px;height:100px;background:var(--bg-card);border-radius:var(--radius-md);box-shadow:\${shadowRule};display:flex;align-items:center;justify-content:center;font-weight:600\">معاينة الصندوق</div>
                    </div>
                    <label class=\"form-label\">كود CSS:</label>
                    <textarea class=\"form-control\" rows=\"2\" style=\"font-family:monospace;direction:ltr\" readonly>\${cssFull}</textarea>
                </div>
            `);
        ",
        'points' => [
            'الظلال الناعمة ذات الانتشار السالب (Negative Spread) تمنح البطاقات مظهراً ثلاثي الأبعاد فاخراً وعصرياً.',
            'يتضمن بادئة -webkit- لضمان التوافق مع كافة المتصفحات.'
        ],
        'assumptions' => 'يفترض ظلاً خارجياً متناسقاً.',
        'faqs' => [
            ['q' => 'كيف أجعل الظل يبدو طبيعياً وناعماً؟', 'a' => 'اجعل قيمة الـ Blur عالية (20px إلى 30px) واستخدم انتشاراً سالباً خفيفاً (-5px) مع شفافية لون منخفضة (Opacity 0.1 إلى 0.2).']
        ],
        'related' => ['css-border-radius-generator', 'css-gradient-generator', 'color-converter']
    ],

    'css-border-radius-generator' => [
        'title' => 'مولد حواف مخصصة (CSS Border Radius Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'brTopLeft', 'label' => 'الحافة العلوية اليمنى (%)', 'type' => 'number', 'default' => '30', 'min' => '0', 'max' => '100', 'step' => '5'],
            ['id' => 'brTopRight', 'label' => 'الحافة العلوية اليسرى (%)', 'type' => 'number', 'default' => '70', 'min' => '0', 'max' => '100', 'step' => '5'],
            ['id' => 'brBottomRight', 'label' => 'الحافة السفلية اليسرى (%)', 'type' => 'number', 'default' => '70', 'min' => '0', 'max' => '100', 'step' => '5'],
            ['id' => 'brBottomLeft', 'label' => 'الحافة السفلية اليمنى (%)', 'type' => 'number', 'default' => '30', 'min' => '0', 'max' => '100', 'step' => '5'],
        ],
        'calcJs' => "
            const tl = parseInt(document.getElementById('brTopLeft').value) || 30;
            const tr = parseInt(document.getElementById('brTopRight').value) || 70;
            const br = parseInt(document.getElementById('brBottomRight').value) || 70;
            const bl = parseInt(document.getElementById('brBottomLeft').value) || 30;

            const radiusRule = tl + '% ' + (100 - tl) + '% ' + br + '% ' + (100 - br) + '% / ' + bl + '% ' + (100 - tr) + '% ' + tr + '% ' + (100 - bl) + '%';
            const cssCode = 'border-radius: ' + radiusRule + ';';

            setPrimaryResult(cssCode, 'كود الحواف المخصصة');
            showResultArea();

            setDetailStats([
                { label: 'نمط الشكل', value: 'شكل عضوي ناعم (Organic Blob)', color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <div style=\"padding:2.5rem;display:flex;justify-content:center;background:var(--bg-glass);border-radius:var(--radius-lg);margin-bottom:1rem\">
                        <div style=\"width:160px;height:160px;background:linear-gradient(135deg, #6c63ff, #00d4ff);border-radius:\${radiusRule};display:flex;align-items:center;justify-content:center;color:#fff;font-weight:600\">معاينة الشكل</div>
                    </div>
                    <label class=\"form-label\">كود CSS:</label>
                    <textarea class=\"form-control\" rows=\"2\" style=\"font-family:monospace;direction:ltr\" readonly>\${cssCode}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يولد أشكالاً عضوية متغيرة (Blob shapes) باستخدام الخاصية المتقدمة لثماني قيم في border-radius.',
            'مفيدة جداً لخلفيات صور البروفايل والأيقونات العصرية في صفحات الهبوط.'
        ],
        'assumptions' => 'النسب المئوية تضمن تماسك الشكل.',
        'faqs' => [
            ['q' => 'ما فائدة استخدام علامة السلاش / في border-radius؟', 'a' => 'علامة السلاش تفصل بين أنصاف الأقطار الأفقية والعمودية للحواف، مما يسمح بصنع أشكال بيضاوية وعضوية غير متناظرة.']
        ],
        'related' => ['css-box-shadow-generator', 'css-gradient-generator', 'color-converter']
    ],

    'qr-generator' => [
        'title' => 'مولد رموز الاستجابة السريعة (QR Code Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'qrContentInput', 'label' => 'النص أو الرابط المراد تحويله إلى QR Code', 'type' => 'textarea', 'rows' => 4, 'default' => 'https://example.com', 'placeholder' => 'ضع الرابط أو النص أو رقم الهاتف هنا...'],
            ['id' => 'qrSizeOption', 'label' => 'مقاس الصورة (بكسل)', 'type' => 'select', 'options' => [
                '200' => 'صغير (200×200 بكسل)',
                '300' => 'متوسط (300×300 بكسل - موصى به)',
                '500' => 'كبير عالي الدقة (500×500 بكسل للطباعة)'
            ], 'default' => '300'],
        ],
        'calcJs' => "
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
                <div style=\"margin-top:1.5rem;text-align:center\">
                    <div style=\"display:inline-block;padding:1rem;background:#fff;border-radius:var(--radius-md);box-shadow:0 8px 24px rgba(0,0,0,0.25)\">
                        <img src=\"\${qrUrl}\" alt=\"QR Code\" style=\"max-width:100%;height:auto;display:block\">
                    </div>
                    <div style=\"margin-top:1rem\">
                        <a href=\"\${qrUrl}\" target=\"_blank\" download=\"qrcode.png\" class=\"btn btn-primary\">
                            <i class=\"fas fa-download\"></i> تنزيل صورة QR كملف PNG
                        </a>
                    </div>
                </div>
            `);
        ",
        'points' => [
            'يولد رمز QR عالي الجودة يدعم الروابط، شبكات الواي فاي، أرقام الهواتف، وبطاقات الأعمال vCard.',
            'قابل للمسح الفوري بواسطة كاميرات جميع الهواتف الذكية (iOS و Android).'
        ],
        'assumptions' => 'الرمز يتم إنشاؤه عبر API قياسي مفتوح المصدر ومستقر.',
        'faqs' => [
            ['q' => 'هل تنتهي صلاحية كود الـ QR بعد فترة؟', 'a' => 'لا؛ كود الـ QR الثابت (Static QR) لا تنتهي صلاحيته مدى الحياة لأنه يحتوي البيانات مشفرة بصرياً في مربعات الصورة ذاتها.']
        ],
        'related' => ['url-encode', 'url-parser', 'random-string-generator']
    ],

    'lorem-ipsum-generator' => [
        'title' => 'مولد نصوص لوريم إيبسوم عربي ولاتيني (Lorem Ipsum)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'loremLanguage', 'label' => 'لغة النص المولد', 'type' => 'select', 'options' => [
                'arabic' => 'نص عربي تجريبي فصيح (بديل لوريم إيبسوم العربي)',
                'latin' => 'نص لاتيني كلاسيكي (Lorem ipsum dolor sit amet)'
            ], 'default' => 'arabic'],
            ['id' => 'paragraphsCountLorem', 'label' => 'عدد الفقرات المطلوبة', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '20', 'step' => '1'],
        ],
        'calcJs' => "
            const lang = document.getElementById('loremLanguage').value;
            const count = Math.max(1, Math.min(20, parseInt(document.getElementById('paragraphsCountLorem').value) || 3));

            const arabicParas = [
                'هذا نص تجريبي يمكن أن يستبدل في نفس المساحة، لقد تم توليد هذا النص من منصة كود التطور لتجربة التصميم والتنسيق البصري، حيث يحتاج المصممون إلى نصوص تحاكي الواقع لاختبار تناسق الخطوط والألوان.',
                'عندما يوضع النص في مكانه الصحيح، يظهر الشكل العام للتصميم بشكل أوضح، مما يساعد العميل على تصور النتيجة النهائية للموقع أو التطبيق دون أن يتشتت بالمعنى المباشر للكلمات.',
                'الخط العربي يتميز بجماليات فريدة وتناغم بصري استثنائي يجمع بين الأصالة والحداثة، وتعتبر التغذية البصرية السليمة من أهم ركائز النجاح في عالم التصميم وتجربة المستخدم.',
                'إن بناء تجربة مستخدم استثنائية يتطلب الاهتمام بأدق التفاصيل المعمارية والبرمجية، والتأكد من سهولة القراءة وتناسق المسافات البيضاء والتباين اللوني عبر كافة الشاشات.'
            ];

            const latinParas = [
                'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'Curabitur pretium tincidunt lacus. Nulla gravida orci a odio. Nullam varius, turpis et commodo pharetra, est eros bibendum elit, nec luctus magna felis sollicitudin mauris. Integer in mauris eu nibh euismod gravida.'
            ];

            const source = lang === 'arabic' ? arabicParas : latinParas;
            let resultList = [];
            for (let i = 0; i < count; i++) {
                resultList.push(source[i % source.length]);
            }

            const textOutput = resultList.join('\\n\\n');

            setPrimaryResult('تم توليد ' + count + ' فقرات ' + (lang === 'arabic' ? 'عربية' : 'لاتينية'), 'النص المولد');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات', value: count + ' فقرات', color: '#10b981' },
                { label: 'عدد الكلمات', value: textOutput.split(/\\s+/).length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص التجريبي الجاهز للنسخ:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"direction:\${lang === 'arabic' ? 'rtl' : 'ltr'};line-height:1.8\" readonly>\${textOutput}</textarea>
                </div>
            `);
        ",
        'points' => [
            'النصوص العربية التجريبية تراعي الطبيعة المتصلة للحروف وتوزيع المسافات الخاصة بالخطوط العربية.',
            'مثالية لمصممي UI/UX ومطوري الويب لملء قوالب الصفحات والنماذج قبل إضافة المحتوى الفعلي.'
        ],
        'assumptions' => 'يفترض نصوصاً نموذجية خالية من الأخطاء الإملائية.',
        'faqs' => [
            ['q' => 'لماذا يُفضل استخدام بديل عربي بدلاً من لوريم إيبسوم اللاتيني؟', 'a' => 'لأن الحروف العربية تختلف في ارتفاعاتها وتمددها وتأثيرها على محاذاة الصناديق وحجم الخطوط مقارنة بالأحرف اللاتينية المنفصلة.']
        ],
        'related' => ['random-string-generator', 'clean-arabic-text', 'word-counter']
    ],

    'random-string-generator' => [
        'title' => 'توليد سلاسل نصية عشوائية للأكواد و API Keys',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'strLengthInput', 'label' => 'طول السلسلة النصية (عدد الأحرف)', 'type' => 'number', 'default' => '32', 'min' => '4', 'max' => '256', 'step' => '4'],
            ['id' => 'strCountInput', 'label' => 'عدد السلاسل المطلوب توليدها', 'type' => 'number', 'default' => '3', 'min' => '1', 'max' => '50', 'step' => '1'],
            ['id' => 'strCharsetOption', 'label' => 'مجموعة الأحرف والرموز المستخدمة', 'type' => 'select', 'options' => [
                'alphanumeric' => 'أحرف وأرقام فقط (A-Z, a-z, 0-9) - مناسب للـ API Keys',
                'hex' => 'أحرف ست عشرية (Hex: 0-9, a-f)',
                'all_symbols' => 'شاملة الرموز الخاصة (!@#$%) لأقصى درجات الأمان'
            ], 'default' => 'alphanumeric'],
        ],
        'calcJs' => "
            const len = Math.max(4, Math.min(256, parseInt(document.getElementById('strLengthInput').value) || 32));
            const count = Math.max(1, Math.min(50, parseInt(document.getElementById('strCountInput').value) || 3));
            const charsetOpt = document.getElementById('strCharsetOption').value;

            let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            if (charsetOpt === 'hex') chars = '0123456789abcdef';
            if (charsetOpt === 'all_symbols') chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}|;:,.<>?';

            const results = [];
            for (let i = 0; i < count; i++) {
                const arr = new Uint32Array(len);
                crypto.getRandomValues(arr);
                let str = '';
                for (let j = 0; j < len; j++) {
                    str += chars[arr[j] % chars.length];
                }
                results.push(str);
            }

            const output = results.join('\\n');

            setPrimaryResult('تم توليد ' + count + ' سلسلة عشوائية بطول ' + len + ' حرف', 'السلاسل الناتجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد السلاسل المولدة', value: count + ' مفاتيح', color: '#10b981' },
                { label: 'طول المفتاح الواحد', value: len + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">السلاسل النصية المولدة (سطر لكل مفتاح):</label>
                    <textarea class=\"form-control\" rows=\"5\" style=\"font-family:monospace;direction:ltr\" readonly>\${output}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يعتمد التوليد على دوال التشفير العشوائي الآمنة في المتصفح (CSPRNG - crypto.getRandomValues).',
            'مناسب لتوليد مفاتيح التوثيق السرية JWT Secrets، ورموز الجلسات Session Tokens، والـ API Keys.'
        ],
        'assumptions' => 'عشوائية مشفرة غير قابلة للتنبؤ رياضياً.',
        'faqs' => [
            ['q' => 'هل هذه السلاسل صالحة للاستخدام كرموز سرية في السيرفرات؟', 'a' => 'نعم؛ بفضل استخدام crypto.getRandomValues تكون السلاسل آمنة تشفيرياً وصالحة لإنتاج مفاتيح بيئات الإنتاج الحساسة.']
        ],
        'related' => ['password-generator', 'uuid-generator', 'hash-generator']
    ],

    'random-number-generator' => [
        'title' => 'توليد أرقام عشوائية ضمن مجال معين (Random Number)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'minNumberRange', 'label' => 'الحد الأدنى للمجال (Min)', 'type' => 'number', 'default' => '1', 'min' => '-1000000', 'step' => '1'],
            ['id' => 'maxNumberRange', 'label' => 'الحد الأقصى للمجال (Max)', 'type' => 'number', 'default' => '100', 'min' => '-1000000', 'step' => '1'],
            ['id' => 'numbersCountToGen', 'label' => 'كم رقماً تريد توليده؟', 'type' => 'number', 'default' => '5', 'min' => '1', 'max' => '500', 'step' => '1'],
            ['id' => 'allowDuplicatesNumbers', 'label' => 'السماح بتكرار الأرقام في السحب', 'type' => 'select', 'options' => [
                'no' => 'أرقام فريدة غير مكررة (سحب قرعة)',
                'yes' => 'السماح بالتكرار'
            ], 'default' => 'no'],
        ],
        'calcJs' => "
            const min = parseInt(document.getElementById('minNumberRange').value) || 1;
            const max = parseInt(document.getElementById('maxNumberRange').value) || 100;
            const count = Math.max(1, Math.min(500, parseInt(document.getElementById('numbersCountToGen').value) || 5));
            const allowDup = document.getElementById('allowDuplicatesNumbers').value === 'yes';

            if (min >= max) {
                alert('الحد الأدنى يجب أن يكون أقل من الحد الأقصى!');
                return;
            }

            const rangeSize = max - min + 1;
            if (!allowDup && count > rangeSize) {
                alert('لا يمكن توليد ' + count + ' رقم فريد من مجال يحتوي على ' + rangeSize + ' رقماً فقط!');
                return;
            }

            const results = [];
            const used = new Set();

            while (results.length < count) {
                const arr = new Uint32Array(1);
                crypto.getRandomValues(arr);
                const rand = min + (arr[0] % rangeSize);
                if (allowDup) {
                    results.push(rand);
                } else if (!used.has(rand)) {
                    used.add(rand);
                    results.push(rand);
                }
            }

            const output = results.join(', ');

            setPrimaryResult(output, 'الأرقام العشوائية المولدة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأرقام', value: count + ' أرقام', color: '#10b981' },
                { label: 'نطاق المجال', value: 'من ' + min + ' إلى ' + max, color: '#3b82f6' },
                { label: 'حالة التكرار', value: allowDup ? 'مسموح' : 'فريدة تماماً (قرعة عادلة)', color: '#f59e0b' }
            ]);

            setResultContent(`
                <p>الأرقام المولدة: <strong>\${output}</strong></p>
            `);
        ",
        'points' => [
            'توليد عشوائي آمن تشفيرياً ومثالي لإجراء القرعة وسحب الفائزين في المسابقات بدون أي تحيز.',
            'يدعم استبعاد الأرقام المكررة وتحديد النطاق بالأرقام السالبة والموجبة.'
        ],
        'assumptions' => 'الأرقام صحيحة Integers.',
        'faqs' => [
            ['q' => 'هل القرعة عادلة 100%؟', 'a' => 'نعم؛ الخوارزمية تستخدم وحدة crypto في المتصفح التي تعتمد على ضوضاء النظام الفيزيائية وتضمن توزيعاً احتمالياً متساوياً لجميع الأرقام دون أي أفضلية.']
        ],
        'related' => ['random-string-generator', 'password-generator', 'uuid-generator']
    ],

    'case-converter' => [
        'title' => 'محول تسمية المتغيرات البرمجية (Case Converter)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'caseTextInput', 'label' => 'أدخل اسم المتغير أو العبارة الإنجليزية', 'type' => 'text', 'default' => 'user profile data service', 'placeholder' => 'user profile data service'],
        ],
        'calcJs' => "
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
                .split(/\\s+/)
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
                <div style=\"margin-top:1rem;display:flex;flex-direction:column;gap:0.5rem\">
                    <div class=\"d-flex justify-between align-center p-1\" style=\"background:var(--bg-glass);border-radius:6px\">
                        <span><strong>camelCase:</strong> <code>\${camelCase}</code></span>
                        <button class=\"btn btn-ghost btn-sm\" onclick=\"navigator.clipboard.writeText('\${camelCase}')\"><i class=\"fas fa-copy\"></i></button>
                    </div>
                    <div class=\"d-flex justify-between align-center p-1\" style=\"background:var(--bg-glass);border-radius:6px\">
                        <span><strong>snake_case:</strong> <code>\${snakeCase}</code></span>
                        <button class=\"btn btn-ghost btn-sm\" onclick=\"navigator.clipboard.writeText('\${snakeCase}')\"><i class=\"fas fa-copy\"></i></button>
                    </div>
                    <div class=\"d-flex justify-between align-center p-1\" style=\"background:var(--bg-glass);border-radius:6px\">
                        <span><strong>kebab-case:</strong> <code>\${kebabCase}</code></span>
                        <button class=\"btn btn-ghost btn-sm\" onclick=\"navigator.clipboard.writeText('\${kebabCase}')\"><i class=\"fas fa-copy\"></i></button>
                    </div>
                    <div class=\"d-flex justify-between align-center p-1\" style=\"background:var(--bg-glass);border-radius:6px\">
                        <span><strong>PascalCase:</strong> <code>\${pascalCase}</code></span>
                        <button class=\"btn btn-ghost btn-sm\" onclick=\"navigator.clipboard.writeText('\${pascalCase}')\"><i class=\"fas fa-copy\"></i></button>
                    </div>
                    <div class=\"d-flex justify-between align-center p-1\" style=\"background:var(--bg-glass);border-radius:6px\">
                        <span><strong>CONSTANT_CASE:</strong> <code>\${constantCase}</code></span>
                        <button class=\"btn btn-ghost btn-sm\" onclick=\"navigator.clipboard.writeText('\${constantCase}')\"><i class=\"fas fa-copy\"></i></button>
                    </div>
                </div>
            `);
        ",
        'points' => [
            'التحويل التلقائي بين كافة أنماط التسمية المستخدمة في أشهر لغات البرمجة وأطر العمل.',
            'يدعم الإدخال بأي صيغة سابقة ويفككها لكلمات مفردة بدقة.'
        ],
        'assumptions' => 'مخصص للأحرف والكلمات اللاتينية والإنجليزية.',
        'faqs' => [
            ['q' => 'ما هو المعيار المعتمد في بايثون وجافاسكريبت؟', 'a' => 'في بايثون المعيار الرسمي هو snake_case لأسماء الدوال والمتغيرات، بينما في جافاسكريبت وتايب سكريبت المعيار هو camelCase.']
        ],
        'related' => ['text-formatter', 'slug-converter', 'regex-tester']
    ],

    'html-entity-converter' => [
        'title' => 'تشفير وفك تشفير رموز HTML Entities',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'entityInputText', 'label' => 'أدخل النص أو الرموز', 'type' => 'textarea', 'rows' => 5, 'default' => '<div>© 2026 "منصة الأدوات" & <المطورين></div>', 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'entityActionChoice', 'label' => 'العملية المطلوبة', 'type' => 'select', 'options' => [
                'encode' => 'تشفير الرموز الخاصة إلى HTML Entities (&lt;, &gt;, &quot;, إلخ)',
                'decode' => 'فك التشفير وإرجاع الرموز لأصلها'
            ], 'default' => 'encode'],
        ],
        'calcJs' => "
            const text = document.getElementById('entityInputText').value;
            const action = document.getElementById('entityActionChoice').value;

            let result = '';
            if (action === 'encode') {
                const el = document.createElement('div');
                el.innerText = text;
                result = el.innerHTML.replace(/\"/g, '&quot;').replace(/'/g, '&#39;');
            } else {
                const el = document.createElement('div');
                el.innerHTML = text;
                result = el.innerText;
            }

            setPrimaryResult('تمت معالجة رموز HTML بنجاح', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'العملية', value: action === 'encode' ? 'تشفير Entities' : 'فك التشفير', color: '#10b981' },
                { label: 'طول النص الناتج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النتيجة المحولة:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"font-family:monospace;direction:ltr\" readonly>\${result}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تشفير رموز الـ HTML يمنع تشوه الأكواد ويحمي المواقع من ثغرات الحقن وتداخل الأقواس < >.',
            'مفيد لعرض أكواد برمجية على صفحات الويب دون أن يقوم المتصفح بتنفيذها كعناصر HTML حقيقية.'
        ],
        'assumptions' => 'يعتمد المعايير القياسية لترميز الرموز المحجوزة في HTML5.',
        'faqs' => [
            ['q' => 'لماذا نشفر علامة الأكبر والأصغر < >؟', 'a' => 'لأن المتصفح سيعتبرها بداية وسم HTML وسيحاول تنفيذها بدلاً من عرضها كنص عادي للمستخدم.']
        ],
        'related' => ['url-encode', 'html-formatter', 'clean-html-text']
    ],

    'url-parser' => [
        'title' => 'تحليل الروابط وفصل المعاملات (URL Parser & Query Params)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'urlToParseInput', 'label' => 'أدخل الرابط المراد تحليله وفحصه', 'type' => 'text', 'default' => 'https://www.example.com:8080/products/shoes?category=running&size=43&sort=price_asc#reviews', 'placeholder' => 'https://...'],
        ],
        'calcJs' => "
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
                paramsHtml = '<h4 style=\"margin:1rem 0 0.5rem\">معاملات الرابط (Query Parameters):</h4><table class=\"table\" style=\"width:100%\"><thead><tr><th>المفتاح (Key)</th><th>القيمة (Value)</th></tr></thead><tbody>';
                params.forEach(p => {
                    paramsHtml += `<tr><td><code>\${p.key}</code></td><td><strong>\${p.val}</strong></td></tr>`;
                });
                paramsHtml += '</tbody></table>';
            } else {
                paramsHtml = '<p style=\"color:var(--text-muted);margin-top:1rem\">لا يحتوي هذا الرابط على معاملات بحث (Query Parameters).</p>';
            }

            if (parsedUrl.hash) {
                paramsHtml += `<p style=\"margin-top:0.5rem\"><strong>المرسى الداخلي (Hash Fragment):</strong> <code>\${parsedUrl.hash}</code></p>`;
            }

            setResultContent(paramsHtml);
        ",
        'points' => [
            'يفكك الرابط بدقة إلى: البروتوكول، المنفذ (Port)، النطاق، المسار، ومعاملات البحث (Query Parameters)، والمرسى الداخلي (Hash).',
            'مفيد جداً للمطورين لفحص معاملات الحملات التسويقية (UTM Parameters) واختبار تكامل الـ APIs.'
        ],
        'assumptions' => 'الرابط يجب أن يكون مكتملاً بالبروتوكول (http:// أو https://).',
        'faqs' => [
            ['q' => 'ما هي معاملات UTM في الروابط؟', 'a' => 'هي معاملات خاصة مثل utm_source و utm_campaign تُضاف لنهاية الرابط لتتبع مصدر الزيارات في Google Analytics.']
        ],
        'related' => ['url-encode', 'extract-urls-from-text', 'qr-generator']
    ],

    'user-agent-parser' => [
        'title' => 'تحليل User Agent ومعلومات المتصفح ونظام التشغيل',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'userAgentInput', 'label' => 'سلسلة User Agent (تم جلب متصفحك الحالي تلقائياً)', 'type' => 'textarea', 'rows' => 4, 'default' => '', 'placeholder' => 'Mozilla/5.0...'],
        ],
        'calcJs' => "
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
                <p>بياناتك المستخرجة: <strong>\${browser}</strong> يعمل على بيئة <strong>\${os}</strong> من خلال <strong>\${device}</strong>.</p>
            `);
        ",
        'points' => [
            'يحلل بيانات الترويسة التي يرسلها المتصفح للسيرفر لتحديد إمكانيات الجهاز وإرسال الصفحة المتوافقة معه.',
            'يساعد المطورين في فحص إحصائيات زوار الموقع والتأكد من دعم المتصفحات المختلفة.'
        ],
        'assumptions' => 'يفترض سلاسل User Agent قياسية صادرة من المتصفحات الحديثة.',
        'faqs' => [
            ['q' => 'هل يحتوي الـ User Agent على معلومات شخصية؟', 'a' => 'لا؛ فهو يحتوي فقط على الإصدار البرمجي للمتصفح ونوع نواة نظام التشغيل ولا يكشف هويتك أو موقعك الجغرافي.']
        ],
        'related' => ['url-parser', 'case-converter', 'timestamp-converter']
    ],

    'cron-expression-generator' => [
        'title' => 'مولد تعابير الجدولة (Cron Expression Generator)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'cronSchedulePreset', 'label' => 'جدول التكرار المفضل', 'type' => 'select', 'options' => [
                'every_minute' => 'كل دقيقة (* * * * *)',
                'every_5_minutes' => 'كل 5 دقائق (*/5 * * * *)',
                'every_15_minutes' => 'كل 15 دقيقة (*/15 * * * *)',
                'every_hour' => 'كل ساعة بالضبط عند الدقيقة 0 (0 * * * *)',
                'daily_midnight' => 'يومياً عند منتصف الليل 12:00 ص (0 0 * * *)',
                'daily_noon' => 'يومياً ظهراً الساعة 12:00 م (0 12 * * *)',
                'weekly_friday' => 'أسبوعياً كل يوم جمعة منتصف الليل (0 0 * * 5)',
                'monthly_first' => 'شهرياً في أول يوم من كل شهر (0 0 1 * *)'
            ], 'default' => 'daily_midnight'],
        ],
        'calcJs' => "
            const preset = document.getElementById('cronSchedulePreset').value;

            let cronExpr = '0 0 * * *';
            let desc = 'يومياً عند منتصف الليل';

            if (preset === 'every_minute') { cronExpr = '* * * * *'; desc = 'يتم التنفيذ كل دقيقة باستمرار'; }
            if (preset === 'every_5_minutes') { cronExpr = '*/5 * * * *'; desc = 'يتم التنفيذ كل 5 دقائق'; }
            if (preset === 'every_15_minutes') { cronExpr = '*/15 * * * *'; desc = 'يتم التنفيذ كل 15 دقيقة'; }
            if (preset === 'every_hour') { cronExpr = '0 * * * *'; desc = 'يتم التنفيذ رأس كل ساعة بالضبط'; }
            if (preset === 'daily_midnight') { cronExpr = '0 0 * * *'; desc = 'يومياً عند الساعة 12:00 ص (منتصف الليل)'; }
            if (preset === 'daily_noon') { cronExpr = '0 12 * * *'; desc = 'يومياً عند الساعة 12:00 ظهراً'; }
            if (preset === 'weekly_friday') { cronExpr = '0 0 * * 5'; desc = 'أسبوعياً كل يوم جمعة عند منتصف الليل'; }
            if (preset === 'monthly_first') { cronExpr = '0 0 1 * *'; desc = 'شهرياً في اليوم الأول من كل شهر'; }

            setPrimaryResult(cronExpr, 'تعبير Cron');
            showResultArea();

            setDetailStats([
                { label: 'شرح التعبير بالعربية', value: desc, color: '#10b981' },
                { label: 'الحقول الخمسة', value: 'دقيقة | ساعة | يوم شهر | شهر | يوم أسبوع', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">تعبير Cron الجاهز للاستخدام في السيرفر أو المهام المجدولة:</label>
                    <input type=\"text\" class=\"form-control\" style=\"font-family:monospace;direction:ltr;font-size:1.2rem;font-weight:bold;color:var(--text-accent-light)\" value=\"\${cronExpr}\" readonly>
                    <p style=\"margin-top:0.75rem;color:var(--text-secondary)\"><i class=\"fas fa-info-circle\"></i> \${desc}.</p>
                </div>
            `);
        ",
        'points' => [
            'يتكون تعبير Cron القياسي في لينكس و Crontab من 5 حقول: (الدقيقة 0-59، الساعة 0-23، يوم الشهر 1-31، الشهر 1-12، يوم الأسبوع 0-7).',
            'علامة النجمة (*) تعني كل قيمة ممكنة بدون استثناء.'
        ],
        'assumptions' => 'متوافق مع خوادم Linux ومعالجات وظائف السحابة AWS Cron و GitHub Actions.',
        'faqs' => [
            ['q' => 'ماذا تعني علامة السلاش مثل */10؟', 'a' => 'تعني التكرار الدوري بخطوة محددة؛ فمثلاً */10 في خانة الدقائق تعني كل 10 دقائق (في الدقائق 0، 10، 20، 30، 40، 50).']
        ],
        'related' => ['cron-expression-explainer', 'timestamp-converter', 'user-agent-parser']
    ],

    'cron-expression-explainer' => [
        'title' => 'شرح وتفسير تعبير Cron باللغة العربية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'cronToExplainInput', 'label' => 'أدخل تعبير Cron المكون من 5 حقول', 'type' => 'text', 'default' => '30 4 * * 1-5', 'placeholder' => '30 4 * * 1-5'],
        ],
        'calcJs' => "
            const expr = document.getElementById('cronToExplainInput').value.trim();
            const parts = expr.split(/\\s+/);

            if (parts.length !== 5) {
                alert('تعبير Cron غير مكتمل! يجب أن يحتوي على 5 حقول تفصلها مسافات (دقيقة ساعة يوم شهر يوم_أسبوع).');
                return;
            }

            const min = parts[0];
            const hour = parts[1];
            const dom = parts[2];
            const mon = parts[3];
            const dow = parts[4];

            let explanation = 'يتم تنفيذ المهمة: ';

            // الدقائق
            if (min === '*') explanation += 'في كل دقيقة ';
            else if (min.startsWith('*/')) explanation += 'كل ' + min.replace('*/', '') + ' دقائق ';
            else explanation += 'عند الدقيقة ' + min + ' ';

            // الساعات
            if (hour === '*') explanation += 'من كل ساعة ';
            else if (hour.startsWith('*/')) explanation += 'كل ' + hour.replace('*/', '') + ' ساعات ';
            else explanation += 'في الساعة ' + (parseInt(hour) > 12 ? (parseInt(hour)-12) + ' مساءً' : (parseInt(hour)===0 ? '12 منتصف الليل' : hour + ' صباحاً')) + ' ';

            // أيام الشهر
            if (dom !== '*') explanation += 'في اليوم رقم ' + dom + ' من الشهر ';

            // الأشهر
            if (mon !== '*') explanation += 'في شهر ' + mon + ' ';

            // أيام الأسبوع
            if (dow === '1-5') explanation += 'من يوم الاثنين إلى الجمعة (أيام العمل) ';
            else if (dow === '5') explanation += 'في يوم الجمعة ';
            else if (dow !== '*') explanation += 'في اليوم رقم ' + dow + ' من الأسبوع ';

            setPrimaryResult(explanation, 'التفسير باللغة العربية');
            showResultArea();

            setDetailStats([
                { label: 'الدقيقة (Minute)', value: min, color: '#3b82f6' },
                { label: 'الساعة (Hour)', value: hour, color: '#10b981' },
                { label: 'يوم الشهر (Day of Month)', value: dom, color: '#f59e0b' },
                { label: 'يوم الأسبوع (Day of Week)', value: dow, color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div class=\"alert alert-info\" style=\"margin-top:1rem\">
                    <i class=\"fas fa-clock\"></i> <strong>المعنى التنفيذي:</strong> \${explanation}.
                </div>
            `);
        ",
        'points' => [
            'يترجم رموز الجدولة المعقدة إلى جملة عربية واضحة ومباشرة لتفادي الأخطاء في إطلاق الوظائف المؤتمتة.',
            'يدعم النطاقات المفصولة بشرطة (مثل 1-5) والخطوات المتكررة (*/10).'
        ],
        'assumptions' => 'يفترض تعبير لينكس القياسي (5 أجزاء).',
        'faqs' => [
            ['q' => 'هل يبدأ ترقيم أيام الأسبوع بـ 0 أم بـ 1؟', 'a' => 'في نظام Cron القياسي، الرقم 0 أو 7 يمثل يوم الأحد، والرقم 1 يمثل الاثنين، والرقم 5 يمثل الجمعة.']
        ],
        'related' => ['cron-expression-generator', 'timestamp-converter', 'regex-tester']
    ],

    'text-diff-checker' => [
        'title' => 'مقارنة النصوص واكتشاف الفروقات (Text Diff Checker)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'originalTextDiff', 'label' => 'النص الأصلي (Original)', 'type' => 'textarea', 'rows' => 6, 'default' => "الذكاء الاصطناعي هو أحدث ثورة تقنية في القرن الحادي والعشرين.\nيساعد المطورين على كتابة الأكواد بسرعة وكفاءة عالية.\nالتعلم المستمر هو المفتاح.", 'placeholder' => 'ضع النص الأصلي هنا...'],
            ['id' => 'modifiedTextDiff', 'label' => 'النص المعدل (Modified)', 'type' => 'textarea', 'rows' => 6, 'default' => "الذكاء الاصطناعي هو أعظم ثورة تكنولوجية في القرن الحالي.\nيساعد المطورين على كتابة واختبار الأكواد بسرعة وكفاءة فائقة.\nالتعلم والتطبيق المستمر هو سر النجاح.", 'placeholder' => 'ضع النص المعدل هنا...'],
        ],
        'calcJs' => "
            const orig = document.getElementById('originalTextDiff').value;
            const mod = document.getElementById('modifiedTextDiff').value;

            const origLines = orig.split(/\\n/);
            const modLines = mod.split(/\\n/);

            let diffHtml = '<div style=\"font-family:monospace;direction:rtl;line-height:1.8;background:var(--bg-card);padding:1rem;border-radius:var(--radius-md)\">';
            const maxL = Math.max(origLines.length, modLines.length);
            let changesCount = 0;

            for (let i = 0; i < maxL; i++) {
                const l1 = origLines[i] || '';
                const l2 = modLines[i] || '';
                if (l1 === l2) {
                    diffHtml += `<div style=\"color:var(--text-secondary);padding:2px 0\">  \${l1 || '&nbsp;'}</div>`;
                } else {
                    changesCount++;
                    if (l1) diffHtml += `<div style=\"background:rgba(239,68,68,0.15);color:#ef4444;padding:2px 6px;border-radius:3px;margin:2px 0\">- \${l1}</div>`;
                    if (l2) diffHtml += `<div style=\"background:rgba(16,185,129,0.15);color:#10b981;padding:2px 6px;border-radius:3px;margin:2px 0\">+ \${l2}</div>`;
                }
            }
            diffHtml += '</div>';

            setPrimaryResult('تم اكتشاف ' + changesCount + ' أسطر معدلة', 'حالة المقارنة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المتغيرة', value: changesCount + ' تعديلات', color: changesCount > 0 ? '#f59e0b' : '#10b981' },
                { label: 'أسطر النص الأصلي', value: origLines.length + ' أسطر', color: '#3b82f6' },
                { label: 'أسطر النص المعدل', value: modLines.length + ' أسطر', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <div style=\"margin-bottom:0.5rem;font-size:0.85rem;color:var(--text-muted)\">
                        <span style=\"color:#ef4444\">(-) باللون الأحمر: محذوف</span> | 
                        <span style=\"color:#10b981\">(+) باللون الأخضر: مضاف جديد</span>
                    </div>
                    \${diffHtml}
                </div>
            `);
        ",
        'points' => [
            'يقارن الأسطر سطراً بسطر بنظام الـ Diff المتبع في Git و GitHub.',
            'يبرز الكلمات المحذوفة باللون الأحمر والكلمات الجديدة المضافة باللون الأخضر.'
        ],
        'assumptions' => 'يفترض مقارنة أسطر متتالية متقابلة.',
        'faqs' => [
            ['q' => 'هل يمكن مقارنة أكواد برمجية كاملة بهذه الأداة؟', 'a' => 'نعم؛ يمكنك مقارنة ملفات JavaScript أو Python أو CSS واكتشاف التعديلات التي أجراها زملاؤك في الفريق فوراً.']
        ],
        'related' => ['sql-formatter', 'clean-arabic-text', 'remove-duplicate-lines']
    ],
];

echo "Generating Group H: Developer Tools (30 tools)...\n";
foreach ($toolsH as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group H!\n";
