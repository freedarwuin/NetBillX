<?php
include "../config.php";

try {

    $dbh = new PDO(
        "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // ===============================
    // 1️⃣ Consulta a Binance P2P
    // ===============================

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
        "user-agent: Mozilla/5.0"
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
        throw new Exception("CURL Error: " . curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['data']) || empty($data['data'])) {
        throw new Exception("Binance no devolvió datos válidos.");
    }

    // ===============================
    // 2️⃣ Procesar precios
    // ===============================

    $prices = array_map(function ($offer) {
        return floatval($offer['adv']['price']);
    }, $data['data']);

    $min = min($prices);
    $max = max($prices);
    $avg = array_sum($prices) / count($prices);
    $offers = count($prices);

    // ===============================
    // 3️⃣ Insertar 1 registro por minuto
    // ===============================

    $stmt = $dbh->prepare("
        INSERT INTO binance_rate (rate_date, avg_rate, min_rate, max_rate, offers)
        VALUES (DATE_FORMAT(NOW(), '%Y-%m-%d %H:%i:00'), ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            avg_rate = VALUES(avg_rate),
            min_rate = VALUES(min_rate),
            max_rate = VALUES(max_rate),
            offers = VALUES(offers),
            created_at = CURRENT_TIMESTAMP
    ");

    $stmt->execute([
        round($avg, 2),
        round($min, 2),
        round($max, 2),
        $offers
    ]);

    // ===============================
    // 4️⃣ Limpiar histórico mayor a 30 días
    // ===============================

    $dbh->exec("
        DELETE FROM binance_rate
        WHERE rate_date < NOW() - INTERVAL 30 DAY
    ");

    echo "Binance actualizado correctamente\n";

} catch (Exception $e) {

    echo "Error: " . $e->getMessage() . "\n";
}