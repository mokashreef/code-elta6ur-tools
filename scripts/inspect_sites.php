<?php
$sites = [
    'main' => 'https://code-elta6ur.com',
    'portfolio' => 'https://portfolio.code-elta6ur.net',
    'code_ora' => 'https://code-ora.com',
    'baccalaureate' => 'https://baccalaureate.code-elta6ur.sy',
    'products' => 'https://code-elta6ur.net',
    'hardwarebase' => 'https://hardwarebase.code-elta6ur.com',
    'mohammad' => 'https://mohammad.code-elta6ur.com',
    'github' => 'https://github.com/mokashreef'
];

echo "========================================================\n";
echo "INSPECTING CODE ELTA6UR ECOSYSTEM PLATFORMS\n";
echo "========================================================\n\n";

$ctx = stream_context_create([
    'http' => [
        'timeout' => 8,
        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ],
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false
    ]
]);

foreach ($sites as $key => $url) {
    echo "--- [$key] $url ---\n";
    $content = @file_get_contents($url, false, $ctx);
    if ($content === false) {
        // try curl
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        $content = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (!$content) {
            echo "STATUS: UNREACHABLE or TIMEOUT (HTTP $code)\n\n";
            continue;
        }
    }
    
    // Extract title
    preg_match('/<title[^>]*>(.*?)<\/title>/is', $content, $tm);
    $title = isset($tm[1]) ? trim(html_entity_decode(strip_tags($tm[1]))) : 'N/A';
    
    // Extract meta description
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $content, $dm);
    $desc = isset($dm[1]) ? trim(html_entity_decode($dm[1])) : 'N/A';
    if ($desc === 'N/A') {
        preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\'](.*?)["\']/is', $content, $odm);
        $desc = isset($odm[1]) ? trim(html_entity_decode($odm[1])) : 'N/A';
    }

    // Extract headings
    preg_match_all('/<h[1-3][^>]*>(.*?)<\/h[1-3]>/is', $content, $hm);
    $headings = [];
    if (!empty($hm[1])) {
        foreach (array_slice($hm[1], 0, 8) as $h) {
            $cleaned = trim(html_entity_decode(strip_tags($h)));
            if (!empty($cleaned)) $headings[] = $cleaned;
        }
    }

    echo "TITLE: $title\n";
    echo "DESC:  $desc\n";
    echo "HEADINGS:\n";
    foreach ($headings as $h) {
        echo "  * $h\n";
    }
    echo "\n";
}
