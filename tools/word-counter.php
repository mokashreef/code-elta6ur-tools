<?php
/**
 * أداة: عداد الكلمات
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
        <textarea id="wcInput" class="form-control" rows="8" placeholder="الصق نصك هنا..." oninput="countWords()"></textarea>
    </div>
</div>
<div class="stats-grid" style="margin-top:1.25rem">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-font"></i></div>
        <div><div class="stat-value" id="charCount">0</div><div class="stat-label">حرف</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-paragraph"></i></div>
        <div><div class="stat-value" id="wordCount">0</div><div class="stat-label">كلمة</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-align-justify"></i></div>
        <div><div class="stat-value" id="lineCount">0</div><div class="stat-label">سطر</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div><div class="stat-value" id="readTime">0</div><div class="stat-label">دقيقة قراءة</div></div>
    </div>
</div>
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-text-width"></i></div>
        <div><div class="stat-value" id="charNoSpace">0</div><div class="stat-label">حرف بدون مسافات</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-list"></i></div>
        <div><div class="stat-value" id="sentenceCount">0</div><div class="stat-label">جملة</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-paragraph"></i></div>
        <div><div class="stat-value" id="paragraphCount">0</div><div class="stat-label">فقرة</div></div>
    </div>
</div>
<script>
function countWords() {
    const text = document.getElementById('wcInput').value;
    const chars = text.length;
    const charsNoSpace = text.replace(/\s/g, '').length;
    const words = text.trim() ? text.trim().split(/\s+/).length : 0;
    const lines = text ? text.split('\n').length : 0;
    const sentences = text.trim() ? text.split(/[.!?؟。]+/).filter(s => s.trim()).length : 0;
    const paragraphs = text.trim() ? text.split(/\n\s*\n/).filter(p => p.trim()).length : 0;
    const readTime = Math.max(1, Math.ceil(words / 200));
    
    document.getElementById('charCount').textContent = chars;
    document.getElementById('charNoSpace').textContent = charsNoSpace;
    document.getElementById('wordCount').textContent = words;
    document.getElementById('lineCount').textContent = lines;
    document.getElementById('sentenceCount').textContent = sentences;
    document.getElementById('paragraphCount').textContent = paragraphs || (text.trim() ? 1 : 0);
    document.getElementById('readTime').textContent = words > 0 ? readTime : 0;
}
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
