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
    // 4️⃣ Histórico últimos 20 días
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    $list = callAPI(
        "https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today",
        $apiKey
    );

    $bcv_history = [];
    $previousRate = null;

    if (isset($list['rates']) && is_array($list['rates'])) {

        // ordenar descendente (de más reciente a más antiguo)
        usort($list['rates'], fn($a,$b) => strcmp($b['date'],$a['date']));

        foreach ($list['rates'] as $row) {

            if (!isset($row['usd'], $row['date'])) continue;

            $rate = (float)$row['usd'];
            $usdt = isset($row['usdt']) ? (float)$row['usdt'] : null;
            $eur  = isset($row['eur'])  ? (float)$row['eur']  : null;
            $date = $row['date'];

            $change = 'same';
            if ($previousRate !== null) {
                if ($rate > $previousRate) $change = 'up';
                elseif ($rate < $previousRate) $change = 'down';
            }

            $bcv_history[] = [
                'rate'      => $rate,
                'usdt'      => $usdt,
                'eur'       => $eur,
                'rate_date' => $date,
                'change'    => $change
            ];

            $previousRate = $rate;
        }
    }

    // ===============================
    // 5️⃣ Tasa actual (la más reciente)
    // ===============================
    if (count($bcv_history) > 0) {
        $latest = $bcv_history[0];
        $bcv_rate = $latest['rate'];
        $usdt_rate = $latest['usdt'];
        $eur_rate  = $latest['eur'];
        $rate_date = $latest['rate_date'];
    } else {
        throw new Exception("No se pudo obtener tasa BCV actual.");
    }

    // ===============================
    // 6️⃣ Guardar JSON
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'    => $bcv_rate,
        'usdt_rate'   => $usdt_rate,
        'eur_rate'    => $eur_rate,
        'rate_date'   => $rate_date,
        'bcv_history' => $bcv_history
    ], JSON_PRETTY_PRINT));

    echo "BCV actualizado correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}