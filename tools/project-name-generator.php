<?php
/**
 * أداة: مولد أسماء مشاريع
 */
$result = '';
$adjectives = ['Swift', 'Bright', 'Dark', 'Rapid', 'Smart', 'Quick', 'Sharp', 'Bold', 'Prime', 'Core', 'Ultra', 'Mega', 'Nova', 'Zen', 'Flux', 'Apex', 'Pure', 'Vivid', 'Deep', 'Open'];
$nouns = ['Hub', 'Lab', 'Box', 'Flow', 'Mind', 'Wave', 'Link', 'Task', 'Nest', 'Forge', 'Base', 'Cloud', 'Space', 'Point', 'Stack', 'Vault', 'Port', 'Spark', 'Gate', 'Pulse'];
$tech = ['App', 'Dev', 'Code', 'Tech', 'Web', 'Net', 'Data', 'Sync', 'API', 'UI', 'Kit', 'Sys', 'IO', 'OS', 'AI'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keyword = sanitize($_POST['keyword'] ?? '');
    $type = sanitize($_POST['type'] ?? 'saas');
    $count = min((int)($_POST['count'] ?? 10), 20);
    
    $names = [];
    for ($i = 0; $i < $count; $i++) {
        $adj = $adjectives[array_rand($adjectives)];
        $noun = $nouns[array_rand($nouns)];
        $t = $tech[array_rand($tech)];
        $kw = ucfirst($keyword);
        
        $patterns = [
            $adj . $noun,
            ($kw ?: $adj) . $noun,
            $noun . $t,
            $adj . ($kw ?: $t),
            ($kw ?: $adj) . '.' . strtolower($t),
            strtolower($adj) . ucfirst($noun),
            $t . $noun,
            ($kw ?: $noun) . $t,
            $adj . '-' . strtolower($noun),
            strtolower($adj . $noun) . '.io',
        ];
        $names[] = $patterns[array_rand($patterns)];
    }
    $names = array_unique($names);
    
    $result = "🚀 أسماء مشاريع مقترحة:\n" . str_repeat('─', 40) . "\n\n";
    foreach (array_values($names) as $i => $n) {
        $result .= "  " . ($i + 1) . ". " . $n . "\n";
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
        <div class="form-group"><label class="form-label">كلمة مفتاحية (اختياري)</label><input type="text" name="keyword" class="form-control" placeholder="مثال: shop, learn, task"></div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">نوع المشروع</label>
                <select name="type" class="form-control">
                    <option value="saas">SaaS / تطبيق ويب</option>
                    <option value="mobile">تطبيق موبايل</option>
                    <option value="api">API / خدمة</option>
                    <option value="game">لعبة</option>
                </select>
            </div>
            <div class="form-group"><label class="form-label">العدد</label><input type="number" name="count" class="form-control" value="10" min="1" max="20"></div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-magic"></i> توليد الأسماء</button>
    </form>
</div>
<?php if ($result): ?>
<div class="result-area show" id="resultArea">
    <div class="result-header">
        <span class="result-title"><i class="fas fa-check-circle"></i> الأسماء جاهزة!</span>
        <div class="result-actions">
            <button class="btn btn-ghost btn-sm" onclick="copyResult()"><i class="fas fa-copy"></i> نسخ</button>
            <button type="submit" form="toolForm" class="btn btn-secondary btn-sm"><i class="fas fa-sync"></i> توليد جديد</button>
        </div>
    </div>
    <div class="result-content" id="resultContent"><?= $result ?></div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
