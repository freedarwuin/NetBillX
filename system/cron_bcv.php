<?php
/**
 * Script para obtener la tasa BCV desde la API
 * y guardarla en JSON temporal.
 * Ejecutar vía cron:
 * * * * * php /ruta/a/system/cron_bcv.php
 */

$apiKey = "e87ea1d5447c431f93e6088c963b9f6f01a416edbe5a810dfc8e8d7149bafd0d";
$tmpFile = __DIR__ . '/bcv_data.json'; // archivo temporal

/**
 * Función para llamar a la API
 */
function callAPI($url, $apiKey) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "x-dolarvzla-key: $apiKey"
        ],
    ]);
    $response = curl_exec($ch);
    if ($response === false) {
        return null;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode !== 200) return null;
    return json_decode($response, true);
}

// ===============================
// 1️⃣ Tasa actual
// ===============================
$current = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate", $apiKey);
$bcv_rate = isset($current['current']['usd']) ? (float)$current['current']['usd'] : null;

// ===============================
// 2️⃣ Histórico 9 días
// ===============================
$today = date('Y-m-d');
$from  = date('Y-m-d', strtotime('-8 days'));
$list = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);

$bcv_history = [];
if (isset($list['rates']) && is_array($list['rates'])) {
    $previousRate = null;
    usort($list['rates'], function($a,$b){ return strcmp($b['date'],$a['date']); });
    foreach($list['rates'] as $row){
        if(!isset($row['usd'],$row['date'])) continue;
        $rate = (float)$row['usd'];
        $date = $row['date'];
        $change = 'same';
        if($previousRate!==null){
            if($rate>$previousRate) $change='up';
            elseif($rate<$previousRate) $change='down';
        }
        $bcv_history[] = ['rate'=>$rate,'rate_date'=>$date,'change'=>$change];
        $previousRate = $rate;
    }
}

// ===============================
// 3️⃣ Guardar en JSON temporal
// ===============================
file_put_contents($tmpFile, json_encode([
    'bcv_rate' => $bcv_rate,
    'bcv_history' => $bcv_history
]));