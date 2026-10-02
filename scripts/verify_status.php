<?php
require_once __DIR__ . '/../includes/tools_registry.php';

$tools = getMasterToolsRegistry();
$missing = [];
$existing = [];

foreach ($tools as $id => $tool) {
    $filePath = __DIR__ . '/../tools/' . $id . '.php';
    if (!file_exists($filePath)) {
        $missing[] = $id;
    } else {
        $existing[] = $id;
    }
}

echo "Total registered: " . count($tools) . PHP_EOL;
echo "Existing files: " . count($existing) . PHP_EOL;
echo "Missing files: " . count($missing) . PHP_EOL;

if (!empty($missing)) {
    echo "Missing tool IDs:\n";
    foreach ($missing as $m) {
        echo " - $m\n";
    }
}

// Check for tools directory files not in registry
$allToolFiles = glob(__DIR__ . '/../tools/*.php');
$notInRegistry = [];
foreach ($allToolFiles as $file) {
    $base = basename($file, '.php');
    if (!isset($tools[$base])) {
        $notInRegistry[] = $base;
    }
}

echo "Files in tools/ not in registry: " . count($notInRegistry) . PHP_EOL;
if (!empty($notInRegistry)) {
    foreach ($notInRegistry as $n) {
        echo " * $n\n";
    }
}
