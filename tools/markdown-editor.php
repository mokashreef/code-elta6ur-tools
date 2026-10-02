<?php
/**
 * أداة: محرر Markdown مع معاينة
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;min-height:500px">
    <div class="card" style="display:flex;flex-direction:column">
        <div class="d-flex align-center justify-between mb-1">
            <h3 style="font-size:0.9rem;color:var(--text-secondary)"><i class="fas fa-edit"></i> المحرر</h3>
            <div class="d-flex gap-1">
                <button class="btn btn-ghost btn-sm" onclick="insertMD('**','**')"><b>B</b></button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('*','*')"><i>I</i></button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('# ','')">H1</button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('## ','')">H2</button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('- ','')">•</button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('[','](url)')">🔗</button>
                <button class="btn btn-ghost btn-sm" onclick="insertMD('```\n','\n```')">{'<>'}</button>
            </div>
        </div>
        <textarea id="mdInput" class="form-control" style="flex:1;min-height:400px;font-family:monospace;resize:none" oninput="renderMD()" placeholder="# مرحباً&#10;&#10;اكتب **Markdown** هنا...&#10;&#10;- عنصر 1&#10;- عنصر 2&#10;&#10;```&#10;console.log('Hello');&#10;```"># مرحباً بك في محرر Markdown

هذا **نص عريض** وهذا *نص مائل*.

## المميزات
- سهل الاستخدام
- معاينة فورية
- يدعم العربية

```javascript
console.log('مرحباً!');
```

> اقتباس مميز

[رابط](https://example.com)</textarea>
    </div>
    <div class="card" style="overflow:auto">
        <div class="d-flex align-center justify-between mb-1">
            <h3 style="font-size:0.9rem;color:var(--text-secondary)"><i class="fas fa-eye"></i> المعاينة</h3>
            <div class="d-flex gap-1">
                <button class="btn btn-ghost btn-sm" onclick="copyToClipboard(document.getElementById('mdInput').value)"><i class="fas fa-copy"></i> نسخ MD</button>
                <button class="btn btn-ghost btn-sm" onclick="copyToClipboard(document.getElementById('mdPreview').innerHTML)"><i class="fas fa-code"></i> نسخ HTML</button>
            </div>
        </div>
        <div id="mdPreview" style="line-height:1.8;font-size:0.9rem"></div>
    </div>
</div>

<style>
#mdPreview h1 { font-size:1.75rem; margin:1rem 0 0.5rem; color:var(--text-accent-light); border-bottom:1px solid var(--border-color); padding-bottom:0.5rem; }
#mdPreview h2 { font-size:1.35rem; margin:1rem 0 0.5rem; color:var(--text-primary); }
#mdPreview h3 { font-size:1.1rem; margin:0.75rem 0 0.5rem; }
#mdPreview p { margin:0.5rem 0; }
#mdPreview ul, #mdPreview ol { padding-right:1.5rem; margin:0.5rem 0; }
#mdPreview li { margin:0.25rem 0; }
#mdPreview code { background:var(--bg-input); padding:0.15rem 0.4rem; border-radius:4px; font-size:0.85rem; color:var(--text-accent-light); }
#mdPreview pre { background:var(--bg-input); padding:1rem; border-radius:8px; overflow-x:auto; margin:0.75rem 0; border:1px solid var(--border-color); }
#mdPreview pre code { background:none; padding:0; }
#mdPreview blockquote { border-right:3px solid var(--text-accent); padding:0.5rem 1rem; margin:0.75rem 0; background:rgba(108,99,255,0.05); border-radius:0 8px 8px 0; }
#mdPreview a { color:var(--text-accent-light); }
#mdPreview strong { color:var(--text-primary); }
#mdPreview hr { border:none; border-top:1px solid var(--border-color); margin:1rem 0; }
@media(max-width:768px) { div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns:1fr !important; } }
</style>

<script>
function renderMD() {
    const input = document.getElementById('mdInput').value;
    let html = input
        .replace(/^### (.+)$/gm, '<h3>$1</h3>')
        .replace(/^## (.+)$/gm, '<h2>$1</h2>')
        .replace(/^# (.+)$/gm, '<h1>$1</h1>')
        .replace(/```(\w*)\n([\s\S]*?)```/g, '<pre><code>$2</code></pre>')
        .replace(/`([^`]+)`/g, '<code>$1</code>')
        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.+?)\*/g, '<em>$1</em>')
        .replace(/^\> (.+)$/gm, '<blockquote>$1</blockquote>')
        .replace(/^- (.+)$/gm, '<li>$1</li>')
        .replace(/^\d+\. (.+)$/gm, '<li>$1</li>')
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank">$1</a>')
        .replace(/^---$/gm, '<hr>')
        .replace(/\n\n/g, '</p><p>')
        .replace(/<\/li>\n<li>/g, '</li><li>');
    
    // Wrap list items in ul
    html = html.replace(/(<li>.*?<\/li>)/gs, '<ul>$1</ul>');
    html = html.replace(/<\/ul>\s*<ul>/g, '');
    
    html = '<p>' + html + '</p>';
    html = html.replace(/<p><(h[1-3]|pre|blockquote|ul|hr)/g, '<$1');
    html = html.replace(/<\/(h[1-3]|pre|blockquote|ul|hr)><\/p>/g, '</$1>');
    html = html.replace(/<p><\/p>/g, '');
    
    document.getElementById('mdPreview').innerHTML = html;
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
}

renderMD();
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
