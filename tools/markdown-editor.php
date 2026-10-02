<?php
/**
 * أداة: محرر Markdown مع معاينة مباشرة وتصدير HTML
 * Code Elta6ur Tools
 */
include __DIR__ . '/../includes/header.php';
renderToolHeader($tool);
?>

<div class="card p-0" style="overflow:hidden">
    <!-- شريط الأدوات العلوي -->
    <div class="md-toolbar">
        <div class="md-toolbar-actions">
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('**','**')" title="نص عريض"><i class="fas fa-bold"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('*','*')" title="نص مائل"><i class="fas fa-italic"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('~~','~~')" title="يتوسطه خط"><i class="fas fa-strikethrough"></i></button>
            <span class="toolbar-sep"></span>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('# ','')" title="عنوان رئيسي">H1</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('## ','')" title="عنوان فرعي">H2</button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('### ','')" title="عنوان ثالث">H3</button>
            <span class="toolbar-sep"></span>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('> ','')" title="اقتباس"><i class="fas fa-quote-right"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('`','`')" title="كود سطري"><i class="fas fa-code"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('```\n','\n```')" title="كتلة كود"><i class="fas fa-file-code"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('- ','')" title="قائمة نقطية"><i class="fas fa-list-ul"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('1. ','')" title="قائمة رقمية"><i class="fas fa-list-ol"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('- [ ] ','')" title="قائمة مهام"><i class="fas fa-check-square"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertTable()" title="إدراج جدول"><i class="fas fa-table"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('[عنوان الرابط](',')')" title="رابط"><i class="fas fa-link"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('![وصف الصورة](',')')" title="صورة"><i class="fas fa-image"></i></button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="insertMD('\n---\n','')" title="خط فاصل"><i class="fas fa-minus"></i></button>
        </div>
        <div class="md-view-toggle">
            <button type="button" class="tab active" id="btnViewSplit" onclick="setViewMode('split')">شاشة مزدوجة</button>
            <button type="button" class="tab" id="btnViewEditor" onclick="setViewMode('editor')">المحرر فقط</button>
            <button type="button" class="tab" id="btnViewPreview" onclick="setViewMode('preview')">المعاينة فقط</button>
        </div>
    </div>

    <!-- شبكة المحرر والمعاينة -->
    <div class="md-container" id="mdContainer">
        <!-- قسم المحرر -->
        <div class="md-editor-pane" id="editorPane">
            <div class="pane-header">
                <span><i class="fas fa-code text-accent"></i> محرر Markdown</span>
                <span id="mdStats" style="font-size:0.75rem;color:var(--text-muted)">0 كلمة | 0 حرف</span>
            </div>
            <textarea id="mdInput" class="md-textarea" oninput="renderMD(); saveLocalMD();" placeholder="# مرحباً بك في محرر Markdown العربي..."></textarea>
        </div>

        <!-- قسم المعاينة -->
        <div class="md-preview-pane" id="previewPane">
            <div class="pane-header">
                <span><i class="fas fa-eye text-accent"></i> المعاينة المباشرة (Live Preview)</span>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-ghost btn-sm" onclick="copyMD()"><i class="fas fa-copy"></i> نسخ MD</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="copyHTML()"><i class="fas fa-code"></i> نسخ HTML</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="downloadMD()"><i class="fas fa-download"></i> .md</button>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="downloadHTML()"><i class="fas fa-file-code"></i> .html</button>
                </div>
            </div>
            <div id="mdPreview" class="md-preview-content"></div>
        </div>
    </div>
</div>

