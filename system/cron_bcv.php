<?php
include "../config.php";

$tmpFile = __DIR__ . '/bcv_data.json';

try {

    // Conexión DB
    $dbh = new PDO(
        "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Obtener API Key
    $stmt = $dbh->prepare("
        SELECT value
        FROM tbl_appconfig
        WHERE setting = 'dolarvzla_api_key'
        LIMIT 1
    ");
    $stmt->execute();
    $row = $stmt->fetch();
    if (!$row || empty($row['value'])) throw new Exception("No existe 'dolarvzla_api_key'.");
    $apiKey = trim($row['value']);

    // Llamar API
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.dolarvzla.com/public/bcv/exchange-rate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "x-dolarvzla-key: $apiKey"
        ]
    ]);
    $response = curl_exec($ch);
    if (curl_errno($ch)) throw new Exception("CURL Error: " . curl_error($ch));
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 429) throw new Exception("Rate limit activo.");
    if ($httpCode !== 200) throw new Exception("API respondió con HTTP $httpCode");

    $data = json_decode($response, true);
    if (!isset($data['current']['usd'])) throw new Exception("Respuesta inesperada de la API.");

    $rates = [
        'usd'  => (float)$data['current']['usd'],
        'usdt' => isset($data['current']['usdt']) ? (float)$data['current']['usdt'] : null,
        'eur'  => isset($data['current']['eur']) ? (float)$data['current']['eur'] : null,
        'date' => $data['current']['date'] ?? date('Y-m-d')
    ];

    file_put_contents($tmpFile, json_encode($rates, JSON_PRETTY_PRINT));

    echo "Tasas BCV guardadas correctamente.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}