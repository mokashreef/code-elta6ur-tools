<?php
/**
 * أداة: محرر Markdown عربي احترافي
 * Code Elta6ur Tools Platform - Unified Architecture
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../includes/tools_registry.php';
require_once __DIR__ . '/../includes/tool_layout.php';

// تحميل بيانات الأداة من السجل
$slug = 'markdown-arabic-editor';
$tool = getToolBySlug($slug);
if (!$tool) {
    redirect('index.php');
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container tool-container">
    <?php renderToolHeader($tool); ?>


            <div class="card" style="margin-bottom:1.5rem">
                <div class="d-flex gap-1 mb-1" style="flex-wrap:wrap">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('# ', '')"><i class="fas fa-heading"></i> عنوان 1</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('## ', '')"><i class="fas fa-heading"></i> عنوان 2</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('**', '**')"><i class="fas fa-bold"></i> عريض</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('*', '*')"><i class="fas fa-italic"></i> مائل</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('> ', '')"><i class="fas fa-quote-right"></i> اقتباس</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('- ', '')"><i class="fas fa-list-ul"></i> قائمة</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('1. ', '')"><i class="fas fa-list-ol"></i> مرقمة</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('`', '`')"><i class="fas fa-code"></i> كود</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="insertMd('[نص الرابط](', 'https://example.com)')"><i class="fas fa-link"></i> رابط</button>
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
        

    <!-- قسم الشرح والمعادلات -->
    <?php 
    renderToolExplanation(
        'طريقة الحساب والمعادلات المستخدمة',
        array (
  0 => 'لغة Markdown تتيح لك كتابة نصوص غنية التنسيق باستخدام علامات نصية بسيطة وسهلة الحفظ.',
  1 => 'المحرر يدعم تلقائياً اتجاه RTL المناسب للكتابة العربية، ويحفظ مسوداتك في المتصفح تلقائياً.',
),
        'المعاينة الفورية تعتمد محرك تحليل خفيف وسريع متوافق مع GitHub Flavored Markdown.'
    ); 
    ?>

    <!-- الأسئلة الشائعة -->
    <?php renderToolFAQ(array (
  0 => 
  array (
    'q' => 'كيف أكتب عنواناً بالماركداون؟',
    'a' => 'أضف علامة الشباك # قبل النص متبوعة بمسافة؛ # للعنوان الرئيسي و ## للعنوان الفرعي.',
  ),
)); ?>

    <!-- أدوات ذات صلة -->
    <?php renderRelatedTools(array (
  0 => 'markdown-to-html',
  1 => 'html-to-markdown',
  2 => 'word-counter',
)); ?>
</div>

<?php renderToolScriptHelpers(); ?>


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
                const tableTpl = "\n| العمود 1 | العمود 2 | العمود 3 |\n| :--- | :---: | ---: |\n| قيمة 1 | قيمة 2 | قيمة 3 |\n| بيانات أ | بيانات ب | بيانات ج |\n";
                insertMd(tableTpl, "");
            }

            function loadSampleMd() {
                document.getElementById("markdownInput").value = "# منصة أدوات المطورين\n\nأهلاً بك في **المحرر العربي المتخصص** لكتابة وتنسيق مستندات الماركداون.\n\n## المميزات الرئيسية:\n- دعم كامل للغة العربية واتجاه **RTL**\n- معاينة حية سريعة وفورية\n- إمكانية التصدير كملف `.md` أو كود `HTML`\n\n> إن المعرفة قوة، وتوثيق الأفكار هو أول خطوة لتحقيقها.\n\n```javascript\nconsole.log('مرحباً بالعالم!');\n```\n";
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
        

<?php include __DIR__ . '/../includes/footer.php'; ?>