<style>
.md-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--bg-card);
    border-bottom: 1px solid var(--border-color);
    padding: 0.5rem 0.85rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.md-toolbar-actions {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    flex-wrap: wrap;
}
.toolbar-sep {
    display: inline-block;
    width: 1px;
    height: 18px;
    background: var(--border-color);
    margin: 0 4px;
}
.md-view-toggle {
    display: flex;
    gap: 0.25rem;
}
.md-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 540px;
    background: var(--bg-input);
}
.md-container.view-editor {
    grid-template-columns: 1fr !important;
}
.md-container.view-editor .md-preview-pane {
    display: none;
}
.md-container.view-preview {
    grid-template-columns: 1fr !important;
}
.md-container.view-preview .md-editor-pane {
    display: none;
}
.md-editor-pane, .md-preview-pane {
    display: flex;
    flex-direction: column;
    overflow: hidden;
}
.md-editor-pane {
    border-left: 1px solid var(--border-color);
}
.pane-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.65rem 1rem;
    background: rgba(255, 255, 255, 0.02);
    border-bottom: 1px solid var(--border-color);
    font-size: 0.85rem;
    font-weight: 600;
}
.md-textarea {
    flex: 1;
    width: 100%;
    border: none;
    outline: none;
    background: transparent;
    color: var(--text-primary);
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.95rem;
    line-height: 1.7;
    padding: 1rem;
    resize: none;
    direction: rtl;
    min-height: 480px;
}
.md-preview-content {
    flex: 1;
    padding: 1.25rem;
    overflow-y: auto;
    line-height: 1.8;
    color: var(--text-primary);
    direction: rtl;
    background: var(--bg-card);
}
.md-preview-content h1, .md-preview-content h2, .md-preview-content h3 {
    margin: 1.25rem 0 0.5rem;
    color: var(--text-primary);
}
.md-preview-content h1 {
    font-size: 1.65rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 0.5rem;
    color: var(--text-accent-light);
}
.md-preview-content h2 { font-size: 1.35rem; }
.md-preview-content h3 { font-size: 1.15rem; }
.md-preview-content p { margin: 0.65rem 0; }
.md-preview-content ul, .md-preview-content ol { padding-right: 1.5rem; margin: 0.65rem 0; }
.md-preview-content li { margin: 0.35rem 0; }
.md-preview-content code {
    background: rgba(108, 99, 255, 0.15);
    color: var(--text-accent-light);
    padding: 0.15rem 0.4rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.85rem;
}
.md-preview-content pre {
    background: #06080f;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius-sm);
    padding: 1rem;
    overflow-x: auto;
    margin: 0.85rem 0;
    direction: ltr;
    text-align: left;
}
.md-preview-content pre code {
    background: none;
    padding: 0;
    color: #e2e8f0;
}
.md-preview-content blockquote {
    border-right: 4px solid var(--text-accent);
    background: rgba(108, 99, 255, 0.05);
    padding: 0.75rem 1.25rem;
    margin: 1rem 0;
    border-radius: 0 8px 8px 0;
    color: var(--text-secondary);
}
.md-preview-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}
.md-preview-content th, .md-preview-content td {
    border: 1px solid var(--border-color);
    padding: 0.6rem 0.85rem;
    text-align: right;
}
.md-preview-content th {
    background: rgba(108, 99, 255, 0.12);
    font-weight: 700;
}
.md-preview-content hr {
    border: none;
    border-top: 1px solid var(--border-color);
    margin: 1.5rem 0;
}
.md-preview-content a {
    color: var(--text-accent-light);
    text-decoration: underline;
}
.md-preview-content img {
    max-width: 100%;
    border-radius: 8px;
    margin: 0.5rem 0;
}
@media (max-width: 768px) {
    .md-container {
        grid-template-columns: 1fr;
    }
    .md-editor-pane {
        border-left: none;
        border-bottom: 1px solid var(--border-color);
    }
}
</style>

<?php 
renderToolExplanation('دليل كتابة Markdown للمحتوى العربي', [
    'العناوين: استخدم # للعنوان الأول، ## للعنوان الثاني، ### للعنوان الثالث.',
    'التنسيق: **نص عريض**، *نص مائل*، ~~نص يتوسطه خط~~.',
    'القوائم: استخدم - لعنصر نقطي، أو 1. لعنصر رقمي، أو - [ ] لقائمة مهام.',
    'الأكواد: استخدم `كود` للكود السطري، أو ثلاثة باك-تكس (```) لكتل الأكواد البرمجية.',
    'الجداول: استخدم الشرطات والخطوط العمودية | لإنشاء جداول احترافية.'
], 'يتم حفظ محتوى مسودتك تلقائياً في المتصفح، حتى لا تفقد عملك في حال قمت بتحديث الصفحة.');

renderToolFAQ([
    ['q' => 'هل يمكن تصدير المحتوى كملف HTML جاهز للنشر؟', 'a' => 'نعم، زر ".html" في شريط المعاينة يتيح لك تحميل صفحة ويب كاملة متوافقة وجاهزة للنشر فوراً.'],
    ['q' => 'هل يدعم المحرر الجداول والصور؟', 'a' => 'نعم، يدعم المحرر الجداول الكاملة وروابط الصور والصيغ القياسية لـ GitHub Flavored Markdown.']
]);

