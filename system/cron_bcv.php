<?php
include "../config.php";

$tmpFile = __DIR__ . '/bcv_data.json';

try {

    // ===============================
    // 1️⃣ Conexión DB
    // ===============================
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
    // 2️⃣ Obtener API Key
    // ===============================
    $stmt = $dbh->prepare("
        SELECT value
        FROM tbl_appconfig
        WHERE setting = 'dolarvzla_api_key'
        LIMIT 1
    ");
    $stmt->execute();
    $row = $stmt->fetch();

    if (!$row || empty($row['value'])) {
        throw new Exception("No existe 'dolarvzla_api_key' en tbl_appconfig.");
    }

    $apiKey = trim($row['value']);

    // ===============================
    // 3️⃣ Función para llamar API
    // ===============================
    function callAPI($url, $apiKey) {

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "x-dolarvzla-key: $apiKey"
            ]
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception("CURL Error: " . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("API respondió con código HTTP $httpCode");
        }

        return json_decode($response, true);
    }

    // ===============================
    // 4️⃣ Tasa actual BCV y USDT
    // ===============================
    $bcvCurrent  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate", $apiKey);
    $usdtCurrent = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate", $apiKey);

    $bcv_rate    = $bcvCurrent['current']['usd'] ?? null;
    $bcv_date    = $bcvCurrent['current']['date'] ?? null;

    $usdt_rate   = $usdtCurrent['current']['usd'] ?? null;
    $usdt_date   = $usdtCurrent['current']['date'] ?? null;

    if (!$bcv_rate)  throw new Exception("No se pudo obtener tasa BCV actual.");
    if (!$usdt_rate) throw new Exception("No se pudo obtener tasa USDT actual.");

    // ===============================
    // 5️⃣ Histórico últimos 28 días
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-28 days'));

    $bcvList  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtList = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    $bcv_history  = [];
    $usdt_history = [];
    $prevBCV  = null;
    $prevUSDT = null;

    if (isset($bcvList['rates']) && is_array($bcvList['rates'])) {
        usort($bcvList['rates'], fn($a, $b) => strcmp($b['date'], $a['date']));
        foreach ($bcvList['rates'] as $row) {
            if (!isset($row['usd'], $row['date'])) continue;
            $rate = (float)$row['usd'];
            $date = $row['date'];
            $change = 'same';
            if ($prevBCV !== null) {
                if ($rate > $prevBCV) $change = 'up';
                elseif ($rate < $prevBCV) $change = 'down';
            }
            $bcv_history[] = ['rate' => $rate, 'rate_date' => $date, 'change' => $change];
            $prevBCV = $rate;
        }
    }

    if (isset($usdtList['rates']) && is_array($usdtList['rates'])) {
        usort($usdtList['rates'], fn($a, $b) => strcmp($b['date'], $a['date']));
        foreach ($usdtList['rates'] as $row) {
            if (!isset($row['usd'], $row['date'])) continue;
            $rate = (float)$row['usd'];
            $date = $row['date'];
            $change = 'same';
            if ($prevUSDT !== null) {
                if ($rate > $prevUSDT) $change = 'up';
                elseif ($rate < $prevUSDT) $change = 'down';
            }
            $usdt_history[] = ['rate' => $rate, 'rate_date' => $date, 'change' => $change];
            $prevUSDT = $rate;
        }
    }

    // ===============================
    // 6️⃣ Guardar JSON completo
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'    => $bcv_rate,
        'bcv_date'    => $bcv_date,
        'bcv_history' => $bcv_history,
        'usdt_rate'    => $usdt_rate,
        'usdt_date'    => $usdt_date,
        'usdt_history' => $usdt_history
    ], JSON_PRETTY_PRINT));

    echo "BCV y USDT actualizados correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}