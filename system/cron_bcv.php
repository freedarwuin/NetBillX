<?php
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

    // Monedas a procesar
    $monedas = [
        'bcv' => [
            'url_current' => "https://api.dolarvzla.com/public/bcv/exchange-rate",
            'url_list' => "https://api.dolarvzla.com/public/bcv/exchange-rate/list"
        ],
        'usdt' => [
            'url_current' => "https://api.dolarvzla.com/public/usdt/exchange-rate",
            'url_list' => "https://api.dolarvzla.com/public/usdt/exchange-rate/list"
        ],
        'eur' => [
            'url_current' => "https://api.dolarvzla.com/public/eur/exchange-rate",
            'url_list' => "https://api.dolarvzla.com/public/eur/exchange-rate/list"
        ]
    ];

    $data = [];

    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-28 days'));

    foreach ($monedas as $key => $urls) {
        $current = callAPI($urls['url_current'], $apiKey);
        $rate = $current['current']['usd'] ?? null; // Para USDT y EUR, ajustar si la API cambia la clave
        $rate_date = $current['current']['date'] ?? null;

        $history = [];
        $previousRate = null;

        $list = callAPI($urls['url_list'] . "?from=$from&to=$today", $apiKey);

        if (isset($list['rates']) && is_array($list['rates'])) {
            usort($list['rates'], fn($a, $b) => strcmp($b['date'], $a['date']));
            foreach ($list['rates'] as $row) {
                if (!isset($row['usd'], $row['date'])) continue;

                $r = (float)$row['usd'];
                $d = $row['date'];

                $change = 'same';
                if ($previousRate !== null) {
                    if ($r > $previousRate) $change = 'up';
                    elseif ($r < $previousRate) $change = 'down';
                }

                $history[] = [
                    'rate' => $r,
                    'rate_date' => $d,
                    'change' => $change
                ];

                $previousRate = $r;
            }
        }

        $data[$key] = [
            'rate' => $rate,
            'rate_date' => $rate_date,
            'history' => $history
        ];
    }

    file_put_contents($tmpFile, json_encode($data, JSON_PRETTY_PRINT));
    echo "JSON actualizado correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}