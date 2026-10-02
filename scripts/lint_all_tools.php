<?php
$files = glob(__DIR__ . '/../tools/*.php');
$errors = [];
$checked = 0;

foreach ($files as $file) {
    $checked++;
    $out = [];
    $code = 0;
    exec('php -l "' . $file . '" 2>&1', $out, $code);
    if ($code !== 0) {
        $errors[$file] = implode(' ', $out);
    }
}

echo "Checked $checked tool files." . PHP_EOL;
echo "Total syntax errors: " . count($errors) . PHP_EOL;

if (!empty($errors)) {
    foreach ($errors as $file => $err) {
        echo "FAIL: $file => $err" . PHP_EOL;
    }
} else {
    echo "ALL 100% PASS SYNTAX VALIDATION!" . PHP_EOL;
}
