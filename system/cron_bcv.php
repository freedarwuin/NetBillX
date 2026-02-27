<?php
/**
 * cron_bcv.php
 * Genera bcv_data.json con tasas BCV, USDT y EUR
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config.php";

$tmpFile = __DIR__ . '/bcv_data.json';

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

    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting = 'dolarvzla_api_key' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();

    if (!$row || empty($row['value'])) {
        throw new Exception("No existe 'dolarvzla_api_key'.");
    }

    $apiKey = trim($row['value']);

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
        if ($httpCode !== 200) throw new Exception("API HTTP $httpCode");
        return json_decode($response, true);
    }

    // Obtener tasas
    $bcvCurrent = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate", $apiKey);
    if (!isset($bcvCurrent['current']['usd'])) throw new Exception("No se pudo obtener tasa actual.");
    $current_usd  = (float)$bcvCurrent['current']['usd'];
    $current_eur  = (float)$bcvCurrent['current']['eur'] ?? null;
    $current_date = substr($bcvCurrent['current']['date'], 0, 10);

    // Histórico últimos 20 días
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    $bcvList  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtList = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    $bcv_history = [];
    $usdtIndexed = [];
    if (isset($usdtList['rates'])) {
        foreach ($usdtList['rates'] as $u) {
            $uDate = substr($u['date'], 0, 10);
            $usdtIndexed[$uDate] = isset($u['average']) ? (float)$u['average'] : null;
        }
    }

    if (isset($bcvList['rates']) && is_array($bcvList['rates'])) {
        usort($bcvList['rates'], fn($a,$b)=>strcmp($b['date'],$a['date']));
        $lastUsdt = null;
        foreach ($bcvList['rates'] as $row) {
            if (!isset($row['usd'], $row['date'])) continue;
            $rateBCV = (float)$row['usd'];
            $rateEUR = (float)($row['eur'] ?? 0);
            $date = substr($row['date'], 0, 10);
            $usdtRate = $usdtIndexed[$date] ?? $lastUsdt;
            $lastUsdt = $usdtRate;
            $bcv_history[] = [
                'rate' => $rateBCV,
                'usdt' => $usdtRate,
                'eur'  => $rateEUR,
                'rate_date' => $date
            ];
        }
    }

    $bcv_rate  = $current_usd;
    $eur_rate  = $current_eur;
    $rate_date = $current_date;
    $usdt_rate = $bcv_history[0]['usdt'] ?? null;

    // Variación
    $ayer_rate = $bcv_history[1]['rate'] ?? $bcv_rate;
    $diferencia = $bcv_rate - $ayer_rate;
    $porcentaje = $ayer_rate != 0 ? ($diferencia / $ayer_rate) * 100 : 0;
    $variacion_texto = $diferencia>0 ? "⬆ Subio +".number_format($diferencia,4,',','.')." Bs (".number_format($porcentaje,2,',','.')."%)" :
                       ($diferencia<0 ? "⬇ Bajo ".number_format($diferencia,4,',','.')." Bs (".number_format($porcentaje,2,',','.')."%)" :
                       "➖ Sin cambio");

    file_put_contents($tmpFile, json_encode([
        'bcv_rate' => $bcv_rate,
        'usdt_rate' => $usdt_rate,
        'eur_rate' => $eur_rate,
        'rate_date' => $rate_date,
        'bcv_history' => $bcv_history,
        'variacion_texto' => $variacion_texto,
        'variacion_valor' => $porcentaje
    ], JSON_PRETTY_PRINT));

    echo "bcv_data.json generado correctamente.\n";

} catch (Exception $e) {
    echo "Error: ".$e->getMessage()."\n";
}