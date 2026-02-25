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
    // 2️⃣ Obtener API Key desde tbl_appconfig
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
        if (curl_errno($ch)) throw new Exception("CURL Error: " . curl_error($ch));
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) throw new Exception("API respondió con código HTTP $httpCode");
        return json_decode($response, true);
    }

    // ===============================
    // 4️⃣ Fechas
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    // ===============================
    // 5️⃣ Obtener históricos BCV y USDT
    // ===============================
    $bcvData  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtData = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    // ===============================
    // 6️⃣ Organizar históricos por fecha
    // ===============================
    $history = [];
    $previousBCV  = null;
    $previousUSDT = null;

    // BCV
    if (isset($bcvData['rates']) && is_array($bcvData['rates'])) {
        foreach ($bcvData['rates'] as $row) {
            $date = $row['date'];
            $rate = isset($row['usd']) ? (float)$row['usd'] : null;
            $usdt = isset($row['usdt']) ? (float)$row['usdt'] : null;
            $eur  = isset($row['eur'])  ? (float)$row['eur']  : null;
            $change = 'same';
            if ($previousBCV !== null && $rate !== null) {
                if ($rate > $previousBCV) $change = 'up';
                elseif ($rate < $previousBCV) $change = 'down';
            }
            $history[$date]['bcv'] = [
                'rate'   => $rate,
                'usdt'   => $usdt,
                'eur'    => $eur,
                'change' => $change
            ];
            $previousBCV = $rate;
        }
    }

    // USDT
    if (isset($usdtData['rates']) && is_array($usdtData['rates'])) {
        foreach ($usdtData['rates'] as $row) {
            $date = $row['date'];
            $buy  = isset($row['buy'])  ? (float)$row['buy']  : null;
            $sell = isset($row['sell']) ? (float)$row['sell'] : null;
            $change = 'same';
            if ($previousUSDT !== null && $buy !== null) {
                if ($buy > $previousUSDT) $change = 'up';
                elseif ($buy < $previousUSDT) $change = 'down';
            }
            $history[$date]['usdt'] = [
                'buy'    => $buy,
                'sell'   => $sell,
                'change' => $change
            ];
            $previousUSDT = $buy;
        }
    }

    // ===============================
    // 7️⃣ Ordenar fechas descendente
    // ===============================
    krsort($history);

    // ===============================
    // 8️⃣ Extraer tasa actual (más reciente)
    // ===============================
    $latestDate = key($history);
    $latestBCV  = $history[$latestDate]['bcv'] ?? [];
    $latestUSDT = $history[$latestDate]['usdt'] ?? [];

    $bcv_rate  = $latestBCV['rate'] ?? null;
    $usdt_rate = $latestBCV['usdt'] ?? null;
    $eur_rate  = $latestBCV['eur'] ?? null;
    $rate_date = $latestDate;

    // ===============================
    // 9️⃣ Guardar JSON
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'    => $bcv_rate,
        'usdt_rate'   => $usdt_rate,
        'eur_rate'    => $eur_rate,
        'rate_date'   => $rate_date,
        'history'     => $history
    ], JSON_PRETTY_PRINT));

    echo "Histórico BCV + USDT generado correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}