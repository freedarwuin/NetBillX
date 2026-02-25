<?php
include "../config.php";

$tmpFile = __DIR__ . '/exchange_data.json';

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

    // Obtener API Key
    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting='dolarvzla_api_key' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    if (!$row || empty($row['value'])) throw new Exception("No existe 'dolarvzla_api_key'.");
    $apiKey = trim($row['value']);

    // Función genérica
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
        if (curl_errno($ch)) throw new Exception("CURL Error: " . curl_error($ch));
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) throw new Exception("API respondió con código HTTP $httpCode");
        return json_decode($response, true);
    }

    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    // ===============================
    // BCV HISTORICO
    // ===============================
    $bcv_list = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $bcv_history = [];
    $prev = null;
    if (isset($bcv_list['rates']) && is_array($bcv_list['rates'])) {
        usort($bcv_list['rates'], fn($a,$b)=>strcmp($b['date'],$a['date']));
        foreach ($bcv_list['rates'] as $row) {
            if (!isset($row['usd'],$row['date'])) continue;
            $rate = (float)$row['usd'];
            $usdt = isset($row['usdt']) ? (float)$row['usdt'] : null;
            $eur  = isset($row['eur']) ? (float)$row['eur'] : null;
            $date = $row['date'];
            $change='same';
            if ($prev!==null) $change=$rate>$prev?'up':($rate<$prev?'down':'same');
            $bcv_history[] = ['rate'=>$rate,'usdt'=>$usdt,'eur'=>$eur,'rate_date'=>$date,'change'=>$change];
            $prev=$rate;
        }
    }

    // ===============================
    // BCV ACTUAL
    // ===============================
    $bcv_latest = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate", $apiKey);

    // ===============================
    // USDT HISTORICO
    // ===============================
    $usdt_list = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdt_history = [];
    $prev = null;
    if (isset($usdt_list['rates']) && is_array($usdt_list['rates'])) {
        usort($usdt_list['rates'], fn($a,$b)=>strcmp($b['date'],$a['date']));
        foreach ($usdt_list['rates'] as $row) {
            if (!isset($row['rate'],$row['date'])) continue;
            $rate = (float)$row['rate'];
            $date = $row['date'];
            $change='same';
            if ($prev!==null) $change=$rate>$prev?'up':($rate<$prev?'down':'same');
            $usdt_history[] = ['rate'=>$rate,'rate_date'=>$date,'change'=>$change];
            $prev=$rate;
        }
    }

    // ===============================
    // USDT ACTUAL
    // ===============================
    $usdt_latest = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate", $apiKey);

    // ===============================
    // Guardar JSON combinado
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv' => [
            'latest'  => $bcv_latest,
            'history' => $bcv_history
        ],
        'usdt' => [
            'latest'  => $usdt_latest,
            'history' => $usdt_history
        ]
    ], JSON_PRETTY_PRINT));

    echo "Datos BCV y USDT actualizados correctamente\n";

} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
}