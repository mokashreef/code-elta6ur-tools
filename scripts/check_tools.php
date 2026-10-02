<?php
require_once __DIR__ . '/../includes/tools_registry.php';

$reg = getMasterToolsRegistry();
echo "Total tools in registry: " . count($reg) . PHP_EOL;

$files = glob(__DIR__ . '/../tools/*.php');
$existing = array_map(function($f) { return basename($f, '.php'); }, $files);
echo "Existing files in tools/: " . count($existing) . PHP_EOL;

$missing = array_diff(array_keys($reg), $existing);
echo "Missing tool files: " . count($missing) . PHP_EOL;

$missingByCat = [];
foreach ($missing as $slug) {
    $cat = $reg[$slug]['category'] ?? 'other';
    $missingByCat[$cat][] = $slug;
}

echo "\nMissing by category:\n";
foreach ($missingByCat as $cat => $slugs) {
    echo " - $cat: " . count($slugs) . "\n";
}

file_put_contents(__DIR__ . '/missing_tools.json', json_encode($missingByCat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nSaved missing tools to scripts/missing_tools.json\n";
