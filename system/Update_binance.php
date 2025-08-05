<?php
header('Content-Type: application/json');

function obtenerPreciosBinanceP2P($asset, $fiat, $tradeType = 'BUY', $rows = 20, $payTypes = []) {
    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";

    $payload = [
        "asset" => $asset,
        "fiat" => $fiat,
        "merchantCheck" => false,
        "page" => 1,
        "payTypes" => $payTypes,
        "publisherType" => null,
        "rows" => $rows,
        "tradeType" => strtoupper($tradeType)
    ];

    $headers = [
        "Content-Type: application/json",
        "Origin: https://p2p.binance.com",
        "Referer: https://p2p.binance.com",
        "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_4 like Mac OS X)"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => 'CURL Error: ' . curl_error($ch)];
    }

    curl_close($ch);
    $data = json_decode($response, true);

    if (empty($data['data'])) {
        return ['error' => "Sin resultados"];
    }

    $precios = array_map(fn($o) => floatval($o['adv']['price']), $data['data']);

    return [
        'min' => round(min($precios), 2),
        'max' => round(max($precios), 2),
        'avg' => round(array_sum($precios) / count($precios), 2),
        'ofertas' => count($precios),
        'payType' => $payTypes[0] ?? 'TODOS'
    ];
}

// ▶️ Lista de métodos de pago comunes en Venezuela
$metodosPago = [
    'ZINLI',
    'BANPLUS',
    'MERCANTIL',
    'PROVINCIAL',
    'BOD',
    'BNC',
    'BFC',
    '100% BANCO',
    'SOFITASA',
    'TRANSFERENCIA',
    'MOBILE_PAYMENT',
    'PAYPAL'
];

$resultados = [];

foreach ($metodosPago as $metodo) {
    $r = obtenerPreciosBinanceP2P('USDT', 'VES', 'BUY', 20, [$metodo]);
    $resultados[$metodo] = $r;
}

// También probar sin método de pago para comparar
$resultados['TODOS'] = obtenerPreciosBinanceP2P('USDT', 'VES', 'BUY', 20, []);

echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'asset' => 'USDT',
    'fiat' => 'VES',
    'tradeType' => 'BUY',
    'resultados' => $resultados
], JSON_PRETTY_PRINT);
