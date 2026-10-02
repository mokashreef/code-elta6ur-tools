<?php
$urls = [
    '/',
    '/tool.php?slug=net-salary-calculator',
    '/tool.php?slug=electricity-consumption-calculator',
    '/tool.php?slug=paint-calculator',
    '/tool.php?slug=monthly-gas-cost-calculator',
    '/tool.php?slug=grade-percentage-calculator',
    '/tool.php?slug=is-salary-enough-calculator',
    '/tool.php?slug=markdown-arabic-editor',
    '/tool.php?slug=json-validator',
    '/tool.php?slug=budget-calculator',
    '/tool.php?slug=password-generator',
    '/tool.php?slug=time-tracker',
    '/api/search.php?q=' . urlencode('راتب'),
    '/api/search.php?q=' . urlencode('كهرباء'),
    '/api/search.php?q=' . urlencode('json')
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$success = 0;
$fail = 0;

foreach ($urls as $url) {
    $fullUrl = 'http://127.0.0.1:8080' . $url;
    curl_setopt($ch, CURLOPT_URL, $fullUrl);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    if ($httpCode === 200) {
        $success++;
        echo "[200 OK] $url (Bytes: " . strlen($response) . ")\n";
    } else {
        $fail++;
        echo "[$httpCode ERROR] $url\n";
    }
}
curl_close($ch);

echo "\nSummary: $success Succeeded, $fail Failed.\n";
