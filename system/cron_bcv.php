<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// =============================
// CONFIG
// =============================
$apiKey = "e87ea1d5447c431f93e6088c963b9f6f01a416edbe5a810dfc8e8d7149bafd0d"; // pon tu nueva key
$url = "https://api.dolarvzla.com/public/bcv/exchange-rate";

echo "<h2>Probando API DolarVzla</h2>";
echo "<pre>";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_HEADER => true,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "x-dolarvzla-key: $apiKey"
    ]
]);

$response = curl_exec($ch);

if ($response === false) {
    echo "❌ Error cURL:\n";
    echo curl_error($ch);
    curl_close($ch);
    exit;
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

curl_close($ch);

echo "HTTP CODE: $httpCode\n\n";

echo "===== HEADERS =====\n";
echo $headers . "\n\n";

echo "===== BODY RAW =====\n";
echo $body . "\n\n";

$data = json_decode($body, true);

echo "===== JSON DECODIFICADO =====\n";
print_r($data);

echo "</pre>";