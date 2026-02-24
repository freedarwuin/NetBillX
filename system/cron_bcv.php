<?php

$apiKey = "TU_NUEVA_API_KEY_AQUI";

/**
 * Llamada segura a API DolarVzla
 */
function callAPI($url, $apiKey)
{
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

    if ($response === false) {
        curl_close($ch);
        return null;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return null;
    }

    return json_decode($response, true);
}

# ===================================
# 1️⃣ TASA ACTUAL
# ===================================

$bcv_rate = null;

$current = callAPI(
    "https://api.dolarvzla.com/public/bcv/exchange-rate",
    $apiKey
);

if (isset($current['current']['usd'])) {
    $bcv_rate = $current['current']['usd'];
}

# ===================================
# 2️⃣ HISTÓRICO (9 días incluyendo hoy)
# ===================================

$bcv_history = [];

$today = date('Y-m-d');
$from  = date('Y-m-d', strtotime('-8 days'));

$list = callAPI(
    "https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today",
    $apiKey
);

if (isset($list['rates']) && is_array($list['rates'])) {

    // Ordenar por fecha descendente
    usort($list['rates'], function ($a, $b) {
        return strcmp($b['date'], $a['date']);
    });

    $previousRate = null;

    foreach ($list['rates'] as $row) {

        if (!isset($row['usd'], $row['date'])) {
            continue;
        }

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
}

# ===================================
# 3️⃣ Enviar a Smarty
# ===================================

$smarty->assign('bcv_rate', $bcv_rate);
$smarty->assign('bcv_history', $bcv_history);