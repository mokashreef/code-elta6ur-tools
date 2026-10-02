<?php
/**
 * أداة: مولد صفحة HTML بسيطة
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <form onsubmit="event.preventDefault();generateHTML()">
        <div class="form-row">
            <div class="form-group"><label class="form-label">عنوان الصفحة</label><input type="text" id="htmlTitle" class="form-control" value="صفحتي" required></div>
            <div class="form-group">
                <label class="form-label">اللغة</label>
                <select id="htmlLang" class="form-control"><option value="ar">عربي (RTL)</option><option value="en">إنجليزي</option></select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label class="form-label">لون الخلفية</label><input type="color" id="htmlBg" value="#0a0e1a" style="width:60px;height:36px;border-radius:8px;border:none;cursor:pointer"></div>
            <div class="form-group"><label class="form-label">لون النص</label><input type="color" id="htmlText" value="#f1f5f9" style="width:60px;height:36px;border-radius:8px;border:none;cursor:pointer"></div>
            <div class="form-group"><label class="form-label">لون الرئيسي</label><input type="color" id="htmlPrimary" value="#6c63ff" style="width:60px;height:36px;border-radius:8px;border:none;cursor:pointer"></div>
        </div>
        <div class="form-group"><label class="form-label">عنوان رئيسي</label><input type="text" id="htmlHeading" class="form-control" value="مرحباً بالعالم"></div>
        <div class="form-group"><label class="form-label">نص الفقرة</label><textarea id="htmlParagraph" class="form-control" rows="3">هذه صفحة HTML بسيطة تم إنشاؤها بواسطة Code Elta6ur Tools</textarea></div>
        <div class="form-row">
            <label class="form-check"><input type="checkbox" id="htmlNav" checked> شريط تنقل</label>
            <label class="form-check"><input type="checkbox" id="htmlFooter" checked> تذييل</label>
            <label class="form-check"><input type="checkbox" id="htmlAnimations" checked> حركات</label>
        </div>
        <div style="margin-top:1rem"><button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد الصفحة</button></div>
    </form>
</div>
<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-html5"></i> الصفحة جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button class="btn btn-primary btn-sm" onclick="downloadGeneratedHTML()"><i class="fas fa-download"></i> تحميل HTML</button>
            <button class="btn btn-secondary btn-sm" onclick="previewHTML()"><i class="fas fa-eye"></i> معاينة</button>
        </div>
    </div>
    <div class="result-content" id="resultContent" style="direction:ltr;text-align:left;font-size:0.8rem"></div>
</div>
<script>
let generatedHTML = '';
function generateHTML() {
    const t = document.getElementById('htmlTitle').value;
    const lang = document.getElementById('htmlLang').value;
    const dir = lang === 'ar' ? 'rtl' : 'ltr';
    const bg = document.getElementById('htmlBg').value;
    const text = document.getElementById('htmlText').value;
    const primary = document.getElementById('htmlPrimary').value;
    const h = document.getElementById('htmlHeading').value;
    const p = document.getElementById('htmlParagraph').value;
    const nav = document.getElementById('htmlNav').checked;
    const footer = document.getElementById('htmlFooter').checked;
    const anim = document.getElementById('htmlAnimations').checked;

    generatedHTML = `<!DOCTYPE html>
<html lang="${lang}" dir="${dir}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${t}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', Arial, sans-serif; background: ${bg}; color: ${text}; min-height: 100vh; direction: ${dir}; }
        ${nav ? `nav { background: rgba(255,255,255,0.05); padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        nav h2 { color: ${primary}; font-size: 1.2rem; }
        nav ul { list-style: none; display: flex; gap: 1.5rem; }
        nav a { color: ${text}; text-decoration: none; opacity: 0.8; transition: opacity 0.3s; }
        nav a:hover { opacity: 1; color: ${primary}; }` : ''}
        .hero { text-align: center; padding: 5rem 2rem; ${anim ? 'animation: fadeIn 1s ease;' : ''} }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; color: ${primary}; }
        .hero p { font-size: 1.1rem; opacity: 0.8; max-width: 600px; margin: 0 auto; line-height: 1.8; }
        ${footer ? `footer { text-align: center; padding: 2rem; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.85rem; opacity: 0.6; }` : ''}
        ${anim ? `@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }` : ''}
    </style>
</head>
<body>
    ${nav ? `<nav><h2>${t}</h2><ul><li><a href="#">الرئيسية</a></li><li><a href="#">حول</a></li><li><a href="#">تواصل</a></li></ul></nav>` : ''}
    <section class="hero">
        <h1>${h}</h1>
        <p>${p}</p>
    </section>
    ${footer ? `<footer>صنع بـ ❤️ باستخدام Code Elta6ur Tools</footer>` : ''}
</body>
</html>`;
    
    document.getElementById('resultContent').textContent = generatedHTML;
    document.getElementById('resultArea').classList.add('show');
}
function downloadGeneratedHTML() { downloadAsHTML(generatedHTML, 'page.html'); }
function previewHTML() {
    const win = window.open('', '_blank');
    win.document.write(generatedHTML);
    win.document.close();
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
