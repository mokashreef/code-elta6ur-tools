<?php
$files = glob(__DIR__ . '/../tools/*.php');
$errors = [];
$checked = 0;

foreach ($files as $file) {
    $checked++;
    $content = file_get_contents($file);
    try {
        token_get_all($content, TOKEN_PARSE);
    } catch (\ParseError $e) {
        $errors[basename($file)] = $e->getMessage() . " on line " . $e->getLine();
    }
}

echo "Checked $checked tool files." . PHP_EOL;
echo "Total syntax errors: " . count($errors) . PHP_EOL;

if (!empty($errors)) {
    foreach ($errors as $file => $err) {
        echo "FAIL: $file => $err" . PHP_EOL;
    }
} else {
    echo "SUCCESS: ALL $checked FILES 100% VALID SYNTAX!" . PHP_EOL;
}
