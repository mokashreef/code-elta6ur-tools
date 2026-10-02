<?php
/**
 * أداة: مولد أسماء مستخدمين
 */
$result = '';

$prefixes_ar = ['كودر', 'ديف', 'تيك', 'هاكر', 'بايت', 'نود', 'دوت', 'لينك', 'بكسل', 'فايل'];
$prefixes_en = ['Code', 'Dev', 'Tech', 'Pixel', 'Byte', 'Node', 'Dot', 'Web', 'Net', 'Pro', 'Dark', 'Neo', 'Cyber', 'Cloud', 'Stack'];
$suffixes_en = ['Master', 'Pro', 'Ninja', 'Wizard', 'King', 'Hero', 'Lord', 'Star', 'Fox', 'Wolf', 'Storm', 'Fire', 'Blaze', 'Shadow', 'Peak'];
$middles = ['_', '.', '-', '', 'x', 'X', '0', '1'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $baseName = sanitize($_POST['name'] ?? '');
    $style = sanitize($_POST['style'] ?? 'mixed');
    $count = min((int)($_POST['count'] ?? 10), 20);
    
    $names = [];
    for ($i = 0; $i < $count; $i++) {
        $mid = $middles[array_rand($middles)];
        $num = rand(0, 999);
        
        switch ($style) {
            case 'professional':
                $name = $baseName ? $baseName . $mid . $suffixes_en[array_rand($suffixes_en)] : $prefixes_en[array_rand($prefixes_en)] . $mid . $suffixes_en[array_rand($suffixes_en)];
                break;
            case 'creative':
                $name = $prefixes_en[array_rand($prefixes_en)] . $mid . ($baseName ?: $suffixes_en[array_rand($suffixes_en)]) . $num;
                break;
            case 'arabic':
                $name = $prefixes_ar[array_rand($prefixes_ar)] . '_' . ($baseName ?: 'سوري') . $num;
                break;
            default:
                $r = rand(1, 4);
                if ($r === 1) $name = $prefixes_en[array_rand($prefixes_en)] . $mid . ($baseName ?: $suffixes_en[array_rand($suffixes_en)]);
                elseif ($r === 2) $name = ($baseName ?: $prefixes_en[array_rand($prefixes_en)]) . $mid . $suffixes_en[array_rand($suffixes_en)] . rand(1, 99);
                elseif ($r === 3) $name = $prefixes_en[array_rand($prefixes_en)] . ($baseName ? ucfirst($baseName) : '') . $num;
                else $name = strtolower($baseName ?: $prefixes_en[array_rand($prefixes_en)]) . '.' . strtolower($suffixes_en[array_rand($suffixes_en)]);
        }
        $names[] = $name;
    }
    
    $result = "🎯 أسماء مستخدمين مقترحة:\n" . str_repeat('─', 40) . "\n\n";
    foreach ($names as $i => $n) {
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
        <div class="form-group"><label class="form-label">اسمك أو كلمة أساسية (اختياري)</label><input type="text" name="name" class="form-control" placeholder="مثال: ahmad"></div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">الأسلوب</label>
                <select name="style" class="form-control">
                    <option value="mixed">مزيج</option>
                    <option value="professional">احترافي</option>
                    <option value="creative">إبداعي</option>
                    <option value="arabic">عربي</option>
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