renderRelatedTools($tool['related'] ?? ['markdown-to-html', 'html-to-markdown', 'word-counter']);
renderToolScriptHelpers();
?>

<script>
const DEFAULT_MD = `# مرحباً بك في محرر Markdown العربي 👋

هذا **نص عريض**، وهذا *نص مائل*، وهذا ~~نص مشطوب~~.

## مميزات هذا المحرر
- 🚀 معاينة فورية سريعة ومتجاوبة
- 📱 متوافق بالكامل مع الهواتف الذكية والأجهزة اللوحية
- 📋 تصدير مباشر إلى كود HTML نظيف أو ملف .md
- 💾 حفظ تلقائي للمسودات في المتصفح

### كود برمجي
\`\`\`javascript
function sayHello(name) {
    console.log("أهلاً يا " + name + "!");
}
sayHello("صديقي");
\`\`\`

> "القوة في البساطة... والإنتاجية في التركيز."

### جدول البيانات
| البند | الكمية | الحالة |
| :--- | :---: | :--- |
| التصميم | 1 | مكتمل ✅ |
| البرمجة | 1 | جاري العمل ⏳ |

[زيارة منصة كود التطور](https://code-elta6ur.sy)`;

function initMD() {
    const saved = localStorage.getItem('elta6ur_saved_md');
    const ta = document.getElementById('mdInput');
    ta.value = saved !== null ? saved : DEFAULT_MD;
    renderMD();

    // استجابة للشاشات الصغيرة
    if (window.innerWidth < 768) {
        setViewMode('editor');
    }
}

function saveLocalMD() {
    const val = document.getElementById('mdInput').value;
    try {
        localStorage.setItem('elta6ur_saved_md', val);
    } catch(e) {}
}

function insertMD(before, after) {
    const ta = document.getElementById('mdInput');
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const text = ta.value;
    const selected = text.substring(start, end) || 'نص';
    ta.value = text.substring(0, start) + before + selected + after + text.substring(end);
    ta.focus();
    ta.selectionStart = start + before.length;
    ta.selectionEnd = start + before.length + selected.length;
    renderMD();
    saveLocalMD();
}

function insertTable() {
    const tableTemplate = `\n| العمود 1 | العمود 2 | العمود 3 |\n| :--- | :---: | ---: |\n| قيمة 1 | قيمة 2 | قيمة 3 |\n| قيمة 4 | قيمة 5 | قيمة 6 |\n`;
    insertMD(tableTemplate, '');
}

function setViewMode(mode) {
    const container = document.getElementById('mdContainer');
    document.getElementById('btnViewSplit').classList.toggle('active', mode === 'split');
    document.getElementById('btnViewEditor').classList.toggle('active', mode === 'editor');
    document.getElementById('btnViewPreview').classList.toggle('active', mode === 'preview');

    container.className = 'md-container' + (mode === 'editor' ? ' view-editor' : (mode === 'preview' ? ' view-preview' : ''));
}

