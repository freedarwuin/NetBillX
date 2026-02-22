<?php
include "../config.php";

try {
    $dbh = new PDO(
        "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";

    $payload = [
        "asset" => "USDT",
        "fiat" => "VES",
        "merchantCheck" => false,
        "page" => 1,
        "payTypes" => [],
        "publisherType" => null,
        "rows" => 20,
        "tradeType" => "BUY"
    ];

    $headers = [
        "accept: application/json, text/plain, */*",
        "accept-language: es-ES,es;q=0.9",
        "content-type: application/json",
        "origin: https://p2p.binance.com",
        "referer: https://p2p.binance.com/",
        "clienttype: web",
        "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36"
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_ENCODING => ""
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        die("CURL Error: " . curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['data']) || empty($data['data'])) {
        die("Sin datos Binance");
    }

    $prices = array_map(function ($o) {
        return floatval($o['adv']['price']);
    }, $data['data']);

    $min = min($prices);
    $max = max($prices);
    $avg = array_sum($prices) / count($prices);
    $offers = count($prices);

    // Insertar en BD
    $stmt = $dbh->prepare("
        INSERT INTO binance_rate (rate_date, avg_rate, min_rate, max_rate, offers)
        VALUES (NOW(), ?, ?, ?, ?)
    ");

    $stmt->execute([
        round($avg, 2),
        round($min, 2),
        round($max, 2),
        $offers
    ]);

    echo "Binance actualizado correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}