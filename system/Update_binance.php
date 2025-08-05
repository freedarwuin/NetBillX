<?php
header('Content-Type: application/json');

function obtenerPreciosBinanceP2P($asset, $fiat, $tradeType = 'BUY', $rows = 20) {
    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";

    $payload = [
        "asset" => $asset,
        "fiat" => $fiat,
        "merchantCheck" => false,
        "page" => 1,
        "payTypes" => [],
        "publisherType" => null,
        "rows" => $rows,
        "tradeType" => strtoupper($tradeType)
    ];

    $headers = [
        "Content-Type: application/json",
        "Origin: https://p2p.binance.com",
        "Referer: https://p2p.binance.com",
        "User-Agent: Mozilla/5.0"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return [
            'fiat' => $fiat,
            'error' => 'CURL Error: ' . curl_error($ch)
        ];
    }

    curl_close($ch);
    $data = json_decode($response, true);

    if (empty($data['data'])) {
        return [
            'fiat' => $fiat,
            'error' => 'No se encontraron ofertas'
        ];
    }

    $precios = array_map(fn($o) => floatval($o['adv']['price']), $data['data']);

    return [
        'fiat' => $fiat,
        'min' => round(min($precios), 2),
        'max' => round(max($precios), 2),
        'avg' => round(array_sum($precios) / count($precios), 2),
        'ofertas' => count($precios)
    ];
}

// 🔁 Monedas a probar (puedes agregar más)
$monedas = ['VES', 'USD', 'COP', 'ARS', 'MXN'];

$resultados = [];

foreach ($monedas as $fiat) {
    $r = obtenerPreciosBinanceP2P('USDT', $fiat, 'BUY', 30);
    $resultados[] = $r;
}

// 🔽 Mostrar resultados como JSON
echo json_encode([
    'timestamp' => date('Y-m-d H:i:s'),
    'asset' => 'USDT',
    'resultados' => $resultados
], JSON_PRETTY_PRINT);