function parseMarkdown(md) {
    let html = md;

    // أكواد متعددة الأسطر ```
    const codeBlocks = [];
    html = html.replace(/```([a-zA-Z0-9_-]*)\n([\s\S]*?)```/g, function(match, lang, code) {
        const placeholder = `__CODE_BLOCK_${codeBlocks.length}__`;
        codeBlocks.push(`<pre><code class="language-${lang}">${escapeHtml(code.trim())}</code></pre>`);
        return placeholder;
    });

    // كود سطري `
    const inlineCodes = [];
    html = html.replace(/`([^`]+)`/g, function(match, code) {
        const placeholder = `__INLINE_CODE_${inlineCodes.length}__`;
        inlineCodes.push(`<code>${escapeHtml(code)}</code>`);
        return placeholder;
    });

    // جداول Markdown
    html = html.replace(/^\|(.+)\|\n\|([: -|]+)\|\n((?:\|.*\|\n?)*)/gm, function(match, headers, align, rows) {
        const ths = headers.split('|').filter(h => h.trim() !== '').map(h => `<th>${h.trim()}</th>`).join('');
        const rowLines = rows.trim().split('\n');
        const trs = rowLines.map(r => {
            const cells = r.split('|').filter(c => c.trim() !== '').map(c => `<td>${c.trim()}</td>`).join('');
            return `<tr>${cells}</tr>`;
        }).join('');
        return `<table><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table>`;
    });

    // عناوين
    html = html.replace(/^### (.*$)/gim, '<h3>$1</h3>');
    html = html.replace(/^## (.*$)/gim, '<h2>$1</h2>');
    html = html.replace(/^# (.*$)/gim, '<h1>$1</h1>');

    // نصوص
    html = html.replace(/\*\*(.*?)\*\*/gim, '<strong>$1</strong>');
    html = html.replace(/\*(.*?)\*/gim, '<em>$1</em>');
    html = html.replace(/~~(.*?)~~/gim, '<del>$1</del>');

    // اقتباس
    html = html.replace(/^\> (.*$)/gim, '<blockquote>$1</blockquote>');

    // خط فاصل
    html = html.replace(/^---$/gim, '<hr>');

    // صور وروابط
    html = html.replace(/!\[([^\]]*)\]\(([^)]+)\)/gim, '<img alt="$1" src="$2">');
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/gim, '<a href="$2" target="_blank" rel="noopener">$1</a>');

    // مهام وقوائم
    html = html.replace(/^- \[x\] (.*$)/gim, '<li><input type="checkbox" checked disabled> $1</li>');
    html = html.replace(/^- \[ \] (.*$)/gim, '<li><input type="checkbox" disabled> $1</li>');
    html = html.replace(/^- (.*$)/gim, '<li>$1</li>');
    html = html.replace(/^\d+\. (.*$)/gim, '<li>$1</li>');

    // تجميع عناصر القوائم
    html = html.replace(/((?:<li>.*<\/li>\n?)+)/gim, '<ul>$1</ul>');

    // فقرات
    html = html.replace(/\n\s*\n/gim, '</p><p>');
    html = '<p>' + html + '</p>';
    html = html.replace(/<p><(h[1-3]|table|blockquote|pre|hr|ul|li)/gim, '<$1');
    html = html.replace(/<\/(h[1-3]|table|blockquote|pre|hr|ul|li)><\/p>/gim, '</$1>');
    html = html.replace(/<p><\/p>/gim, '');

    // استعادة كتل الأكواد
    inlineCodes.forEach((code, idx) => {
        html = html.replace(`__INLINE_CODE_${idx}__`, code);
    });
    codeBlocks.forEach((code, idx) => {
        html = html.replace(`__CODE_BLOCK_${idx}__`, code);
    });

    return html;
}

function renderMD() {
    const input = document.getElementById('mdInput').value;
    const words = input.trim() ? input.trim().split(/\s+/).length : 0;
    const chars = input.length;
    document.getElementById('mdStats').textContent = `${words} كلمة | ${chars} حرف`;

    const html = parseMarkdown(input);
    document.getElementById('mdPreview').innerHTML = html;
}

function copyMD() {
    copyToClipboard(document.getElementById('mdInput').value);
}

function copyHTML() {
    copyToClipboard(document.getElementById('mdPreview').innerHTML);
}

function downloadMD() {
    const text = document.getElementById('mdInput').value;
    const blob = new Blob([text], { type: 'text/markdown;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'document.md';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تحميل ملف Markdown بنجاح!');
}

function downloadHTML() {
    const bodyContent = document.getElementById('mdPreview').innerHTML;
    const fullHtml = `<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مستند تم تصديره</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; line-height: 1.8; max-width: 800px; margin: 2rem auto; padding: 0 1rem; color: #1e293b; direction: rtl; }
        h1, h2, h3 { color: #0f172a; }
        pre { background: #0f172a; color: #f8fafc; padding: 1rem; border-radius: 8px; overflow-x: auto; direction: ltr; }
        code { background: #e2e8f0; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        pre code { background: none; color: inherit; }
        blockquote { border-right: 4px solid #6c63ff; background: #f8fafc; padding: 0.5rem 1rem; margin: 1rem 0; }
        table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        th, td { border: 1px solid #cbd5e1; padding: 0.5rem 0.75rem; text-align: right; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
${bodyContent}
</body>
</html>`;

    const blob = new Blob([fullHtml], { type: 'text/html;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'document.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast('تم تحميل صفحة HTML بنجاح!');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', initMD);
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
