<?php

$apiKey = "e87ea1d5447c431f93e6088c963b9f6f01a416edbe5a810dfc8e8d7149bafd0d";

function callAPI($url, $apiKey) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "x-dolarvzla-key: $apiKey"
        ]
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response ? json_decode($response, true) : null;
}

# ==========================
# 1️⃣ ACTUAL
# ==========================

$current = callAPI(
    "https://api.dolarvzla.com/public/bcv/exchange-rate",
    $apiKey
);

$bcv_rate = $current['current']['usd'] ?? null;

# ==========================
# 2️⃣ HISTÓRICO (últimos 9 días)
# ==========================

$today = date('Y-m-d');
$from = date('Y-m-d', strtotime('-8 days'));

$list = callAPI(
    "https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today",
    $apiKey
);

$bcv_history = [];

if (isset($list['rates']) && is_array($list['rates'])) {

    // Ordenar por fecha descendente
    usort($list['rates'], function($a, $b) {
        return strcmp($b['date'], $a['date']);
    });

    $lastRate = null;

    foreach ($list['rates'] as $row) {

        $rate = $row['usd'];
        $date = $row['date'];

        $change = 'same';

        if ($lastRate !== null) {
            if ($rate > $lastRate) {
                $change = 'up';
            } elseif ($rate < $lastRate) {
                $change = 'down';
            }
        }

        $bcv_history[] = [
            'rate' => $rate,
            'rate_date' => $date,
            'change' => $change
        ];

        $lastRate = $rate;
    }
}

$smarty->assign('bcv_rate', $bcv_rate);
$smarty->assign('bcv_history', $bcv_history);