<?php
/**
 * Ada Ayman – COMP1006 Assignment 1
 * Purpose: Fetch cat images from The Cat API using cURL.
 */

declare(strict_types=1);

function ada_fetch_cat_images(?int $count = null): array
{
    // 1) Load settings 
    $configPath = __DIR__ . '/config.php';
    if (is_file($configPath)) {
        $cfg = require $configPath;                
    } else {
        $cfg = require __DIR__ . '/config.sample.php'; // safe placeholder 
    }

    // 2) To decide how many images to request 
    $limit = $count ?? (int)($cfg['DEFAULT_LIMIT'] ?? 8);
    if ($limit < 1)  { $limit = 1; }
    if ($limit > 20) { $limit = 20; } 

    // 3) Build the request URL with query parameters
    $params = [
        'limit'      => $limit,
        'mime_types' => $cfg['DEFAULT_MIME_TYPES'] ?? 'jpg,png',
        'size'       => $cfg['DEFAULT_SIZE'] ?? 'med',
    ];
    $url = ($cfg['CAT_API_URL'] ?? '') . '?' . http_build_query($params);

    // 4) Prepare headers. 
    $headers = ['Accept: application/json'];
    if (!empty($cfg['CAT_API_KEY']) && $cfg['CAT_API_KEY'] !== 'REPLACE_WITH_YOUR_KEY') {
        $headers[] = 'x-api-key: ' . $cfg['CAT_API_KEY'];
    }

    // 5) Make the HTTP request with cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,  // return response as a string
        CURLOPT_TIMEOUT        => 10,    // 10 second timeout
        CURLOPT_FOLLOWLOCATION => true,  // follow redirects, just in case
        CURLOPT_HTTPHEADER     => $headers,
    ]);

    $raw    = curl_exec($ch);
    $errNo  = curl_errno($ch);
    $errStr = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 6) Handle errors
    if ($errNo !== 0) {
        return ['error' => 'Network error: ' . $errStr];
    }
    if ($status < 200 || $status >= 300) {
        return ['error' => 'API returned HTTP ' . $status];
    }

    // 7) Parse JSON
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        return ['error' => 'Invalid JSON from API'];
    }

    // 8) Extract relevant fields
    $items = [];
    foreach ($data as $row) {
        if (!empty($row['url'])) {
            $items[] = [
                'url'    => $row['url'],
                'id'     => $row['id']     ?? null,
                'width'  => $row['width']  ?? null,
                'height' => $row['height'] ?? null,
            ];
        }
    }

    return ['items' => $items];
}
