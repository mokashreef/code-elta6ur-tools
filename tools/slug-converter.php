<?php
/**
 * أداة: تحويل النص إلى Slug
 */
include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <div class="form-group">
        <label class="form-label">أدخل النص</label>
        <input type="text" id="slugInput" class="form-control" placeholder="مثال: My Awesome Project أو مشروعي الرائع" oninput="generateSlug()">
    </div>
    <div class="form-group">
        <label class="form-label">الفاصل</label>
        <select id="slugSeparator" class="form-control" style="max-width:200px" onchange="generateSlug()">
            <option value="-">شرطة (-)</option>
            <option value="_">شرطة سفلية (_)</option>
            <option value=".">نقطة (.)</option>
        </select>
    </div>
</div>
<div class="result-area" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-link"></i> Slug</span>
        <div class="result-actions"><button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button></div>
    </div>
    <div class="result-content" id="resultContent" style="direction:ltr;font-size:1.1rem;font-weight:600;color:var(--text-accent-light)"></div>
</div>
<script>
function generateSlug() {
    const input = document.getElementById('slugInput').value;
    const sep = document.getElementById('slugSeparator').value;
    if (!input) { document.getElementById('resultArea').classList.remove('show'); return; }
    
    // إزالة التشكيل العربي
    let slug = input.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    // محاولة transliterate الحروف العربية
    const arMap = {'ا':'a','ب':'b','ت':'t','ث':'th','ج':'j','ح':'h','خ':'kh','د':'d','ذ':'dh','ر':'r','ز':'z','س':'s','ش':'sh','ص':'s','ض':'d','ط':'t','ظ':'dh','ع':'a','غ':'gh','ف':'f','ق':'q','ك':'k','ل':'l','م':'m','ن':'n','ه':'h','و':'w','ي':'y','ة':'h','ى':'a','ء':'','آ':'a','أ':'a','إ':'i','ؤ':'w','ئ':'y'};
    slug = slug.split('').map(c => arMap[c] || c).join('');
    
    slug = slug.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, sep)
        .replace(new RegExp(`^\\${sep}+|\\${sep}+$`, 'g'), '');
    
    document.getElementById('resultContent').textContent = slug;
    document.getElementById('resultArea').classList.add('show');
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
