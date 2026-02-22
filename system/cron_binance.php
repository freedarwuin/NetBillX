<?php
header('Content-Type: application/json');

// 🕒 Duración del caché (en segundos)
$cacheFile = __DIR__ . '/cache/ves_buy.json';
$cacheTTL = 60; // 1 minuto

if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
    echo file_get_contents($cacheFile);
    exit;
}

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
        "accept: application/json, text/plain, */*",
        "accept-language: es-ES,es;q=0.9",
        "content-type: application/json",
        "origin: https://p2p.binance.com",
        "referer: https://p2p.binance.com/",
        "user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ['error' => 'CURL Error: ' . curl_error($ch)];
    }

    curl_close($ch);
    $data = json_decode($response, true);

    if (empty($data['data'])) {
        return ['error' => 'Sin resultados'];
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

// Métodos de pago conocidos para Venezuela
$metodos = ['ZINLI', 'BANPLUS', 'MERCANTIL', 'BNC', 'BFC', 'PROVINCIAL', 'PAYPAL', 'TRANSFERENCIA'];

$resultados = [];

foreach ($metodos as $metodo) {
    $resultados[$metodo] = obtenerPreciosBinanceP2P('USDT', 'VES', 'BUY', 20, [$metodo]);
}

// También consultar sin filtrar método de pago
$resultados['TODOS'] = obtenerPreciosBinanceP2P('USDT', 'VES', 'BUY', 20, []);

$output = [
    'timestamp' => date('Y-m-d H:i:s'),
    'asset' => 'USDT',
    'fiat' => 'VES',
    'tradeType' => 'BUY',
    'resultados' => $resultados
];

// Guardar en caché
file_put_contents($cacheFile, json_encode($output, JSON_PRETTY_PRINT));

// Mostrar al cliente
echo json_encode($output, JSON_PRETTY_PRINT);
