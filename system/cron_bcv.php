<?php
// ===================================
// CONFIGURACIÓN
// ===================================
$apiKey = "TU_NUEVA_API_KEY_AQUI"; // Reemplaza con tu clave real
$debug  = true; // Cambiar a false en producción

// ===================================
// FUNCIÓN DE LLAMADA A LA API
// ===================================
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
        // Solo para depuración si hay problemas SSL, no usar en producción
        // CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $err = curl_error($ch);
        curl_close($ch);
        if ($GLOBALS['debug']) echo "CURL Error: $err\n";
        return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        if ($GLOBALS['debug']) echo "HTTP Code: $httpCode\nResponse: $response\n";
        return null;
    }

    $data = json_decode($response, true);

    if ($data === null) {
        if ($GLOBALS['debug']) echo "Error decodificando JSON: $response\n";
    }

    return $data;
}

// ===================================
// 1️⃣ TASA ACTUAL
// ===================================
$bcv_rate = null;

$current = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate", $apiKey);

if ($debug) {
    echo "<pre>Respuesta tasa actual:\n";
    var_dump($current);
    echo "</pre>";
}

if (isset($current['current']['usd'])) {
    $bcv_rate = (float) $current['current']['usd'];
} else {
    if ($debug) echo "No se encontró la tasa actual USD en la respuesta.\n";
}

// ===================================
// 2️⃣ HISTÓRICO (9 días incluyendo hoy)
// ===================================
$bcv_history = [];

$today = date('Y-m-d');
$from  = date('Y-m-d', strtotime('-8 days'));

$list = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);

if ($debug) {
    echo "<pre>Respuesta histórico:\n";
    var_dump($list);
    echo "</pre>";
}

if (isset($list['rates']) && is_array($list['rates']) && count($list['rates']) > 0) {

    // Ordenar por fecha descendente
    usort($list['rates'], function ($a, $b) {
        return strcmp($b['date'], $a['date']);
    });

    $previousRate = null;

    foreach ($list['rates'] as $row) {

        if (!isset($row['usd'], $row['date'])) continue;

        $rate = (float) $row['usd'];
        $date = $row['date'];

        $change = 'same';
        if ($previousRate !== null) {
            if ($rate > $previousRate) {
                $change = 'up';
            } elseif ($rate < $previousRate) {
                $change = 'down';
            }
        }

        $bcv_history[] = [
            'rate' => $rate,
            'rate_date' => $date,
            'change' => $change
        ];

        $previousRate = $rate;
    }

} else {
    if ($debug) echo "No se encontraron datos históricos de tasas.\n";
}

// ===================================
// 3️⃣ ASIGNAR A SMARTY
// ===================================
if (isset($smarty)) {
    $smarty->assign('bcv_rate', $bcv_rate);
    $smarty->assign('bcv_history', $bcv_history);
}

// ===================================
// 4️⃣ OPCIONAL: DEPURACIÓN FINAL
// ===================================
if ($debug) {
    echo "<pre>Tasa actual final: $bcv_rate\n";
    echo "Histórico:\n";
    var_dump($bcv_history);
    echo "</pre>";
}