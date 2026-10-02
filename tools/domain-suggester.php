<?php
/**
 * أداة: اقتراح أسماء دومينات
 */
$result = '';
$extensions = ['.com', '.net', '.io', '.dev', '.app', '.co', '.me', '.org', '.tech', '.site', '.online', '.xyz'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = sanitize($_POST['keyword'] ?? '');
    $industry = sanitize($_POST['industry'] ?? '');
    
    if ($keyword) {
        $kw = strtolower(str_replace(' ', '', $keyword));
        $prefixes = ['get', 'try', 'use', 'my', 'the', 'go', 'hey', 'just', 'be', 'we'];
        $suffixes = ['app', 'hub', 'lab', 'hq', 'io', 'ly', 'ify', 'now', 'pro', 'studio'];
        
        $suggestions = [];
        // اسم مباشر مع امتدادات
        foreach (array_slice($extensions, 0, 5) as $ext) {
            $suggestions[] = $kw . $ext;
        }
        // مع بادئة
        foreach (array_slice($prefixes, 0, 4) as $p) {
            $suggestions[] = $p . $kw . '.com';
        }
        // مع لاحقة
        foreach (array_slice($suffixes, 0, 4) as $s) {
            $suggestions[] = $kw . $s . '.com';
        }
        // إبداعي
        $suggestions[] = $kw . '-' . $industry . '.com';
        $suggestions[] = $kw . '.' . ($industry ?: 'dev');
        
        $suggestions = array_filter(array_unique($suggestions));
        
        $result = "🌐 اقتراحات دومينات لـ \"{$keyword}\":\n" . str_repeat('─', 40) . "\n\n";
        foreach (array_values($suggestions) as $i => $s) {
            $result .= "  " . ($i + 1) . ". " . $s . "\n";
        }
        $result .= "\n💡 ملاحظة: تحقق من التوفر عبر مواقع تسجيل الدومينات";
    }
    incrementToolUsage($toolId);
}

include __DIR__ . '/../includes/header.php';
?>
<div class="tool-page-header">
    <div class="tool-page-icon"><i class="fas <?= $tool['icon'] ?>"></i></div>
    <div class="tool-page-info"><h1><?= sanitize($tool['name']) ?></h1><p><?= sanitize($tool['description']) ?></p></div>
</div>
<div class="card">
    <form method="POST" id="toolForm">
        <div class="form-group"><label class="form-label">الكلمة المفتاحية <span class="required">*</span></label><input type="text" name="keyword" class="form-control" placeholder="مثال: taskflow" required></div>
        <div class="form-group"><label class="form-label">المجال (اختياري)</label><input type="text" name="industry" class="form-control" placeholder="مثال: tech, shop, learn"></div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-globe"></i> اقتراح دومينات</button>
    </form>
</div>
<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-globe"></i> الاقتراحات جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
