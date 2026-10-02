<?php
/**
 * مولد أدوات المجموعة G: أدوات النصوص والمحتوى (31 أداة)
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/tool_generator_core.php';

$outputDir = __DIR__ . '/../tools';

$toolsG = [
    'markdown-arabic-editor' => [
        'title' => 'محرر Markdown عربي احترافي',
        'isFinancial' => false,
        'customHtml' => '
            <div class="card" style="margin-bottom:1.5rem">
                <div class="d-flex gap-1 mb-1" style="flex-wrap:wrap">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'# \', \'\')"><i class="fas fa-heading"></i> عنوان 1</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'## \', \'\')"><i class="fas fa-heading"></i> عنوان 2</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'**\', \'**\')"><i class="fas fa-bold"></i> عريض</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'*\', \'*\')"><i class="fas fa-italic"></i> مائل</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'> \', \'\')"><i class="fas fa-quote-right"></i> اقتباس</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'- \', \'\')"><i class="fas fa-list-ul"></i> قائمة</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'1. \', \'\')"><i class="fas fa-list-ol"></i> مرقمة</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'`\', \'`\')"><i class="fas fa-code"></i> كود</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd(\'[نص الرابط](\', \'https://example.com)\')"><i class="fas fa-link"></i> رابط</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertTable()"><i class="fas fa-table"></i> جدول</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="loadSampleMd()"><i class="fas fa-file-alt"></i> نموذج عربي</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="clearMd()" style="color:var(--danger)"><i class="fas fa-trash"></i> مسح</button>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:1rem">
                    <div>
                        <div class="d-flex justify-between align-center mb-1">
                            <span style="font-weight:600"><i class="fas fa-edit text-accent"></i> محرر Markdown</span>
                            <span id="mdWordCount" style="font-size:0.8rem;color:var(--text-muted)">0 كلمة | 0 حرف</span>
                        </div>
                        <textarea id="markdownInput" class="form-control" rows="18" style="font-family:monospace;direction:rtl;line-height:1.7" oninput="updatePreview()" placeholder="اكتب نصوص الماركداون هنا..."></textarea>
                    </div>
                    <div>
                        <div class="d-flex justify-between align-center mb-1">
                            <span style="font-weight:600"><i class="fas fa-eye text-accent"></i> المعاينة المباشرة (Live Preview)</span>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-ghost btn-sm" onclick="copyHtml()"><i class="fas fa-code"></i> نسخ HTML</button>
                                <button type="button" class="btn btn-ghost btn-sm" onclick="downloadMdFile()"><i class="fas fa-download"></i> تنزيل .md</button>
                            </div>
                        </div>
                        <div id="markdownPreview" class="form-control" style="min-height:430px;max-height:430px;overflow-y:auto;background:var(--bg-card);direction:rtl;line-height:1.8;padding:1.25rem"></div>
                    </div>
                </div>
            </div>
        ',
        'customJs' => '
            <script>
            function parseMarkdown(md) {
                if (!md) return "<p style=\"color:var(--text-muted)\">اكتب في المحرر لمشاهدة المعاينة الفورية هنا...</p>";
                let html = md
                    .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
                    .replace(/^### (.*$)/gim, "<h3 style=\"margin:1rem 0 0.5rem;color:var(--text-primary)\">$1</h3>")
                    .replace(/^## (.*$)/gim, "<h2 style=\"margin:1.25rem 0 0.5rem;color:var(--text-accent-light)\">$1</h2>")
                    .replace(/^# (.*$)/gim, "<h1 style=\"margin:1.5rem 0 0.75rem;border-bottom:1px solid var(--border-color);padding-bottom:0.5rem\">$1</h1>")
                    .replace(/^\> (.*$)/gim, "<blockquote style=\"border-right:4px solid var(--primary);padding:0.5rem 1rem;background:var(--bg-glass);margin:0.75rem 0\">$1</blockquote>")
                    .replace(/\*\*(.*?)\*\*/gim, "<strong>$1</strong>")
                    .replace(/\*(.*?)\*/gim, "<em>$1</em>")
                    .replace(/~~(.*?)~~/gim, "<del>$1</del>")
                    .replace(/`([^`]+)`/gim, "<code style=\"background:var(--bg-input);padding:2px 6px;border-radius:4px;color:var(--text-accent-light)\">$1</code>")
                    .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, "<a href=\"$2\" target=\"_blank\" style=\"color:var(--primary);text-decoration:underline\">$1</a>")
                    .replace(/^\- (.*$)/gim, "<li style=\"margin-right:1.2rem\">$1</li>")
                    .replace(/^\d+\. (.*$)/gim, "<li style=\"margin-right:1.2rem\">$1</li>")
                    .replace(/\n\n/gim, "</p><p style=\"margin-bottom:0.75rem\">")
                    .replace(/\n/gim, "<br>");
                return "<p style=\"margin-bottom:0.75rem\">" + html + "</p>";
            }

            function updatePreview() {
                const text = document.getElementById("markdownInput").value;
                document.getElementById("markdownPreview").innerHTML = parseMarkdown(text);
                const words = text.trim() ? text.trim().split(/\s+/).length : 0;
                document.getElementById("mdWordCount").textContent = words + " كلمة | " + text.length + " حرف";
                localStorage.setItem("elta6ur_md_arabic", text);
            }

            function insertMd(prefix, suffix) {
                const textarea = document.getElementById("markdownInput");
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
                const sel = textarea.value.substring(start, end);
                const replacement = prefix + (sel || "نص") + suffix;
                textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
                textarea.focus();
                textarea.setSelectionRange(start + prefix.length, start + replacement.length - suffix.length);
                updatePreview();
            }

            function insertTable() {
                const tableTpl = "\\n| العمود 1 | العمود 2 | العمود 3 |\\n| :--- | :---: | ---: |\\n| قيمة 1 | قيمة 2 | قيمة 3 |\\n| بيانات أ | بيانات ب | بيانات ج |\\n";
                insertMd(tableTpl, "");
            }

            function loadSampleMd() {
                document.getElementById("markdownInput").value = "# منصة أدوات المطورين\\n\\nأهلاً بك في **المحرر العربي المتخصص** لكتابة وتنسيق مستندات الماركداون.\\n\\n## المميزات الرئيسية:\\n- دعم كامل للغة العربية واتجاه **RTL**\\n- معاينة حية سريعة وفورية\\n- إمكانية التصدير كملف `.md` أو كود `HTML`\\n\\n> إن المعرفة قوة، وتوثيق الأفكار هو أول خطوة لتحقيقها.\\n\\n```javascript\\nconsole.log(\'مرحباً بالعالم!\');\\n```\\n";
                updatePreview();
            }

            function clearMd() {
                if (confirm("هل تريد مسح النص بالكامل؟")) {
                    document.getElementById("markdownInput").value = "";
                    updatePreview();
                }
            }

            function copyHtml() {
                const html = document.getElementById("markdownPreview").innerHTML;
                navigator.clipboard.writeText(html).then(() => showToast("تم نسخ كود HTML بنجاح! ✅"));
            }

            function downloadMdFile() {
                const text = document.getElementById("markdownInput").value;
                const blob = new Blob([text], { type: "text/markdown;charset=utf-8" });
                const a = document.createElement("a");
                a.href = URL.createObjectURL(blob);
                a.download = "document.md";
                a.click();
            }

            document.addEventListener("DOMContentLoaded", () => {
                const saved = localStorage.getItem("elta6ur_md_arabic");
                if (saved) {
                    document.getElementById("markdownInput").value = saved;
                } else {
                    loadSampleMd();
                }
                updatePreview();
            });
            </script>
        ',
        'points' => [
            'لغة Markdown تتيح لك كتابة نصوص غنية التنسيق باستخدام علامات نصية بسيطة وسهلة الحفظ.',
            'المحرر يدعم تلقائياً اتجاه RTL المناسب للكتابة العربية، ويحفظ مسوداتك في المتصفح تلقائياً.'
        ],
        'assumptions' => 'المعاينة الفورية تعتمد محرك تحليل خفيف وسريع متوافق مع GitHub Flavored Markdown.',
        'faqs' => [
            ['q' => 'كيف أكتب عنواناً بالماركداون؟', 'a' => 'أضف علامة الشباك # قبل النص متبوعة بمسافة؛ # للعنوان الرئيسي و ## للعنوان الفرعي.']
        ],
        'related' => ['markdown-to-html', 'html-to-markdown', 'word-counter']
    ],

    'markdown-to-html' => [
        'title' => 'تحويل Markdown إلى HTML',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'markdownSourceText', 'label' => 'أدخل كود الماركداون (Markdown)', 'type' => 'textarea', 'rows' => 8, 'default' => "# عنوان المستند\n\nهذا نص تجريبي يحتوي على **خط عريض** و *خط مائل* مع [رابط تجريبي](https://example.com).\n\n- عنصر قائمة 1\n- عنصر قائمة 2\n\n> اقتباس ملهم", 'placeholder' => 'اكتب كود Markdown هنا...'],
        ],
        'calcJs' => "
            const md = document.getElementById('markdownSourceText').value;
            let html = md
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/^### (.*$)/gim, '<h3>$1</h3>')
                .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                .replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>')
                .replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/gim, '<em>$1</em>')
                .replace(/`([^`]+)`/gim, '<code>$1</code>')
                .replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href=\"$2\">$1</a>')
                .replace(/^\- (.*$)/gim, '<li>$1</li>')
                .replace(/\\n\\n/gim, '</p>\\n<p>')
                .replace(/\\n/gim, '<br>\\n');
            html = '<p>' + html + '</p>';

            setPrimaryResult('تم التحويل بنجاح (' + html.length + ' حرف HTML)', 'حالة التحويل إلى HTML');
            showResultArea();

            setDetailStats([
                { label: 'عدد أحرف Markdown المصدر', value: md.length + ' حرف', color: '#3b82f6' },
                { label: 'عدد أحرف كود HTML الناتج', value: html.length + ' حرف', color: '#10b981' },
                { label: 'عدد الأسطر', value: md.split(/\\n/).length + ' سطر', color: '#f59e0b' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود HTML الناتج:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"font-family:monospace;direction:ltr\" readonly>\${html}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يحول نصوص Markdown الشائعة إلى عناصر HTML5 دلالية ونظيفة.',
            'الكود الناتج جاهز للنشر في مواقع الويب ومدونات ووردبريس ومشاريع الويب.'
        ],
        'assumptions' => 'يفترض نصوص ماركداون قياسية وفق معايير CommonMark.',
        'faqs' => [
            ['q' => 'هل كود HTML الناتج آمن؟', 'a' => 'نعم؛ يتم فحص الوسوم وتشفير الأحرف الخطرة لحماية موقعك من ثغرات XSS.']
        ],
        'related' => ['html-to-markdown', 'markdown-arabic-editor', 'clean-html-text']
    ],

    'html-to-markdown' => [
        'title' => 'تحويل كود HTML إلى Markdown',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'htmlSourceText', 'label' => 'أدخل كود الـ HTML المطلوب تحويله', 'type' => 'textarea', 'rows' => 8, 'default' => "<h1>عنوان الصفحة</h1>\n<p>مرحباً بكم في <strong>منصتنا</strong>، لقراءة المزيد زوروا <a href=\"https://example.com\">موقعنا</a>.</p>\n<ul>\n  <li>الميزة الأولى</li>\n  <li>الميزة الثانية</li>\n</ul>", 'placeholder' => 'ضع كود HTML هنا...'],
        ],
        'calcJs' => "
            const html = document.getElementById('htmlSourceText').value;
            let md = html
                .replace(/<h1[^>]*>(.*?)<\\/h1>/gi, '# $1\\n\\n')
                .replace(/<h2[^>]*>(.*?)<\\/h2>/gi, '## $1\\n\\n')
                .replace(/<h3[^>]*>(.*?)<\\/h3>/gi, '### $1\\n\\n')
                .replace(/<strong[^>]*>(.*?)<\\/strong>/gi, '**$1**')
                .replace(/<b[^>]*>(.*?)<\\/b>/gi, '**$1**')
                .replace(/<em[^>]*>(.*?)<\\/em>/gi, '*$1*')
                .replace(/<i[^>]*>(.*?)<\\/i>/gi, '*$1*')
                .replace(/<code[^>]*>(.*?)<\\/code>/gi, '`$1`')
                .replace(/<blockquote[^>]*>(.*?)<\\/blockquote>/gi, '> $1\\n\\n')
                .replace(/<a[^>]*href=[\"\\\']([^\"\\\']*)[\"\\\'][^>]*>(.*?)<\\/a>/gi, '[$2]($1)')
                .replace(/<li[^>]*>(.*?)<\\/li>/gi, '- $1\\n')
                .replace(/<ul[^>]*>|<\\/ul>|<ol[^>]*>|<\\/ol>/gi, '')
                .replace(/<p[^>]*>(.*?)<\\/p>/gi, '$1\\n\\n')
                .replace(/<br[^>]*>/gi, '\\n')
                .replace(/<[^>]+>/g, '') // إزالة أي وسوم متبقية
                .replace(/\\n{3,}/g, '\\n\\n')
                .trim();

            setPrimaryResult('تم التحويل إلى Markdown (' + md.length + ' حرف)', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'أحرف كود HTML المصدر', value: html.length + ' حرف', color: '#3b82f6' },
                { label: 'أحرف Markdown الصافية', value: md.length + ' حرف', color: '#10b981' },
                { label: 'نسبة تقليص الحجم', value: html.length > 0 ? (((html.length - md.length)/html.length)*100).toFixed(0) + '% أنظف' : '0%', color: '#f59e0b' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود Markdown الناتج:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"font-family:monospace;direction:rtl\" readonly>\${md}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يجرد أكواد HTML من وسوم التنسيق الزائدة ويحولها إلى صيغة ماركداون نقية.',
            'مفيد جداً لنقل المقالات والمحتوى من ووردبريس أو مواقع الأخبار إلى ملفات التوثيق GitHub README.'
        ],
        'assumptions' => 'يفترض كود HTML صالح البنية مع وسوم فتح وإغلاق قياسية.',
        'faqs' => [
            ['q' => 'هل يحافظ التحويل على الروابط والخط العريض؟', 'a' => 'نعم؛ يتم الحفاظ على الروابط وعناوين المقالات والتعداد النقطي والخطوط العريضة والمائلة بدقة.']
        ],
        'related' => ['markdown-to-html', 'markdown-arabic-editor', 'clean-html-text']
    ],

    'arabic-word-counter' => [
        'title' => 'عداد الكلمات العربي المتخصص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'arabicWordInput', 'label' => 'ألصق أو اكتب النص العربي هنا', 'type' => 'textarea', 'rows' => 7, 'default' => 'بسم الله الرحمن الرحيم. القراءة تفتح آفاق العقل، وتمنح الإنسان قدرة استثنائية على فهم العالم من حوله والتعبير عن أفكاره بوضوح وإبداع.', 'placeholder' => 'اكتب النص العربي هنا لحساب كلماته بدقة...'],
        ],
        'calcJs' => "
            const text = document.getElementById('arabicWordInput').value;
            // تنظيف النص وتجاهل التشكيل عند عد الكلمات
            const cleanText = text.replace(/[\\u064B-\\u065F\\u0670]/g, '').trim();
            const words = cleanText ? cleanText.split(/\\s+/).filter(w => w.length > 0) : [];
            const charsWithSpaces = text.length;
            const charsNoSpaces = text.replace(/\\s/g, '').length;
            const lettersOnly = text.replace(/[^\\u0600-\\u06FFa-zA-Z]/g, '').length;
            const readingMins = (words.length / 200).toFixed(1);

            setPrimaryResult(words.length.toLocaleString() + ' كلمة عربية', 'إجمالي عدد الكلمات');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأحرف بدون مسافات', value: charsNoSpaces.toLocaleString() + ' حرف', color: '#10b981' },
                { label: 'عدد الأحرف مع المسافات', value: charsWithSpaces.toLocaleString() + ' حرف', color: '#3b82f6' },
                { label: 'عدد الحروف الهجائية الصافية', value: lettersOnly.toLocaleString() + ' حرف', color: '#f59e0b' },
                { label: 'وقت القراءة المقدر', value: readingMins + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>\${words.length} كلمة</strong> و <strong>\${charsNoSpaces} حرفاً</strong> بدون مسافات، ويستغرق قراءته بتركيز حوالي <strong>\${readingMins} دقيقة</strong>.</p>
            `);
        ",
        'points' => [
            'يقوم المحلل بتنظيف علامات التشكيل والتنوين تلقائياً لضمان عد دقيق وحقيقي للكلمات العربية.',
            'الكلمات المفصولة بفواصل أو نقاط أو أسطر جديدة تُحتسب ككلمات مستقلة.'
        ],
        'assumptions' => 'معدل سرعة القراءة العربي المعتمد هو 200 كلمة في الدقيقة.',
        'faqs' => [
            ['q' => 'هل تحسب واو العطف ككلمة مستقلة؟', 'a' => 'في القواعد الإملائية العربية المتصلة، الكلمة التي تبدأ بواو العطف مثل (والتعبير) تُعد ككلمة واحدة في معظم خوارزميات النصوص لأنها متصلة بدون مسافة فاصلة.']
        ],
        'related' => ['arabic-char-counter', 'sentence-counter', 'reading-time-calculator']
    ],

    'arabic-char-counter' => [
        'title' => 'عداد الأحرف والمسافات العربي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'charCounterInput', 'label' => 'أدخل النص لحساب الأحرف والرموز بدقة', 'type' => 'textarea', 'rows' => 6, 'default' => 'اللُّغَةُ العَرَبِيَّةُ هِيَ إِحْدَى أَكْثَرِ اللُّغَاتِ انْتِشَاراً فِي العَالَمِ.', 'placeholder' => 'اكتب النص هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('charCounterInput').value;
            const totalChars = text.length;
            const noSpaces = text.replace(/\\s/g, '').length;
            const spacesCount = (text.match(/\\s/g) || []).length;
            const tashkeelCount = (text.match(/[\\u064B-\\u065F\\u0670]/g) || []).length;
            const punctuationCount = (text.match(/[.,،؛:!؟?\"'()\\[\\]\\-]/g) || []).length;
            const digitsCount = (text.match(/[0-9\\u0660-\\u0669]/g) || []).length;

            setPrimaryResult(totalChars.toLocaleString() + ' حرف مع المسافات (' + noSpaces.toLocaleString() + ' بدونها)', 'إجمالي عدد الأحرف');
            showResultArea();

            setDetailStats([
                { label: 'الأحرف بدون مسافات', value: noSpaces.toLocaleString() + ' حرف', color: '#10b981' },
                { label: 'عدد المسافات الفارغة', value: spacesCount.toLocaleString() + ' مسافة', color: '#3b82f6' },
                { label: 'حركات التشكيل والتنوين', value: tashkeelCount.toLocaleString() + ' حركة', color: '#f59e0b' },
                { label: 'علامات الترقيم والأرقام', value: (punctuationCount + digitsCount) + ' رمز', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>إجمالي الأحرف: <strong>\${totalChars}</strong> (منها <strong>\${tashkeelCount} حركة تشكيل</strong> و <strong>\${spacesCount} مسافة</strong> و <strong>\${punctuationCount} علامة ترقيم</strong>).</p>
            `);
        ",
        'points' => [
            'يفصل العداد حركات التشكيل (الفتحة، الضمة، الكسرة، الشدة، التنوين) عن الحروف الأصلية.',
            'مفيد جداً لكتابة نصوص مقيدة بطول معين مثل إعلانات Google ومشاركات منصة X (تويتر سابقاً - 280 حرف).'
        ],
        'assumptions' => 'حركات التشكيل تحسب كرموز Unicode مستقلة في البرمجيات.',
        'faqs' => [
            ['q' => 'كم حرفاً يسمح به في تغريدة X (تويتر)؟', 'a' => 'الحد الأقصى هو 280 حرفاً للحسابات العادية، وتعتبر الحروف العربية مساوية لحرف واحد لكل حرف ومسافة.']
        ],
        'related' => ['arabic-word-counter', 'sentence-counter', 'clean-arabic-text']
    ],

    'sentence-counter' => [
        'title' => 'عداد الجمل في النص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'sentenceTextInput', 'label' => 'ألصق النص هنا لعد جمله', 'type' => 'textarea', 'rows' => 6, 'default' => 'العلم نور، والجهل ظلام. هل فكرت يوماً في سر تقدم الأمم؟ إنها القراءة والمعرفة والعمل الجاد! لا تتوقف عن التعلم مهما تقدم بك العمر.', 'placeholder' => 'اكتب النص هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('sentenceTextInput').value.trim();
            // تقسيم الجمل بالنقطة وعلامة الاستفهام والتعجب والفاصلة المنقوطة
            const sentences = text ? text.split(/[.!?؟؛\\n]+/).filter(s => s.trim().length > 3) : [];
            const words = text ? text.split(/\\s+/).filter(w => w.length > 0).length : 0;
            const avgWordsPerSentence = sentences.length > 0 ? (words / sentences.length) : 0;

            setPrimaryResult(sentences.length + ' جملة', 'إجمالي عدد الجمل في النص');
            showResultArea();

            setDetailStats([
                { label: 'عدد الجمل المكتشفة', value: sentences.length + ' جملة', color: '#10b981' },
                { label: 'إجمالي كلمات النص', value: words + ' كلمة', color: '#3b82f6' },
                { label: 'متوسط الكلمات في كل جملة', value: avgWordsPerSentence.toFixed(1) + ' كلمة / جملة', color: '#f59e0b' },
                { label: 'علامات الترقيم المستخدمة', value: (text.match(/[.!?؟؛]/g) || []).length + ' علامة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>\${sentences.length} جملة</strong> بمتوسط <strong>\${avgWordsPerSentence.toFixed(1)} كلمة لكل جملة</strong>. الجمل ذات الطول بين 12 إلى 18 كلمة هي الأكثر وضوحاً وسلاسة للقارئ.</p>
            `);
        ",
        'points' => [
            'يتم التعرف على نهاية الجمل بواسطة علامات الترقيم الختامية: النقطة (.)، علامة الاستفهام (؟ / ?)، علامة التعجب (!)، والفاصلة المنقوطة (؛).',
            'تجنب الجمل شديدة الطول (أكثر من 30 كلمة) يرفع من جودة الكتابة وسهولة فهمها للقارئ.'
        ],
        'assumptions' => 'الجمل التي تقل عن 3 أحرف تستبعد لتفادي النقاط الزائدة في الاختصارات.',
        'faqs' => [
            ['q' => 'هل الفاصلة العادية (،) تنهي الجملة؟', 'a' => 'الفاصلة العادية تفصل بين أجزاء الجملة الواحدة وليست نهاية تامة، وتعتبر النقطة وعلامات الاستفهام والتعجب هي المحددات الرسمية لختام الجملة التامة.']
        ],
        'related' => ['paragraph-counter', 'avg-sentence-length', 'arabic-word-counter']
    ],

    'paragraph-counter' => [
        'title' => 'عداد الفقرات والأسطر في النص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'paragraphInputText', 'label' => 'ألصق المقال أو النص هنا', 'type' => 'textarea', 'rows' => 8, 'default' => "الذكاء الاصطناعي يغير عالمنا بسرعة مذهلة، ويفتح آفاقاً لا حدود لها في الطب والتعليم والهندسة.\n\nمن المهم أن نستعد للمستقبل باكتساب مهارات رقمية متقدمة تواكب هذا التطور السريع.\n\nالاستمرار في التعلم هو الضمانة الحقيقية للنجاح والريادة في العصر الحديث.", 'placeholder' => 'اكتب النص هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('paragraphInputText').value;
            const paragraphs = text.split(/\\n\\s*\\n/).filter(p => p.trim().length > 0);
            const lines = text.split(/\\n/).filter(l => l.trim().length > 0);
            const emptyLines = text.split(/\\n/).filter(l => l.trim().length === 0).length;
            const words = text.trim() ? text.trim().split(/\\s+/).length : 0;
            const avgWordsPerPara = paragraphs.length > 0 ? (words / paragraphs.length) : 0;

            setPrimaryResult(paragraphs.length + ' فقرة (' + lines.length + ' سطر مكتوب)', 'إجمالي عدد الفقرات');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات المستقلة', value: paragraphs.length + ' فقرة', color: '#10b981' },
                { label: 'عدد الأسطر المكتوبة', value: lines.length + ' أسطر', color: '#3b82f6' },
                { label: 'الأسطر الفارغة الفاصلة', value: emptyLines + ' سطر فارغ', color: '#f59e0b' },
                { label: 'متوسط كلمات الفقرة', value: Math.round(avgWordsPerPara) + ' كلمة / فقرة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>\${paragraphs.length} فقرة</strong> موزعة على <strong>\${lines.length} سطر</strong> بمتوسط <strong>\${Math.round(avgWordsPerPara)} كلمة لكل فقرة</strong>.</p>
            `);
        ",
        'points' => [
            'الفقرة تُعرّف في تحرير النصوص بوجود سطر فارغ مزدوج يفصل بين الفكرة والأخرى.',
            'في المقالات الرقمية ومواقع الويب، يُفضل ألا تتجاوز الفقرة 3 إلى 5 أسطر لتحسين تجربة القراءة على شاشات الهواتف المحمولة.'
        ],
        'assumptions' => 'يفترض ضغط مفتاح Enter مرتين للفصل بين الفقرات المتعاقبة.',
        'faqs' => [
            ['q' => 'لماذا يُفضل قصر الفقرات في المحتوى الرقمي؟', 'a' => 'لأن القارئ على الهاتف المحمول يميل إلى التصفح السريع والمسح البصري (Scanning)، والفقرات الطويلة تبدو ككتل نصية مربكة ومنفرة للعين.']
        ],
        'related' => ['sentence-counter', 'split-text-paragraphs', 'remove-empty-lines']
    ],

    'avg-sentence-length' => [
        'title' => 'حساب متوسط طول الجملة وسهولة القراءة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'avgSentenceTextInput', 'label' => 'أدخل النص لقياس سهولة قراءته ومتوسط طول الجمل', 'type' => 'textarea', 'rows' => 7, 'default' => 'التخطيط المالي السليم هو سر راحة البال. عندما تضع ميزانية واضحة، تستطيع التحكم في مصاريفك وتفادي الديون. ابدأ اليوم بتسجيل نفقاتك اليومية الصغيرة.', 'placeholder' => 'اكتب النص هنا...'],
        ],
        'calcJs' => "
            const text = document.getElementById('avgSentenceTextInput').value.trim();
            const words = text ? text.split(/\\s+/).filter(w => w.length > 0) : [];
            const sentences = text ? text.split(/[.!?؟؛\\n]+/).filter(s => s.trim().length > 3) : [];

            const totalWords = words.length;
            const totalSentences = Math.max(1, sentences.length);
            const avgLength = totalWords / totalSentences;

            let readability = 'سلس وسهل الفهم جداً للجمهور العام 🌟';
            let color = '#10b981';
            if (avgLength > 25) { readability = 'معقد وطويل جداً ويحتاج للتبسيط وتقصير الجمل ⚠️'; color = '#ef4444'; }
            else if (avgLength > 18) { readability = 'متوسط التعقيد (مناسب للمقالات الأكاديمية والرسمية)'; color = '#f59e0b'; }

            setPrimaryResult(avgLength.toFixed(1) + ' كلمة لكل جملة', 'متوسط طول الجملة');
            showResultArea();

            setDetailStats([
                { label: 'مستوى سهولة القراءة (Readability)', value: readability, color: color },
                { label: 'عدد الجمل الكلي', value: totalSentences + ' جملة', color: '#3b82f6' },
                { label: 'عدد الكلمات الكلي', value: totalWords + ' كلمة', color: '#10b981' },
                { label: 'المعدل المثالي الموصى به', value: '12 إلى 18 كلمة / جملة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>متوسط طول الجمل في النص هو <strong>\${avgLength.toFixed(1)} كلمة</strong>. تقييم السلاسة: <strong>\${readability}</strong>.</p>
            `);
        ",
        'points' => [
            'متوسط طول الجملة = إجمالي الكلمات ÷ إجمالي الجمل.',
            'المعيار الصحفي العالمي للكتابة الواضحة يوصي بمتوسط 14 إلى 17 كلمة للجملة الواحدة لضمان أقصى قدر من الاستيعاب.'
        ],
        'assumptions' => 'التقييم مبني على مؤشرات سهولة القراءة المعيارية للنصوص العربية.',
        'faqs' => [
            ['q' => 'كيف أختصر الجمل الطويلة؟', 'a' => 'احذف حشو الكلمات الزائدة، واستبدل واوات العطف المتتالية بنقاط لتقسيم الفكرة الكبيرة إلى جملتين أو ثلاث جمل قصيرة مستقلة.']
        ],
        'related' => ['sentence-counter', 'reading-time-calculator', 'speech-time-calculator']
    ],

    'reading-time-calculator' => [
        'title' => 'حساب وقت قراءة النص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'readingTextInput', 'label' => 'ألصق المقال أو المحتوى لحساب مدة قراءته', 'type' => 'textarea', 'rows' => 8, 'default' => "تعتبر القراءة من أهم الأدوات التي تفتح آفاق العقل البشري، وتمنحه القدرة على رؤية العالم من زوايا متعددة ومختلفة. عندما يقرأ الإنسان، فإنه لا يكتفي باستيعاب المعلومات فحسب، بل يتدرب عقله على التحليل النقدي والتفكير المنطقي والربط بين الأفكار المتباعدة.\n\nإن القارئ النهم يعيش مئات الحيوات في حياة واحدة، ويتعلم من خلاصة تجارب وحكمة الآخرين التي استغرقت عقوداً من الزمن لتدوينها. الاستثمار في القراءة اليومية هو أعظم استثمار يمكن أن يقدمه الشخص لنفسه ولمستقبله المهني والشخصي.", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'readingSpeedWpm', 'label' => 'سرعة القراءة المستهدفة', 'type' => 'select', 'options' => [
                '160' => 'قراءة متأنية ودقيقة (دراسة وتحليل) ~ 160 كلمة/دقيقة',
                '200' => 'قراءة قياسية طبيعية للمقالات ~ 200 كلمة/دقيقة',
                '250' => 'قراءة سريعة واعية ~ 250 كلمة/دقيقة'
            ], 'default' => '200'],
        ],
        'calcJs' => "
            const text = document.getElementById('readingTextInput').value.trim();
            const wpm = parseFloat(document.getElementById('readingSpeedWpm').value) || 200;
            const words = text ? text.split(/\\s+/).filter(w => w.length > 0).length : 0;

            const totalMinutes = words / wpm;
            const mins = Math.floor(totalMinutes);
            const secs = Math.round((totalMinutes - mins) * 60);

            let timeStr = '';
            if (mins > 0) timeStr += mins + ' دقيقة ';
            if (secs > 0) timeStr += 'و ' + secs + ' ثانية';
            if (timeStr === '') timeStr = 'أقل من 5 ثوانٍ';

            setPrimaryResult(timeStr, 'الوقت المقدر لقراءة المقال');
            showResultArea();

            setDetailStats([
                { label: 'عدد كلمات النص', value: words.toLocaleString() + ' كلمة', color: '#3b82f6' },
                { label: 'سرعة القراءة المعتمدة', value: wpm + ' كلمة / دقيقة', color: '#10b981' },
                { label: 'عدد الأحرف والرموز', value: text.length.toLocaleString() + ' حرف', color: '#f59e0b' },
                { label: 'الوقت بالدقائق العشرية', value: totalMinutes.toFixed(1) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يحتوي النص على <strong>\${words} كلمة</strong>. يستغرق القارئ العادي حوالي <strong>\${timeStr}</strong> لإنهاء قراءته وفهم محتواه.</p>
            `);
        ",
        'points' => [
            'وقت القراءة = عدد كلمات النص ÷ سرعة القراءة بالكلمة في الدقيقة (WPM).',
            'إضافة علامة (وقت القراءة المقدر: 3 دقائق) في بداية المقالات والمدونات يرفع نسبة إكمال القراءة بنسبة 40%.'
        ],
        'assumptions' => 'معدل سرعة القراءة الصامتة للبالغين باللغة العربية يتراوح بين 180 إلى 220 كلمة/دقيقة.',
        'faqs' => [
            ['q' => 'لماذا تختلف سرعة القراءة الصوتية عن الصامتة؟', 'a' => 'القراءة الصامتة أسرع بحوالي 40% لأن العين والدماغ يعالجان الكلمات كصور وأنماط مباشرة دون انتظار حركة عضلات اللسان والحبال الصوتية.']
        ],
        'related' => ['speech-time-calculator', 'arabic-word-counter', 'avg-sentence-length']
    ],

    'speech-time-calculator' => [
        'title' => 'حساب وقت إلقاء النص والخطاب (Speech Time)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'speechTextInput', 'label' => 'ألصق نص الخطاب أو العرض التقديمي أو الفيديو', 'type' => 'textarea', 'rows' => 8, 'default' => "السلام عليكم ورحمة الله وبركاته،\nأيها الحضور الكريم، يسعدني ويشرفني أن أقف بينكم اليوم لنتحدث عن مستقبل التحول الرقمي، وكيف تسهم التكنولوجيا في تمكين الشباب العربي من بناء مشاريع رائدة.\n\nإن التحديات التي نواجهها اليوم هي ذاتها الفرص التي ستصنع قادة الغد، وبالعزيمة والعمل المشترك سنحقق الطموحات.", 'placeholder' => 'ضع نص الخطاب هنا...'],
            ['id' => 'speakingPace', 'label' => 'سرعة ونبرة الإلقاء', 'type' => 'select', 'options' => [
                '110' => 'إلقاء بطيء ورسمي وهادئ مع وقفات مؤثرة (110 كلمة/دقيقة)',
                '130' => 'إلقاء خطابي قياسي للمؤتمرات والندوات (130 كلمة/دقيقة)',
                '150' => 'إلقاء سريع وإعلاني وتفاعلي (فيديوهات يوتيوب وتيك توك) ~ 150 كلمة/دقيقة'
            ], 'default' => '130'],
        ],
        'calcJs' => "
            const text = document.getElementById('speechTextInput').value.trim();
            const wpm = parseFloat(document.getElementById('speakingPace').value) || 130;
            const words = text ? text.split(/\\s+/).filter(w => w.length > 0).length : 0;

            const totalMins = words / wpm;
            const mins = Math.floor(totalMins);
            const secs = Math.round((totalMins - mins) * 60);

            let timeStr = '';
            if (mins > 0) timeStr += mins + ' دقيقة ';
            if (secs > 0) timeStr += 'و ' + secs + ' ثانية';
            if (timeStr === '') timeStr = 'أقل من 5 ثوانٍ';

            setPrimaryResult(timeStr, 'مدة الإلقاء والحديث على المسرح');
            showResultArea();

            setDetailStats([
                { label: 'عدد كلمات الخطاب', value: words.toLocaleString() + ' كلمة', color: '#3b82f6' },
                { label: 'سرعة الإلقاء الصوتية', value: wpm + ' كلمة / دقيقة', color: '#10b981' },
                { label: 'الشرائح المقترحة للعرض (Slide)', value: Math.ceil(totalMins / 2) + ' شرائح (بمعدل دقيقتين للوحة)', color: '#f59e0b' },
                { label: 'الوقت بالدقائق العشرية', value: totalMins.toFixed(1) + ' دقيقة', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>يستغرق إلقاء هذا النص أمام الجمهور حوالي <strong>\${timeStr}</strong> بنبرة إلقاء <strong>\${wpm} كلمة/دقيقة</strong> مع مراعاة فترات التوقف والتنفس الطبيعية.</p>
            `);
        ",
        'points' => [
            'السرعة الذهبية للخطابات المؤثرة والعروض التقديمية هي 120 إلى 140 كلمة في الدقيقة.',
            'التحدث بسرعة أعلى من 160 كلمة/دقيقة يصعب على الجمهور متابعة واستيعاب النقاط المحورية.'
        ],
        'assumptions' => 'يفترض وقفات تنفس طبيعية وتأكيداً على الكلمات المفتاحية.',
        'faqs' => [
            ['q' => 'كم عدد الكلمات المناسبة لعرض تقديمي مدته 10 دقائق؟', 'a' => 'الخطاب المثالي لمدة 10 دقائق يحتوي بين 1200 إلى 1300 كلمة كحد أقصى لإتاحة وقت للترحيب والأسئلة والوقفات التوضيحية.']
        ],
        'related' => ['reading-time-calculator', 'arabic-word-counter', 'avg-sentence-length']
    ],

    'clean-arabic-text' => [
        'title' => 'تنظيف وتوحيد النص العربي وإزالة التشكيل',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawArabicText', 'label' => 'أدخل النص العربي المطلوب تنظيفه', 'type' => 'textarea', 'rows' => 6, 'default' => 'هَـٰذَا نَصٌّ عَرَبِـــــيّ مُشَكَّلٌ وَيَحْتَوِي عَلَى هَمَزَاتٍ مُخْتَلِفَةٍ كَمَا فِي (إِنَّمَا وَأَنْتُمْ وَهَؤُلَاءِ).', 'placeholder' => 'ضع النص العربي هنا...'],
            ['id' => 'removeTashkeelOpt', 'label' => 'إزالة حركات التشكيل والتنوين', 'type' => 'select', 'options' => ['yes' => 'نعم، إزالة التشكيل كاملاً', 'no' => 'لا، الإبقاء على التشكيل'], 'default' => 'yes'],
            ['id' => 'removeTatweelOpt', 'label' => 'إزالة التطويل والكشيدة (ـ)', 'type' => 'select', 'options' => ['yes' => 'نعم، إزالة الكشيدة والتطويل', 'no' => 'لا'], 'default' => 'yes'],
            ['id' => 'normalizeAlefOpt', 'label' => 'توحيد الهمزات (أ، إ، آ -> ا)', 'type' => 'select', 'options' => ['yes' => 'نعم، توحيد الألف', 'no' => 'لا، ترك الهمزات كما هي'], 'default' => 'yes'],
            ['id' => 'normalizeYaaHaaOpt', 'label' => 'توحيد الياء والتاء المربوطة (ى -> ي / ة -> ه)', 'type' => 'select', 'options' => ['none' => 'لا تقم بالتعديل', 'yaa_only' => 'توحيد الألف المقصورة فقط (ى -> ي)', 'all' => 'توحيد الياء والتاء المربوطة معاً'], 'default' => 'yaa_only'],
        ],
        'calcJs' => "
            let text = document.getElementById('rawArabicText').value;
            const rmTashkeel = document.getElementById('removeTashkeelOpt').value === 'yes';
            const rmTatweel = document.getElementById('removeTatweelOpt').value === 'yes';
            const normAlef = document.getElementById('normalizeAlefOpt').value === 'yes';
            const normYaaHaa = document.getElementById('normalizeYaaHaaOpt').value;

            if (rmTashkeel) {
                text = text.replace(/[\\u064B-\\u065F\\u0670]/g, '');
            }
            if (rmTatweel) {
                text = text.replace(/\\u0640/g, ''); // إزالة الكشيدة ـ
            }
            if (normAlef) {
                text = text.replace(/[أإآ]/g, 'ا');
            }
            if (normYaaHaa === 'yaa_only' || normYaaHaa === 'all') {
                text = text.replace(/ى/g, 'ي');
            }
            if (normYaaHaa === 'all') {
                text = text.replace(/ة/g, 'ه');
            }

            text = text.replace(/[ ]{2,}/g, ' ').trim();

            setPrimaryResult('تم تنظيف وتوحيد النص بنجاح (' + text.length + ' حرف)', 'حالة النص');
            showResultArea();

            setDetailStats([
                { label: 'عدد أحرف النص النظيف', value: text.length + ' حرف', color: '#10b981' },
                { label: 'عدد الكلمات الصافية', value: (text ? text.split(/\\s+/).length : 0) + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص المنظف الجاهز للنسخ:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:rtl;line-height:1.8\" readonly>\${text}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تنظيف النصوص العربية ضروري جداً لتدريب نماذج الذكاء الاصطناعي ومعالجة اللغات الطبيعية (NLP) وبناء محركات البحث.',
            'إزالة الكشيدة وعلامات التشكيل تضمن تطابق الكلمات أثناء البحث والفهرسة.'
        ],
        'assumptions' => 'يحافظ التحويل على الأرقام والكلمات الإنجليزية المدمجة.',
        'faqs' => [
            ['q' => 'متى يجب توحيد الهمزات؟', 'a' => 'عند معالجة البيانات وبناء فهارس البحث SEO، لأن المستخدمين يبحثون غالباً بالألف المجردة (ا) بدلاً من (أ) أو (إ).']
        ],
        'related' => ['remove-extra-spaces', 'arabic-word-counter', 'arabic-char-counter']
    ],

    'remove-extra-spaces' => [
        'title' => 'إزالة المسافات الزائدة وضبط التباعد',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'spacesInputText', 'label' => 'ألصق النص الذي يحتوي على مسافات مشتتة أو فراغات زائدة', 'type' => 'textarea', 'rows' => 6, 'default' => 'هذا    نص     يحتوي   على   مسافات    فارغة     كثيرة    بين   الكلمات .  وكذلك   قبل   علامات  الترقيم  .', 'placeholder' => 'اكتب النص هنا...'],
        ],
        'calcJs' => "
            let text = document.getElementById('spacesInputText').value;
            // إزالة المسافات المتكررة بين الكلمات
            let cleaned = text.replace(/[ ]{2,}/g, ' ');
            // إزالة المسافة قبل علامات الترقيم
            cleaned = cleaned.replace(/\\s+([.,،؛:!؟?])/g, '$1');
            // التأكد من وجود مسافة بعد علامة الترقيم إذا كان بعدها حرف
            cleaned = cleaned.replace(/([.,،؛:!؟?])([^\\s\\d])/g, '$1 $2');
            cleaned = cleaned.trim();

            const savedChars = text.length - cleaned.length;

            setPrimaryResult('تم تقليص ' + savedChars + ' مسافة زائدة', 'حالة تنظيف المسافات');
            showResultArea();

            setDetailStats([
                { label: 'الأحرف المحذوفة الزائدة', value: savedChars + ' مسافة', color: '#10b981' },
                { label: 'طول النص الأصلي', value: text.length + ' حرف', color: '#ef4444' },
                { label: 'طول النص بعد الضبط', value: cleaned.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص المضبوط باحترافية:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:rtl;line-height:1.8\" readonly>\${cleaned}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يقوم المعالج بإلغاء المسافات المتعددة واستبدالها بمسافة واحدة قياسية.',
            'يصحح مواضع علامات الترقيم بحيث تلتصق بالكلمة السابقة وتتبعها مسافة واحدة تفصلها عن الكلمة اللاحقة وفقاً لقواعد الإملاء العربية السليمة.'
        ],
        'assumptions' => 'يحافظ على فواصل الأسطر دون حذف.',
        'faqs' => [
            ['q' => 'أين توضع المسافة بالنسبة لعلامات الترقيم؟', 'a' => 'القاعدة الإملائية العربية: علامة الترقيم (كالنقطة والفاصلة) تلتصق بالكلمة التي قبلها مباشرة بدون أي مسافة، وتترك مسافة واحدة بعدها قبل الكلمة التالية.']
        ],
        'related' => ['remove-empty-lines', 'clean-arabic-text', 'remove-duplicate-lines']
    ],

    'remove-empty-lines' => [
        'title' => 'إزالة الأسطر الفارغة الزائدة من النص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'emptyLinesInput', 'label' => 'ألصق النص المحتوي على أسطر فارغة', 'type' => 'textarea', 'rows' => 8, 'default' => "السطر الأول\n\n\n\nالسطر الثاني\n\n\n\nالسطر الثالث", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'emptyLineMode', 'label' => 'طريقة التنظيف المطلوبة', 'type' => 'select', 'options' => [
                'all' => 'حذف جميع الأسطر الفارغة نهائياً (دمج مباشر)',
                'single' => 'الإبقاء على سطر فارغ واحد فقط بين الفقرات (تنظيف التكرار الزائد)'
            ], 'default' => 'all'],
        ],
        'calcJs' => "
            const text = document.getElementById('emptyLinesInput').value;
            const mode = document.getElementById('emptyLineMode').value;

            let cleaned = '';
            if (mode === 'all') {
                cleaned = text.split(/\\n/).filter(line => line.trim().length > 0).join('\\n');
            } else {
                cleaned = text.replace(/\\n\\s*\\n\\s*\\n+/g, '\\n\\n').trim();
            }

            const origLines = text.split(/\\n/).length;
            const newLines = cleaned.split(/\\n/).length;
            const removed = origLines - newLines;

            setPrimaryResult('تم حذف ' + removed + ' سطر فارغ', 'نتيجة المعالجة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المحذوفة', value: removed + ' سطر', color: '#10b981' },
                { label: 'عدد الأسطر الأصلية', value: origLines + ' سطر', color: '#ef4444' },
                { label: 'عدد الأسطر المتبقية', value: newLines + ' سطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص بعد إزالة الأسطر الفارغة:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:rtl;line-height:1.7\" readonly>\${cleaned}</textarea>
                </div>
            `);
        ",
        'points' => [
            'خيار حذف جميع الأسطر مفيد لضغط القوائم والبيانات والأكواد.',
            'خيار الإبقاء على سطر واحد يحافظ على تنسيق الفقرات للمقالات مع حذف المسافات الرأسية الشاذة.'
        ],
        'assumptions' => 'الأسطر التي تحتوي على مسافات بيضاء فقط تُعامل كأسطر فارغة.',
        'faqs' => [
            ['q' => 'هل تتأثر الكلمات والمسافات الأفقية؟', 'a' => 'لا؛ الأداة تعمل فقط على الفواصل الرأسية (Line breaks) دون المساس بالنصوص الأفقية.']
        ],
        'related' => ['remove-extra-spaces', 'remove-duplicate-lines', 'merge-lines-tool']
    ],

    'remove-duplicate-lines' => [
        'title' => 'إزالة التكرار من النص والأسطر المتشابهة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'dupLinesInput', 'label' => 'ألصق القائمة أو الأسطر المراد إزالة التكرار منها', 'type' => 'textarea', 'rows' => 8, 'default' => "تفاح\nبرتقال\nموز\nتفاح\nعنب\nبرتقال\nمانجو", 'placeholder' => 'ضع الأسطر هنا (سطر لكل عنصر)...'],
            ['id' => 'caseSensitiveDup', 'label' => 'حساسية حالة الأحرف والمسافات', 'type' => 'select', 'options' => [
                'trim' => 'تجاهل المسافات البادئة والختامية (موصى به)',
                'exact' => 'تطابق حرفي تام'
            ], 'default' => 'trim'],
        ],
        'calcJs' => "
            const text = document.getElementById('dupLinesInput').value;
            const mode = document.getElementById('caseSensitiveDup').value;

            const lines = text.split(/\\n/);
            const seen = new Set();
            const uniqueLines = [];

            lines.forEach(line => {
                const key = mode === 'trim' ? line.trim() : line;
                if (key.length > 0 && !seen.has(key)) {
                    seen.add(key);
                    uniqueLines.push(line.trim());
                }
            });

            const resultText = uniqueLines.join('\\n');
            const dupesRemoved = lines.filter(l => l.trim().length > 0).length - uniqueLines.length;

            setPrimaryResult('تم حذف ' + dupesRemoved + ' عنصر مكرر بنجاح', 'إزالة التكرار');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر الفريدة المتبقية', value: uniqueLines.length + ' عنصر', color: '#10b981' },
                { label: 'عدد الأسطر المكررة المحذوفة', value: dupesRemoved + ' تكرار', color: '#ef4444' },
                { label: 'إجمالي عناصر القائمة الأصلية', value: lines.filter(l => l.trim().length > 0).length + ' أسطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">القائمة الفريدة المصفاة:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:rtl;line-height:1.7\" readonly>\${resultText}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تحذف الأداة الأسطر المكررة وتحافظ على الترتيب الأصلي لأول ظهور لكل عنصر.',
            'مثالية لتنظيف قوائم البريد الإلكتروني، الكلمات المفتاحية، وأرقام الهواتف وبيانات العملاء.'
        ],
        'assumptions' => 'الأسطر الفارغة تُستبعد تلقائياً.',
        'faqs' => [
            ['q' => 'هل تحافظ الأداة على ترتيب القائمة الأصلي؟', 'a' => 'نعم؛ يتم الحفاظ على ترتيب الأسطر كما أدخلتها تماماً، مع حذف التكرارات اللاحقة فقط.']
        ],
        'related' => ['sort-text-lines', 'remove-empty-lines', 'text-to-list-converter']
    ],

    'sort-text-lines' => [
        'title' => 'ترتيب النص والأسطر أبجدياً (أ إلى ي)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'sortLinesInput', 'label' => 'ألصق الأسطر المراد ترتيبها', 'type' => 'textarea', 'rows' => 7, 'default' => "محمد\nأحمد\nخالد\nإبراهيم\nيوسف\nعمر\nبلال", 'placeholder' => 'اكتب سطراً لكل عنصر...'],
            ['id' => 'sortDirection', 'label' => 'اتجاه الترتيب', 'type' => 'select', 'options' => [
                'asc' => 'أبجدي تصاعدي (أ -> ي / A -> Z)',
                'desc' => 'أبجدي تنازلي (ي -> أ / Z -> A)',
                'length_asc' => 'حسب طول السطر (من الأقصر للأطول)',
                'length_desc' => 'حسب طول السطر (من الأطول للأقصر)'
            ], 'default' => 'asc'],
        ],
        'calcJs' => "
            const text = document.getElementById('sortLinesInput').value;
            const dir = document.getElementById('sortDirection').value;

            let lines = text.split(/\\n/).filter(l => l.trim().length > 0);

            if (dir === 'asc') {
                lines.sort((a, b) => a.trim().localeCompare(b.trim(), 'ar'));
            } else if (dir === 'desc') {
                lines.sort((a, b) => b.trim().localeCompare(a.trim(), 'ar'));
            } else if (dir === 'length_asc') {
                lines.sort((a, b) => a.trim().length - b.trim().length);
            } else if (dir === 'length_desc') {
                lines.sort((a, b) => b.trim().length - a.trim().length);
            }

            const sortedText = lines.join('\\n');

            setPrimaryResult('تم ترتيب ' + lines.length + ' سطر بنجاح', 'حالة الترتيب');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المرتبة', value: lines.length + ' عنصر', color: '#10b981' },
                { label: 'النوع المعتمد', value: dir.includes('length') ? 'حسب الطول' : 'أبجدي عربي', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">الأسطر بعد الترتيب:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:rtl;line-height:1.7\" readonly>\${sortedText}</textarea>
                </div>
            `);
        ",
        'points' => [
            'الترتيب الأبجدي يعتمد الترتيب الهجائي العربي الرسمي مع مراعاة الحروف الخاصة والهمزات.',
            'يتوفر أيضاً خيار الترتيب حسب طول السطر وهو مفيد لتنظيم الكلمات الدلالية والشعارات.'
        ],
        'assumptions' => 'الأسطر الفارغة تُستثنى من الترتيب.',
        'faqs' => [
            ['q' => 'كيف يعامل الترتيب همزة الوصل والقطع؟', 'a' => 'تتبع الأداة الترتيب اللغوي القياسي لـ Unicode الخاص باللغة العربية حيث تأتي الألف بكافة أشكالها (أ، إ، آ، ا) في بداية الترتيب.']
        ],
        'related' => ['remove-duplicate-lines', 'text-to-list-converter', 'clean-arabic-text']
    ],

    'text-to-list-converter' => [
        'title' => 'تحويل النص العربي إلى قائمة منسقة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawListInput', 'label' => 'أدخل العناصر (مفصولة بأسطر أو فواصل)', 'type' => 'textarea', 'rows' => 6, 'default' => "إتقان البرمجة\nتصميم الواجهات\nالتسويق الرقمي\nإدارة المشاريع", 'placeholder' => 'اكتب العناصر هنا...'],
            ['id' => 'listFormatStyle', 'label' => 'نوع وشكل القائمة المطلوبة', 'type' => 'select', 'options' => [
                'bullets' => 'قائمة نقطية (• عنصر)',
                'dashed' => 'قائمة شُرطية (- عنصر)',
                'numbered' => 'قائمة مرقمة (1. عنصر)',
                'letters' => 'قائمة هجائية (أ. ب. ج.)',
                'html_ul' => 'كود HTML نقطي (<ul><li>)',
                'html_ol' => 'كود HTML رقمي (<ol><li>)'
            ], 'default' => 'bullets'],
        ],
        'calcJs' => "
            const text = document.getElementById('rawListInput').value;
            const style = document.getElementById('listFormatStyle').value;

            // فصل العناصر سواء كانت أسطراً أو مفصولة بفواصل
            let items = text.split(/\\n|,|،/).map(i => i.trim()).filter(i => i.length > 0);
            let formatted = '';

            const abjad = ['أ', 'ب', 'ج', 'د', 'هـ', 'و', 'ز', 'ح', 'ط', 'ي', 'ك', 'ل', 'م', 'ن', 'س', 'ع', 'ف', 'ص', 'ق', 'ر', 'ش', 'ت', 'ث', 'خ', 'ذ', 'ض', 'ظ', 'غ'];

            if (style === 'bullets') {
                formatted = items.map(i => '• ' + i).join('\\n');
            } else if (style === 'dashed') {
                formatted = items.map(i => '- ' + i).join('\\n');
            } else if (style === 'numbered') {
                formatted = items.map((i, idx) => (idx + 1) + '. ' + i).join('\\n');
            } else if (style === 'letters') {
                formatted = items.map((i, idx) => (abjad[idx % abjad.length] || (idx+1)) + '. ' + i).join('\\n');
            } else if (style === 'html_ul') {
                formatted = '<ul>\\n' + items.map(i => '  <li>' + i + '</li>').join('\\n') + '\\n</ul>';
            } else if (style === 'html_ol') {
                formatted = '<ol>\\n' + items.map(i => '  <li>' + i + '</li>').join('\\n') + '\\n</ol>';
            }

            setPrimaryResult('تم إنشاء قائمة من ' + items.length + ' عناصر', 'قائمة منسقة');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر بالقائمة', value: items.length + ' عناصر', color: '#10b981' },
                { label: 'النوع المعتمد', value: style, color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">القائمة المنسقة الناتجة:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:\${style.includes('html') ? 'ltr' : 'rtl'};line-height:1.8\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تحول النصوص المشتتة أو المدخلات المفصولة بفواصل إلى قوائم نقطية أو مرقمة أنيقة جاهزة للنشر.',
            'تدعم الترقيم الأبجدي العربي (أ، ب، ج، د) وكود HTML للقوائم.'
        ],
        'assumptions' => 'تتعرف الأداة على الفواصل العربية (،) والإنجليزية (,) كفواصل للعناصر.',
        'faqs' => [
            ['q' => 'هل يمكن تصديرها ككود HTML جاهز؟', 'a' => 'نعم؛ باختيار خيار HTML UL أو OL يتم توليد وسم القائمة مع وسوم <li> لكل عنصر.']
        ],
        'related' => ['merge-lines-tool', 'sort-text-lines', 'markdown-to-html']
    ],

    'merge-lines-tool' => [
        'title' => 'دمج الأسطر في سطر واحد أو فقرة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'mergeInputText', 'label' => 'ألصق الأسطر المراد دمجها', 'type' => 'textarea', 'rows' => 6, 'default' => "السطر الأول من الفكرة\nتكملة السطر الثاني\nالخاتمة للسطر الثالث", 'placeholder' => 'ضع الأسطر هنا...'],
            ['id' => 'mergeSeparatorOption', 'label' => 'الفاصل المستخدم بين الأسطر المدمجة', 'type' => 'select', 'options' => [
                'space' => 'مسافة واحدة (تحويل لفقرة متصلة)',
                'comma_ar' => 'فاصلة عربية ومسافة (، )',
                'comma_en' => 'فاصلة إنجليزية ومسافة (, )',
                'dash' => 'شرطة مع مسافات ( - )',
                'pipe' => 'رمز الفاصل الرأسي ( | )',
                'none' => 'دمج مباشر بدون أي فواصل'
            ], 'default' => 'space'],
        ],
        'calcJs' => "
            const text = document.getElementById('mergeInputText').value;
            const sepOpt = document.getElementById('mergeSeparatorOption').value;

            let sep = ' ';
            if (sepOpt === 'comma_ar') sep = '، ';
            if (sepOpt === 'comma_en') sep = ', ';
            if (sepOpt === 'dash') sep = ' - ';
            if (sepOpt === 'pipe') sep = ' | ';
            if (sepOpt === 'none') sep = '';

            const lines = text.split(/\\n/).map(l => l.trim()).filter(l => l.length > 0);
            const merged = lines.join(sep);

            setPrimaryResult('تم دمج ' + lines.length + ' أسطر في نص واحد', 'حالة الدمج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الأسطر المدمجة', value: lines.length + ' أسطر', color: '#10b981' },
                { label: 'طول النص الناتج', value: merged.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص بعد الدمج:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:rtl;line-height:1.8\" readonly>\${merged}</textarea>
                </div>
            `);
        ",
        'points' => [
            'مثالية لإصلاح النصوص المنسوخة من ملفات PDF التي تحتوي على انكسارات أسطر مفاجئة ومشوهة.',
            'تتيح دمج الكلمات المفتاحية في سطر واحد مفصول بفواصل مناسبة لمحركات البحث.'
        ],
        'assumptions' => 'يتم حذف المسافات الزائدة من أطراف كل سطر قبل الدمج.',
        'faqs' => [
            ['q' => 'كيف أصلح نصوص PDF المشوهة بالأسطر القصيرة؟', 'a' => 'اختر خيار الدمج بمسافة واحدة؛ ستقوم الأداة بوصل الجمل المكسورة وتحويلها إلى فقرة متصلة طبيعية وسلسة.']
        ],
        'related' => ['text-to-list-converter', 'remove-empty-lines', 'remove-extra-spaces']
    ],

    'split-text-paragraphs' => [
        'title' => 'تقسيم النص الطويل إلى فقرات متناسقة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'longTextInputSplit', 'label' => 'ألصق النص الطويل المراد تقسيمه', 'type' => 'textarea', 'rows' => 8, 'default' => "النجاح ليس وليد الصدفة بل هو نتاج عمل دؤوب وتخطيط محكم. كل خطوة تخطوها في سبيل تحقيق أهدافك تقربك من القمة. لا تيأس عند مواجهة العقبات فالفشل مجرد تجربة تثقل مهاراتك. اجعل شغفك هو الوقود الذي يدفعك للأمام واحرص دائماً على مساعدة الآخرين في طريقك نحو النجاح. إن التعاون والعمل الجماعي هما أساس كل إنجاز عظيم في هذا العصر الحديث.", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'splitCondition', 'label' => 'طريقة التقسيم', 'type' => 'select', 'options' => [
                'sentences_2' => 'فقرة كل جملتين (2 جمل)',
                'sentences_3' => 'فقرة كل 3 جمل (المعيار الموصى به)',
                'words_50' => 'فقرة كل 40 إلى 50 كلمة تقريباً'
            ], 'default' => 'sentences_2'],
        ],
        'calcJs' => "
            const text = document.getElementById('longTextInputSplit').value.trim();
            const cond = document.getElementById('splitCondition').value;

            let resultParas = [];

            if (cond.startsWith('sentences')) {
                const count = cond === 'sentences_2' ? 2 : 3;
                const sentences = text.match(/[^.!?؟؛]+[.!?؟؛]+/g) || [text];
                for (let i = 0; i < sentences.length; i += count) {
                    resultParas.push(sentences.slice(i, i + count).join(' ').trim());
                }
            } else {
                const words = text.split(/\\s+/);
                for (let i = 0; i < words.length; i += 45) {
                    resultParas.push(words.slice(i, i + 45).join(' ').trim());
                }
            }

            const formatted = resultParas.join('\\n\\n');

            setPrimaryResult('تم تقسيم النص إلى ' + resultParas.length + ' فقرات متوازنة', 'نتيجة التقسيم');
            showResultArea();

            setDetailStats([
                { label: 'عدد الفقرات الناتجة', value: resultParas.length + ' فقرة', color: '#10b981' },
                { label: 'إجمالي كلمات النص', value: text.split(/\\s+/).length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص مقسماً إلى فقرات سهلة القراءة:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"direction:rtl;line-height:1.8\" readonly>\${formatted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يقسم الكتل النصية الصعبة إلى فقرات صغيرة مريحة للعين لتحسين تجربة القراءة والـ UX على الهواتف.',
            'يعتمد على علامات الترقيم لضمان اكتمال المعنى قبل الانتقال لسطر جديد.'
        ],
        'assumptions' => 'يفترض وجود علامات ترقيم لتقسيم الجمل بدقة.',
        'faqs' => [
            ['q' => 'ما هو الحجم المثالي للفقرة في مقالات الويب؟', 'a' => 'الفقرة المثالية في المقالات الرقمية تتراوح بين جملتين إلى 4 جمل (حوالي 40 إلى 70 كلمة) لتسهيل القراءة السريعة على شاشات الموبايل.']
        ],
        'related' => ['paragraph-counter', 'sentence-counter', 'clean-arabic-text']
    ],

    'extract-urls-from-text' => [
        'title' => 'استخراج الروابط (URLs) من النص وتصفيتها',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'urlExtractInput', 'label' => 'ألصق النص المحتوي على روابط ومواقع إلكترونية', 'type' => 'textarea', 'rows' => 7, 'default' => "يمكنك زيارة موقعنا الرسمي https://google.com للمزيد من المعلومات.\nكما يمكنك متابعة أخبارنا على https://twitter.com/elta6ur أو مراسلتنا عبر الرابط المختصر bit.ly/test-link وموقع https://google.com مكرر.", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'removeDuplicateUrls', 'label' => 'إزالة الروابط المكررة', 'type' => 'select', 'options' => ['yes' => 'نعم، روابط فريدة فقط', 'no' => 'لا'], 'default' => 'yes'],
        ],
        'calcJs' => "
            const text = document.getElementById('urlExtractInput').value;
            const rmDup = document.getElementById('removeDuplicateUrls').value === 'yes';

            // تعبير نمطي دقيق لاستخراج كافة صيغ الروابط
            const urlRegex = /(https?:\\/\\/[^\\s\"\'<>()]+|www\\.[^\\s\"\'<>()]+|[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,6}\\/[^\\s\"\'<>()]*)/gi;
            let matches = text.match(urlRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches));
            }

            const resultList = matches.join('\\n');

            setPrimaryResult(matches.length + ' رابط تم استخراجه', 'عدد الروابط المكتشفة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الروابط المستخرجة', value: matches.length + ' رابط', color: '#10b981' },
                { label: 'حالة التكرار', value: rmDup ? 'تمت تصفية المكرر' : 'شامل التكرار', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">قائمة الروابط المستخرجة (سطر لكل رابط):</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"font-family:monospace;direction:ltr\" readonly>\${resultList}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تستخرج الأداة روابط http و https والروابط التي تبدأ بـ www والروابط المختصرة بدقة فائقة.',
            'مفيدة جداً للمسوقين والباحثين لجمع المراجع وقوائم المواقع من المقالات والرسائل.'
        ],
        'assumptions' => 'يتم تنظيف علامات الترقيم اللاحقة للرابط كالنقاط والأقواس تلقائياً.',
        'faqs' => [
            ['q' => 'هل تدعم الأداة الروابط المختصرة مثل bit.ly؟', 'a' => 'نعم؛ يتم استخراج كافة النطاقات والروابط المختصرة والفرعية بدقة.']
        ],
        'related' => ['extract-emails-from-text', 'extract-hashtags-from-text', 'url-parser']
    ],

    'extract-emails-from-text' => [
        'title' => 'استخراج البريد الإلكتروني من النص',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'emailExtractInput', 'label' => 'ألصق النص أو المستند المحتوي على إيميلات', 'type' => 'textarea', 'rows' => 7, 'default' => "تواصل مع فريق الدعم عبر support@example.com أو المبيعات sales@elta6ur.com لمزيد من التفاصيل.\nيمكنك أيضاً مراسلة info@example.com أو support@example.com.", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'removeDupEmails', 'label' => 'تصفية الإيميلات المكررة', 'type' => 'select', 'options' => ['yes' => 'نعم، إيميلات فريدة فقط', 'no' => 'لا'], 'default' => 'yes'],
        ],
        'calcJs' => "
            const text = document.getElementById('emailExtractInput').value;
            const rmDup = document.getElementById('removeDupEmails').value === 'yes';

            const emailRegex = /([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,})/gi;
            let matches = text.match(emailRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches.map(e => e.toLowerCase())));
            }

            const resultText = matches.join('\\n');

            setPrimaryResult(matches.length + ' بريد إلكتروني تم استخراجه', 'نتيجة الاستخراج');
            showResultArea();

            setDetailStats([
                { label: 'عدد الإيميلات المكتشفة', value: matches.length + ' إيميل', color: '#10b981' },
                { label: 'تصفية الحروف الكبيرة والمكرر', value: rmDup ? 'مفعلة (Lowercase)' : 'معطلة', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">قائمة عناوين البريد الإلكتروني المستخرجة:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"font-family:monospace;direction:ltr\" readonly>\${resultText}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تستخرج كافة عناوين البريد الإلكتروني الصالحة وتوفرها في قائمة مرتبة سطر لكل إيميل.',
            'تحول العناوين تلقائياً إلى حروف صغيرة (Lowercase) لتوحيد وتسهيل استيرادها في منصات التسويق البريدي.'
        ],
        'assumptions' => 'يفترض عناوين بريد إلكتروني قياسية تحتوي على علامة @ ونطاق صحيح.',
        'faqs' => [
            ['q' => 'هل يمكن تصديرها كملف جاهز للاستيراد في برامج الـ CRM؟', 'a' => 'نعم؛ انسخ القائمة مباشرة بضغطة زر والصقها في ملف Excel أو CSV للاستيراد الفوري في أدوات الـ Mailchimp وغيرها.']
        ],
        'related' => ['extract-urls-from-text', 'extract-hashtags-from-text', 'remove-duplicate-lines']
    ],

    'extract-hashtags-from-text' => [
        'title' => 'استخراج الهاشتاغات (#) من النص ومنشورات السوشيال ميديا',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'hashtagInputText', 'label' => 'ألصق المنشور أو النص المحتوي على وسوم', 'type' => 'textarea', 'rows' => 6, 'default' => "يسعدنا إطلاق منصة الأدوات الجديدة اليوم! #تقنية #برمجة_المواقع #السعودية #تطوير_الويب مع أحدث أدوات #الذكاء_الاصطناعي و #تقنية مكرر.", 'placeholder' => 'ضع المنشور هنا...'],
            ['id' => 'removeDuplicateTags', 'label' => 'إزالة الوسوم المكررة', 'type' => 'select', 'options' => ['yes' => 'نعم، وسوم فريدة فقط', 'no' => 'لا'], 'default' => 'yes'],
        ],
        'calcJs' => "
            const text = document.getElementById('hashtagInputText').value;
            const rmDup = document.getElementById('removeDuplicateTags').value === 'yes';

            // استخراج الهاشتاغات العربية والإنجليزية مع علامة الشرطة السفلية
            const tagRegex = /(#[\\u0600-\\u06FFa-zA-Z0-9_]+)/g;
            let matches = text.match(tagRegex) || [];

            if (rmDup) {
                matches = Array.from(new Set(matches));
            }

            const joined = matches.join(' ');

            setPrimaryResult(matches.length + ' هاشتاغ تم استخراجه', 'عدد الهاشتاغات المكتشفة');
            showResultArea();

            setDetailStats([
                { label: 'عدد الوسوم المستخرجة', value: matches.length + ' وسم', color: '#10b981' },
                { label: 'جاهز للنشر على', value: 'إكس، إنستغرام، لينكدإن، تيك توك', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">الهاشتاغات مفصولة بمسافات (جاهزة للنسخ في المنشور):</label>
                    <textarea class=\"form-control\" rows=\"4\" style=\"direction:rtl;line-height:1.8\" readonly>\${joined}</textarea>
                </div>
            `);
        ",
        'points' => [
            'تدعم استخراج الهاشتاغات العربية المشتملة على شرطة سفلية (_) والهاشتاغات الإنجليزية والأرقام.',
            'تجمع الوسوم في سطر واحد لتسهيل نسخها ولصقها في التعليق الأول أو نهاية المنشورات.'
        ],
        'assumptions' => 'الهاشتاغ يجب أن يبدأ برمز # ولا يحتوي على مسافات بداخله.',
        'faqs' => [
            ['q' => 'كم عدد الهاشتاغات الموصى به في منشورات إنستغرام وإكس؟', 'a' => 'التوصية الحالية لخوارزميات 2025 هي استخدام من 3 إلى 5 هاشتاغات مركزة وذات صلة وثيقة بموضوع المنشور، وتجنب حشو أكثر من 15 وسماً لتفادي اعتبار الحساب كـ Spam.']
        ],
        'related' => ['extract-keywords-from-text', 'extract-urls-from-text', 'social-media-pricing-calculator']
    ],

    'extract-keywords-from-text' => [
        'title' => 'استخراج الكلمات المفتاحية الأكثر تكراراً وأهمية',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'keywordsTextInput', 'label' => 'ألصق المقال لتحليل واستخراج كلماته المفتاحية', 'type' => 'textarea', 'rows' => 8, 'default' => "التجارة الإلكترونية تشهد نمواً هائلاً في العالم العربي. يتيح المتجر الإلكتروني للشركات الوصول إلى شريحة واسعة من العملاء. لتحقيق النجاح في التجارة الإلكترونية، يجب التركيز على تجربة المستخدم وسرعة الشحن وتوفير بوابات دفع آمنة. التسويق الرقمي هو ركيزة أساسية لنمو أي متجر إلكتروني وزيادة مبيعات التجارة الإلكترونية.", 'placeholder' => 'ضع المقال هنا...'],
            ['id' => 'topKeywordsCount', 'label' => 'عدد أهم الكلمات المطلوب إظهارها', 'type' => 'number', 'default' => '10', 'min' => '5', 'max' => '30', 'step' => '5'],
        ],
        'calcJs' => "
            const text = document.getElementById('keywordsTextInput').value.toLowerCase();
            const topN = Math.max(5, parseInt(document.getElementById('topKeywordsCount').value) || 10);

            // قائمة كلمات التوقف العربية الشائعة لاستبعادها (Stop Words)
            const stopWords = new Set([
                'في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'تم', 'كان', 'كانت',
                'أن', 'إن', 'أو', 'ثم', 'حيث', 'كل', 'هو', 'هي', 'التي', 'الذي', 'التي',
                'ما', 'لا', 'لم', 'لن', 'قد', 'بين', 'كما', 'ذلك', 'تلك', 'حتى', 'إذا',
                'غير', 'نحو', 'أي', 'فإن', 'ولكن', 'لكن', 'به', 'بها', 'له', 'لها', 'عند'
            ]);

            // تنظيف النص وتقطيعه لكلمات
            const rawWords = text
                .replace(/[^\\u0600-\\u06FFa-zA-Z0-9]/g, ' ')
                .split(/\\s+/)
                .filter(w => w.length > 2 && !stopWords.has(w));

            const freqMap = {};
            rawWords.forEach(w => { freqMap[w] = (freqMap[w] || 0) + 1; });

            const sorted = Object.entries(freqMap)
                .sort((a, b) => b[1] - a[1])
                .slice(0, topN);

            let tableHtml = '<div style=\"margin-top:1rem\"><table class=\"table\" style=\"width:100%\"><thead><tr><th>الكلمة المفتاحية</th><th>عدد مرات التكرار</th><th>الكثافة المئوية</th></tr></thead><tbody>';
            sorted.forEach(([word, count]) => {
                const density = ((count / Math.max(1, rawWords.length)) * 100).toFixed(1);
                tableHtml += `<tr><td><strong>\${word}</strong></td><td>\${count} مرات</td><td>\${density}%</td></tr>`;
            });
            tableHtml += '</tbody></table></div>';

            setPrimaryResult(sorted.length + ' كلمات مفتاحية رئيسية تم استخراجها', 'الكلمات المفتاحية البارزة');
            showResultArea();

            setDetailStats([
                { label: 'أكثر كلمة تكراراً في النص', value: sorted[0] ? sorted[0][0] + ' (' + sorted[0][1] + 'x)' : 'لا يوجد', color: '#10b981' },
                { label: 'إجمالي الكلمات المعنوية المفلترة', value: rawWords.length + ' كلمة', color: '#3b82f6' }
            ]);

            setResultContent(tableHtml);
        ",
        'points' => [
            'تستبعد الأداة تلقائياً حروف الجر وأسماء الإشارة والضمائر (Stop Words) للتركيز فقط على الكلمات ذات الدلالة المعنوية.',
            'تفيد في تحسين السيو (SEO) والتأكد من مطابقة المقال للكلمات المفتاحية المستهدفة.'
        ],
        'assumptions' => 'الكلمات الأقل من 3 أحرف تستبعد تلقائياً.',
        'faqs' => [
            ['q' => 'ما هي كثافة الكلمات المفتاحية المثالية للسيو؟', 'a' => 'النسبة الموصى بها لمحركات البحث مثل Google هي بين 1% إلى 2.5% للكلمة المفتاحية الرئيسية في المقال لتفادي حشو الكلمات (Keyword Stuffing).']
        ],
        'related' => ['keyword-density-calculator', 'arabic-word-counter', 'extract-hashtags-from-text']
    ],

    'keyword-density-calculator' => [
        'title' => 'حاسبة كثافة الكلمات المفتاحية (Keyword Density)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'densityTextInput', 'label' => 'نص المقال بالكامل', 'type' => 'textarea', 'rows' => 7, 'default' => "الطاقة الشمسية أصبحت الخيار الأول لتوليد الكهرباء النظيفة في العالم العربي. توفر منظومة الطاقة الشمسية استقلالاً كاملاً عن انقطاعات الشبكة، وتخفض فاتورة الكهرباء الشهرية بشكل ملموس. الاستثمار في الطاقة الشمسية يعود بأرباح مجزية على المدى الطويل.", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'targetKeywordInput', 'label' => 'الكلمة أو العبارة المفتاحية المستهدفة', 'type' => 'text', 'default' => 'الطاقة الشمسية', 'placeholder' => 'اكتب الكلمة المفتاحية المستهدفة...'],
        ],
        'calcJs' => "
            const text = document.getElementById('densityTextInput').value.toLowerCase();
            const keyword = document.getElementById('targetKeywordInput').value.trim().toLowerCase();

            if (!keyword) {
                alert('يرجى إدخال الكلمة المفتاحية المراد فحص كثافتها');
                return;
            }

            const totalWords = text.trim() ? text.trim().split(/\\s+/).length : 0;
            // حساب تكرار العبارة
            const regex = new RegExp(keyword.replace(/[-\\/\\\\^$*+?.()|[\\]{}]/g, '\\\\$&'), 'gi');
            const matches = text.match(regex) || [];
            const count = matches.length;

            const density = totalWords > 0 ? ((count / totalWords) * 100) : 0;

            let status = 'كثافة ممتازة ومثالية للسيو ✅ (1% - 2.5%)';
            let color = '#10b981';
            if (density < 0.8) { status = 'منخفضة جداً؛ يفضل ذكر الكلمة المفتاحية أكثر ⚠️'; color = '#3b82f6'; }
            else if (density > 3.0) { status = 'مرتفعة جداً! خطر حشو الكلمات (Keyword Stuffing) ❌'; color = '#ef4444'; }

            setPrimaryResult(density.toFixed(2) + '% (' + count + ' مرات تكرار)', 'كثافة الكلمة المفتاحية في المقال');
            showResultArea();

            setDetailStats([
                { label: 'عدد مرات ذكر الكلمة المستهدفة', value: count + ' مرات', color: '#10b981' },
                { label: 'إجمالي كلمات المقال الكلي', value: totalWords + ' كلمة', color: '#3b82f6' },
                { label: 'تقييم التوافق مع محركات البحث (SEO)', value: status, color: color },
                { label: 'النسبة المثالية الموصى بها', value: '1.0% إلى 2.5%', color: '#8b5cf6' }
            ]);

            setResultContent(`
                <p>تكررت عبارة <strong>\"\${keyword}\"</strong> بمعدل <strong>\${count} مرات</strong> في مقال من <strong>\${totalWords} كلمة</strong>، بكثافة مئوية <strong>\${density.toFixed(2)}%</strong> - \${status}.</p>
            `);
        ",
        'points' => [
            'كثافة الكلمة المفتاحية = (عدد مرات تكرار الكلمة ÷ إجمالي كلمات المقال) × 100.',
            'الحفاظ على الكثافة بين 1% إلى 2.5% يضمن لمحركات البحث فهم موضوع المقال بدقة دون التعرض لعقوبة الحشو الزائد.'
        ],
        'assumptions' => 'يفترض نص مقال كامل يتجاوز 100 كلمة للحصول على نسبة ذات دلالة إحصائية.',
        'faqs' => [
            ['q' => 'أين يجب توزيع الكلمة المفتاحية في المقال؟', 'a' => 'يُوصى بذكر الكلمة المفتاحية في عنوان المقال الرئيسي H1، وفي أول 100 كلمة من المقدمة، وفي أحد العناوين الفرعية H2، ومرة في الخاتمة بشكل طبيعي وسلس.']
        ],
        'related' => ['extract-keywords-from-text', 'arabic-word-counter', 'arabic-english-slug-generator']
    ],

    'arabic-english-slug-generator' => [
        'title' => 'توليد الـ Slug المخصص (عربي أو تعريب صوتي)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'titleToSlugInput', 'label' => 'عنوان المقال أو الصفحة', 'type' => 'text', 'default' => 'أفضل 10 نصائح لتصميم وبرمجة المواقع في 2026!', 'placeholder' => 'اكتب العنوان هنا...'],
            ['id' => 'slugLanguageMode', 'label' => 'صيغة الرابط المستهدفة', 'type' => 'select', 'options' => [
                'arabic' => 'رابط بالكلمات العربية الصافية (مثل: افضل-نصائح-تصميم-مواقع)',
                'latin_translit' => 'تعريب صوتي لاتيني للمبرمجين (Franco / Transliteration)',
                'english_simple' => 'إنجليزي بأحرف وأرقام مبسطة'
            ], 'default' => 'arabic'],
            ['id' => 'slugSeparator', 'label' => 'الفاصل بين الكلمات', 'type' => 'select', 'options' => [
                'dash' => 'شرطة عادية - (المعيار الأفضل للسيو)',
                'underscore' => 'شرطة سفلية _'
            ], 'default' => 'dash'],
        ],
        'calcJs' => "
            const title = document.getElementById('titleToSlugInput').value.trim();
            const mode = document.getElementById('slugLanguageMode').value;
            const sep = document.getElementById('slugSeparator').value === 'underscore' ? '_' : '-';

            let slug = '';
            if (mode === 'arabic') {
                slug = title
                    .replace(/[\\u064B-\\u065F\\u0670]/g, '') // إزالة التشكيل
                    .replace(/[أإآ]/g, 'ا')
                    .replace(/ة/g, 'ه')
                    .replace(/[^\\u0600-\\u06FFa-zA-Z0-9\\s]/g, '') // إزالة الرموز
                    .trim()
                    .replace(/\\s+/g, sep)
                    .toLowerCase();
            } else {
                // تعريب صوتي مبسط للأحرف العربية
                const map = {
                    'ا': 'a', 'أ': 'a', 'إ': 'e', 'آ': 'aa', 'ب': 'b', 'ت': 't', 'ث': 'th',
                    'ج': 'j', 'ح': 'h', 'خ': 'kh', 'د': 'd', 'ذ': 'th', 'ر': 'r', 'ز': 'z',
                    'س': 's', 'ش': 'sh', 'ص': 's', 'ض': 'd', 'ط': 't', 'ظ': 'z', 'ع': 'a',
                    'غ': 'gh', 'ف': 'f', 'ق': 'q', 'ك': 'k', 'ل': 'l', 'م': 'm', 'ن': 'n',
                    'ه': 'h', 'ة': 'a', 'و': 'w', 'ي': 'y', 'ى': 'a', 'ء': '', 'ئ': 'e', 'ؤ': 'o'
                };
                let converted = '';
                for (let ch of title) {
                    converted += map[ch] !== undefined ? map[ch] : ch;
                }
                slug = converted
                    .replace(/[^a-zA-Z0-9\\s]/g, '')
                    .trim()
                    .replace(/\\s+/g, sep)
                    .toLowerCase();
            }

            setPrimaryResult(slug, 'الرابط النظيف الدائم (Slug URL)');
            showResultArea();

            setDetailStats([
                { label: 'طول الرابط الناتج', value: slug.length + ' حرف', color: '#10b981' },
                { label: 'الفاصل المعتمد', value: sep === '-' ? 'شرطة -' : 'شرطة سفلية _', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">رابط المقال الدائم المقترح:</label>
                    <input type=\"text\" class=\"form-control\" style=\"font-family:monospace;direction:ltr\" value=\"https://example.com/blog/\${slug}\" readonly>
                </div>
            `);
        ",
        'points' => [
            'الروابط النظيفة (Friendly URLs) تحسن ظهور الموقع في محركات البحث وتسهل مشاركتها على وسائل التواصل.',
            'تفضل محركات البحث مثل Google استخدام الشرطة العادية (-) كفاصل بين الكلمات بدلاً من الشرطة السفلية (_).'
        ],
        'assumptions' => 'تُحذف علامات التعجب والاستفهام والأقواس والرموز التعبيرية تلقائياً.',
        'faqs' => [
            ['q' => 'أيهما أفضل للسيو: الرابط العربي أم الإنجليزي؟', 'a' => 'الروابط العربية ممتازة للمواقع الموجهة حصراً للمستخدم العربي وتزيد من نسبة النقر (CTR)، بينما الروابط الإنجليزية أو المعربة صوتياً تكون أسهل في النسخ والمشاركة عبر التطبيقات دون تشويه الرابط برموز النسبة المئوية %D8%A7.']
        ],
        'related' => ['slug-converter', 'keyword-density-calculator', 'clean-arabic-text']
    ],

    'quote-converter' => [
        'title' => 'تحويل علامات الاقتباس والأقواس',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'quoteTextInput', 'label' => 'أدخل النص المحتوي على اقتباسات', 'type' => 'textarea', 'rows' => 6, 'default' => 'قال الحكيم: "العلم في الصغر كالنقش على الحجر"، وأكد زميله: \'التكرار أم المهارات\'.', 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'targetQuoteStyle', 'label' => 'تحويل الاقتباسات إلى', 'type' => 'select', 'options' => [
                'arabic_guillemets' => 'أقواس الاقتباس العربية التقليدية « »',
                'curly_quotes' => 'علامات اقتباس منحنية فاخرة “ ”',
                'straight_double' => 'اقتباس مزدوج مستقيم " "',
                'single_quotes' => 'اقتباس مفرد \' \''
            ], 'default' => 'arabic_guillemets'],
        ],
        'calcJs' => "
            let text = document.getElementById('quoteTextInput').value;
            const style = document.getElementById('targetQuoteStyle').value;

            let openQ = '«';
            let closeQ = '»';
            if (style === 'curly_quotes') { openQ = '”'; closeQ = '“'; }
            if (style === 'straight_double') { openQ = '\"'; closeQ = '\"'; }
            if (style === 'single_quotes') { openQ = \"'\"; closeQ = \"'\"; }

            // استبدال الاقتباس المزدوج والمفرد والأقواس الفرنسية
            let converted = text.replace(/[\"“«](.*?)[\"”»]/g, openQ + '$1' + closeQ);
            converted = converted.replace(/['’](.*?)['’]/g, openQ + '$1' + closeQ);

            setPrimaryResult('تم تحويل علامات الاقتباس بنجاح', 'حالة الاقتباسات');
            showResultArea();

            setDetailStats([
                { label: 'النمط المعتمد الجديد', value: openQ + ' نص الاقتباس ' + closeQ, color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص بعد ضبط علامات الاقتباس:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:rtl;line-height:1.8\" readonly>\${converted}</textarea>
                </div>
            `);
        ",
        'points' => [
            'الأقواس المزدوجة الصغيرة « » (Guillemets) هي العلامة المعتمدة رسمياً في الطباعة والنشر العربي الكلاسيكي.',
            'توحيد علامات الاقتباس في الأبحاث والكتب يمنح العمل مظهراً أكاديمياً رصيناً واحترافياً.'
        ],
        'assumptions' => 'يفترض وجود أزواج اقتباس متطابقة (فتح وإغلاق).',
        'faqs' => [
            ['q' => 'كيف أكتب القوسين « » على لوحة المفاتيح؟', 'a' => 'على ويندوز باللغة العربية يمكنك كتابتها بالضغط على Shift + حرف الزاي وحرف الراء في بعض التوزيعات، أو استخدام هذه الأداة للتحويل التلقائي بنقرة زر.']
        ],
        'related' => ['clean-arabic-text', 'clean-word-text', 'arabic-english-numbers-converter']
    ],

    'arabic-english-numbers-converter' => [
        'title' => 'تحويل الأرقام العربية والإنجليزية (المشرقية والغربية)',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'numbersTextInput', 'label' => 'ألصق النص المحتوي على أرقام للتحويل', 'type' => 'textarea', 'rows' => 6, 'default' => 'تأسست الشركة في عام 2024 وحققت أرباحاً تجاوزت ١٥٠٠٠٠ دولار خلال أول ٦ أشهر من إطلاقها.', 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'targetNumberFormat', 'label' => 'تحويل كافة الأرقام إلى', 'type' => 'select', 'options' => [
                'to_english' => 'أرقام عربية غربية / إنجليزية (0, 1, 2, 3, 4, 5, 6, 7, 8, 9) - موصى به',
                'to_arabic' => 'أرقام مشرقية / هندية (٠، ١، ٢، ٣، ٤، ٥، ٦، ٧، ٨، ٩)'
            ], 'default' => 'to_english'],
        ],
        'calcJs' => "
            const text = document.getElementById('numbersTextInput').value;
            const target = document.getElementById('targetNumberFormat').value;

            const eastern = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
            const western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

            let result = '';
            if (target === 'to_english') {
                result = text;
                for (let i = 0; i < 10; i++) {
                    result = result.replace(new RegExp(eastern[i], 'g'), western[i]);
                }
            } else {
                result = text;
                for (let i = 0; i < 10; i++) {
                    result = result.replace(new RegExp(western[i], 'g'), eastern[i]);
                }
            }

            setPrimaryResult('تم توحيد الأرقام بنجاح في كامل النص', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'الصيغة المعتمدة الناتجة', value: target === 'to_english' ? 'أرقام غربية (0-9)' : 'أرقام مشرقية (٠-٩)', color: '#10b981' },
                { label: 'طول النص المعالج', value: result.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص بعد توحيد صيغة الأرقام:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:rtl;line-height:1.8\" readonly>\${result}</textarea>
                </div>
            `);
        ",
        'points' => [
            'الأرقام (0, 1, 2, 3, 4...) هي الأرقام العربية الغربية التي ابتكرها الخوارزمي ونقلها الغرب عن العرب وتستخدم رسمياً في دول المغرب العربي والجهات الحكومية بالمملكة.',
            'الأرقام (٠، ١، ٢، ٣...) هي الأرقام المشرقية ذات الأصل الهندي وتستخدم في مصر وبلاد الشام والعراق.'
        ],
        'assumptions' => 'يحافظ التحويل على الكلمات والرموز المحيطة بالأرقام دون أي تغيير.',
        'faqs' => [
            ['q' => 'أيهما أفضل استخدامه في المواقع الإلكترونية؟', 'a' => 'يُوصى دائماً باستخدام الأرقام الغربية (0-9) في المواقع والتطبيقات لأنها مدعومة في كافة المتصفحات ولا تسبب أخطاء في الحسابات البرمجية أو قواعد البيانات.']
        ],
        'related' => ['clean-arabic-text', 'clean-word-text', 'quote-converter']
    ],

    'rtl-ltr-text-converter' => [
        'title' => 'تحويل اتجاه النص بين RTL و LTR وعلامات التوجيه',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'directionTextInput', 'label' => 'ألصق النص المطلوب تغيير اتجاهه', 'type' => 'textarea', 'rows' => 6, 'default' => "const title = 'منصة أدوات المطورين';\n// مرحباً بكم في الكود العربي", 'placeholder' => 'ضع النص هنا...'],
            ['id' => 'targetDirectionOption', 'label' => 'الاتجاه المطلوب', 'type' => 'select', 'options' => [
                'rtl' => 'اتجاه من اليمين لليسار (RTL - مناسب للعربية)',
                'ltr' => 'اتجاه من اليسار لليمين (LTR - مناسب للأكواد والإنجليزية)'
            ], 'default' => 'ltr'],
        ],
        'calcJs' => "
            const text = document.getElementById('directionTextInput').value;
            const dir = document.getElementById('targetDirectionOption').value;

            setPrimaryResult('تم تغيير اتجاه العرض إلى ' + dir.toUpperCase(), 'اتجاه النص');
            showResultArea();

            setDetailStats([
                { label: 'الاتجاه المعتمد', value: dir === 'rtl' ? 'Right-To-Left (يمين)' : 'Left-To-Right (يسار)', color: '#10b981' },
                { label: 'عدد الأسطر', value: text.split(/\\n/).length + ' أسطر', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص بالاتجاه المحدد:</label>
                    <textarea class=\"form-control\" rows=\"6\" style=\"direction:\${dir};text-align:\${dir === 'rtl' ? 'right' : 'left'};font-family:monospace;line-height:1.8\">\${text}</textarea>
                </div>
            `);
        ",
        'points' => [
            'مفيد للمبرمجين عند كتابة تعليقات عربية داخل الأكواد البرمجية (Inline Comments) لمنع ارتباك ترتيب الأقواس.',
            'يساعد في تصحيح النصوص ثنائية اللغة (Bi-directional Text) التي تحتوي على مصطلحات لاتينية مدمجة.'
        ],
        'assumptions' => 'يغير خاصية direction و text-align البرمجية.',
        'faqs' => [
            ['q' => 'لماذا تنقلب علامات الترقيم والأقواس في بعض النصوص العربية؟', 'a' => 'يحدث هذا عندما يكون اتجاه الصندوق الحاوي (Container) مضبوطاً على LTR، فتعتبر خوارزمية اليونيكود أن القوس يتبع سياقاً لاتينياً فتقلبه، ويتم حل ذلك فوراً بضبط الاتجاه على RTL.']
        ],
        'related' => ['clean-arabic-text', 'clean-word-text', 'text-formatter']
    ],

    'clean-word-text' => [
        'title' => 'تنظيف النص المنسوخ من Word وبرامج المايكروسوفت',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'wordTextInput', 'label' => 'ألصق النص المنسوخ من مستند Word هنا', 'type' => 'textarea', 'rows' => 8, 'default' => "هذا نص منسوخ من ملف Word يحتوي على رموز تحكم خفية مثل:\n- خطوط منكسرة\n- واقتباسات مايكروسوفت “المنحنية”\n- ومسافات ناعمة &nbsp; وعلامات فقرات غريبة.", 'placeholder' => 'ضع النص المنسوخ من وورد هنا...'],
        ],
        'calcJs' => "
            let text = document.getElementById('wordTextInput').value;

            let cleaned = text
                .replace(/[\\u200B-\\u200D\\uFEFF]/g, '') // إزالة المسافات الصفرية الخفية Zero-Width
                .replace(/[\\u2018\\u2019]/g, \"'\") // توحيد الاقتباس المفرد
                .replace(/[\\u201C\\u201D]/g, '\"') // توحيد الاقتباس المزدوج
                .replace(/\\u2013/g, '-') // توحيد En-dash
                .replace(/\\u2014/g, '--') // توحيد Em-dash
                .replace(/\\u2026/g, '...') // توحيد علامة الحذف
                .replace(/&nbsp;/g, ' ')
                .replace(/\\r\\n/g, '\\n')
                .replace(/\\r/g, '\\n')
                .replace(/[ ]{2,}/g, ' ')
                .trim();

            const saved = text.length - cleaned.length;

            setPrimaryResult('تم تنظيف ' + saved + ' رمز تحكم خفي من مستند Word', 'حالة التنظيف');
            showResultArea();

            setDetailStats([
                { label: 'الرموز والمحارف الخفية المزالة', value: saved + ' رمز', color: '#10b981' },
                { label: 'عدد الأحرف الصافية', value: cleaned.length + ' حرف', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص النظيف الخالي من أخطاء وتشويهات مايكروسوفت وورد:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:rtl;line-height:1.8\" readonly>\${cleaned}</textarea>
                </div>
            `);
        ",
        'points' => [
            'برنامج Microsoft Word يضيف رموز تحكم وتنسيقات خفية (مثل مسافات الصفر Zero-Width Spaces) تتسبب في انهيار البرمجيات وتشوه قواعد البيانات.',
            'هذه الأداة تجرد النص من كافة شفرات وورد المشوهة وتبقيه نصاً برمجياً نقياً UTF-8.'
        ],
        'assumptions' => 'يحافظ على الفقرات والكلمات الأصلية كما هي.',
        'faqs' => [
            ['q' => 'لماذا تظهر مربعات غريبة أحياناً عند نسخ نصوص وورد إلى المواقع؟', 'a' => 'بسبب وجود محارف خاصة غير مرئية بخط وورد لا يتعرف عليها متصفح الويب، وتعمل هذه الأداة على تنظيفها بالكامل.']
        ],
        'related' => ['clean-html-text', 'clean-arabic-text', 'remove-extra-spaces']
    ],

    'clean-html-text' => [
        'title' => 'تنظيف النص المنسوخ من HTML واستخراج النص الصافي',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'htmlCleanInput', 'label' => 'ألصق كود أو صفحة HTML المراد استخراج النص النظيف منها', 'type' => 'textarea', 'rows' => 8, 'default' => "<div class=\"article-content\">\n  <h2>عنوان المقال الهام</h2>\n  <p>هذا نص <strong>مهم جداً</strong> داخل المقال، ويمكنك الضغط على <a href=\"#\">هذا الرابط</a> للمزيد.</p>\n  <span style=\"color:red\">ملاحظة ختامية مميزة</span>\n</div>", 'placeholder' => 'ضع كود HTML هنا...'],
            ['id' => 'keepLineBreaks', 'label' => 'الحفاظ على فواصل الفقرات والأسطر', 'type' => 'select', 'options' => ['yes' => 'نعم، أسطر جديدة لكل فقرة', 'no' => 'لا، دمج كامل في سطر واحد'], 'default' => 'yes'],
        ],
        'calcJs' => "
            const raw = document.getElementById('htmlCleanInput').value;
            const keepLines = document.getElementById('keepLineBreaks').value === 'yes';

            let cleaned = raw
                .replace(/<script[^>]*>[\\s\\S]*?<\\/script>/gi, '') // إزالة الجافاسكريبت
                .replace(/<style[^>]*>[\\s\\S]*?<\\/style>/gi, '') // إزالة أكواد CSS
                .replace(/<br\\s*\\/?>/gi, '\\n')
                .replace(/<\\/p>|<\\/div>|<\\/h[1-6]>|<\\/li>/gi, '\\n\\n')
                .replace(/<[^>]+>/g, '') // إزالة كافة وسوم HTML
                .replace(/&nbsp;/g, ' ')
                .replace(/&amp;/g, '&')
                .replace(/&lt;/g, '<')
                .replace(/&gt;/g, '>')
                .replace(/&quot;/g, '\"')
                .replace(/&#39;/g, \"'\");

            if (!keepLines) {
                cleaned = cleaned.replace(/\\s+/g, ' ');
            } else {
                cleaned = cleaned.replace(/\\n{3,}/g, '\\n\\n');
            }
            cleaned = cleaned.trim();

            setPrimaryResult('تم تجريد النص من وسوم HTML بنجاح', 'استخراج النص الصافي');
            showResultArea();

            setDetailStats([
                { label: 'طول كود HTML الأصلي', value: raw.length + ' حرف', color: '#ef4444' },
                { label: 'طول النص الصافي بعد التنظيف', value: cleaned.length + ' حرف', color: '#10b981' },
                { label: 'نسبة تنظيف الأكواد الزائدة', value: raw.length > 0 ? (((raw.length - cleaned.length)/raw.length)*100).toFixed(0) + '%' : '0%', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص الصافي المستخرج بدون وسوم HTML:</label>
                    <textarea class=\"form-control\" rows=\"7\" style=\"direction:rtl;line-height:1.8\" readonly>\${cleaned}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يحذف وسوم التنسيق والـ HTML وسكريبتات الجافاسكريبت وتنسيقات الـ CSS بالكامل.',
            'يستبدل رموز الـ HTML Entities (مثل &amp; و &nbsp;) بمقابلاتها النصية الحقيقية.'
        ],
        'assumptions' => 'يحافظ على المحتوى النصي المعروض للمستخدم.',
        'faqs' => [
            ['q' => 'هل يحذف محتوى سكربتات الإعلانات والـ CSS؟', 'a' => 'نعم؛ يتم التخلص تماماً من نصوص وسكريبتات الـ style والـ script ولا تظهر في النص المستخرج.']
        ],
        'related' => ['clean-word-text', 'html-to-markdown', 'clean-arabic-text']
    ],

    'text-to-json-converter' => [
        'title' => 'تحويل النص والقوائم إلى كائن JSON مهيكل',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'rawTextToJsonInput', 'label' => 'ألصق الأسطر أو البيانات المراد تحويلها لـ JSON', 'type' => 'textarea', 'rows' => 7, 'default' => "الرياض\nدبي\nالقاهرة\nعمان\nالدوحة\nالكويت", 'placeholder' => 'ضع النص هنا (سطر لكل عنصر أو صيغة المفتاح: القيمة)...'],
            ['id' => 'jsonOutputStructure', 'label' => 'طبيعة هيكل الـ JSON المطلوب', 'type' => 'select', 'options' => [
                'array_strings' => 'مصفوفة نصوص بسيطة ["نص1", "نص2"]',
                'array_objects' => 'مصفوفة كائنات [{ "id": 1, "value": "نص" }]',
                'key_value' => 'كائن مفتاح وقيمة (إذا كانت الأسطر بصيغة مفتاح: قيمة)'
            ], 'default' => 'array_strings'],
        ],
        'calcJs' => "
            const text = document.getElementById('rawTextToJsonInput').value;
            const struct = document.getElementById('jsonOutputStructure').value;

            const lines = text.split(/\\n/).map(l => l.trim()).filter(l => l.length > 0);
            let jsonObj;

            if (struct === 'array_strings') {
                jsonObj = lines;
            } else if (struct === 'array_objects') {
                jsonObj = lines.map((val, idx) => ({ id: idx + 1, name: val }));
            } else {
                jsonObj = {};
                lines.forEach(l => {
                    const parts = l.split(/[:=]/);
                    if (parts.length >= 2) {
                        jsonObj[parts[0].trim()] = parts.slice(1).join(':').trim();
                    } else {
                        jsonObj[l] = '';
                    }
                });
            }

            const jsonStr = JSON.stringify(jsonObj, null, 2);

            setPrimaryResult('تم توليد JSON بنجاح (' + lines.length + ' عناصر)', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المحولة', value: lines.length + ' عنصر', color: '#10b981' },
                { label: 'حجم ملف JSON الناتج', value: jsonStr.length + ' بايت', color: '#3b82f6' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">كود JSON المنسق الجاهز للنسخ:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"font-family:monospace;direction:ltr\" readonly>\${jsonStr}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يحول القوائم النصية العادية إلى مصفوفات JSON برمجية متوافقة مع لغات JavaScript و Python و PHP.',
            'يدعم استخراج الكائنات Key-Value إذا كانت الأسطر مفصولة بنقطتين رأسيتين (:).'
        ],
        'assumptions' => 'يفترض نصوصاً سليمة خالية من علامات التحكم غير المعرفة.',
        'faqs' => [
            ['q' => 'كيف أحول قائمة أسماء لـ JSON بصيغة id و name؟', 'a' => 'اختر خيار مصفوفة كائنات (Array of Objects) وستقوم الأداة تلقائياً بترقيم كل عنصر برقم id متسلسل.']
        ],
        'related' => ['json-to-formatted-text', 'json-formatter', 'json-validator']
    ],

    'json-to-formatted-text' => [
        'title' => 'تحويل كود JSON إلى نص منسق وقابل للقراءة',
        'isFinancial' => false,
        'inputs' => [
            ['id' => 'jsonSourceInput', 'label' => 'ألصق كود الـ JSON هنا', 'type' => 'textarea', 'rows' => 8, 'default' => "[\n  {\"id\": 1, \"name\": \"الرياض\", \"country\": \"السعودية\"},\n  {\"id\": 2, \"name\": \"دبي\", \"country\": \"الإمارات\"},\n  {\"id\": 3, \"name\": \"القاهرة\", \"country\": \"مصر\"}\n]", 'placeholder' => 'ضع كود JSON هنا...'],
            ['id' => 'textFormatModeJson', 'label' => 'طريقة عرض النص الناتج', 'type' => 'select', 'options' => [
                'bullet_list' => 'قائمة نقطية منسقة بوضوح',
                'table_csv' => 'جدول نصي / أسطر مفصولة بفواصل CSV'
            ], 'default' => 'bullet_list'],
        ],
        'calcJs' => "
            const jsonText = document.getElementById('jsonSourceInput').value;
            const mode = document.getElementById('textFormatModeJson').value;

            let parsed;
            try {
                parsed = JSON.parse(jsonText);
            } catch (e) {
                alert('خطأ في صيغة الـ JSON: ' + e.message);
                return;
            }

            let output = '';
            if (Array.isArray(parsed)) {
                if (mode === 'bullet_list') {
                    output = parsed.map((item, idx) => {
                        if (typeof item === 'object' && item !== null) {
                            const details = Object.entries(item).map(([k, v]) => k + ': ' + v).join(' | ');
                            return (idx + 1) + '. ' + details;
                        }
                        return '• ' + item;
                    }).join('\\n');
                } else {
                    if (parsed.length > 0 && typeof parsed[0] === 'object') {
                        const headers = Object.keys(parsed[0]);
                        output = headers.join(', ') + '\\n' + parsed.map(row => headers.map(h => row[h] || '').join(', ')).join('\\n');
                    } else {
                        output = parsed.join(', ');
                    }
                }
            } else if (typeof parsed === 'object' && parsed !== null) {
                output = Object.entries(parsed).map(([k, v]) => '• ' + k + ': ' + (typeof v === 'object' ? JSON.stringify(v) : v)).join('\\n');
            } else {
                output = String(parsed);
            }

            setPrimaryResult('تم تحويل كود JSON إلى نص مقروء', 'حالة التحويل');
            showResultArea();

            setDetailStats([
                { label: 'عدد العناصر المعالجة', value: (Array.isArray(parsed) ? parsed.length : Object.keys(parsed).length) + ' عنصر', color: '#10b981' }
            ]);

            setResultContent(`
                <div style=\"margin-top:1rem\">
                    <label class=\"form-label\">النص المقروء الناتج:</label>
                    <textarea class=\"form-control\" rows=\"8\" style=\"direction:rtl;line-height:1.8\" readonly>\${output}</textarea>
                </div>
            `);
        ",
        'points' => [
            'يحول هياكل JSON المعقدة إلى نصوص وجداول مبسطة يفهمها المستخدم العادي وغير المبرمج.',
            'يدعم المصفوفات والكائنات المتداخلة وقوائم البيانات.'
        ],
        'assumptions' => 'يفترض JSON صالح البنية والصياغة.',
        'faqs' => [
            ['q' => 'ماذا يحدث إذا كان الـ JSON يحتوي على خطأ كتابي؟', 'a' => 'ستعرض الأداة رسالة تنبيه توضح سطر ونوع الخطأ النحوي في ملف الـ JSON لتصحيحه.']
        ],
        'related' => ['text-to-json-converter', 'json-formatter', 'json-validator']
    ],
];

echo "Generating Group G: Text & Content Tools (31 tools)...\n";
foreach ($toolsG as $slug => $def) {
    generateToolFile($slug, $def, $outputDir);
}
echo "Completed Group G!\n";
