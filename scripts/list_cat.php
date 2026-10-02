<?php
require_once __DIR__ . '/../includes/tools_registry.php';

$reg = getMasterToolsRegistry();
$category = $argv[1] ?? 'finance';

echo "=== Category: $category ===\n";
foreach ($reg as $slug => $t) {
    if ($t['category'] === $category) {
        echo "Slug: " . str_pad($slug, 35) . " | Name: " . $t['name'] . "\n";
    }
}
