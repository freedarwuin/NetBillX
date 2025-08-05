<?php

function obtenerPreciosP2PBinanceUSDT($fiat = 'VES', $tradeType = 'BUY', $rows = 50) {
    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";

    $payload = [
        "page" => 1,
        "rows" => $rows,
        "payTypes" => [],
        "asset" => "USDT",
        "tradeType" => $tradeType, // BUY o SELL
        "fiat" => $fiat,
        "publisherType" => null
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0'
    ]);

    $response = curl_exec($ch);
    if(curl_errno($ch)){
        echo 'Error en CURL: ' . curl_error($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (empty($data['data'])) {
        echo "No se encontraron datos de ofertas P2P.\n";
        return null;
    }

    $precios = array_map(function($offer) {
        return floatval($offer['adv']['price']);
    }, $data['data']);

    $minPrice = min($precios);
    $maxPrice = max($precios);

    return [
        'min' => $minPrice,
        'max' => $maxPrice,
        'all_prices' => $precios
    ];
}

// Uso:
$precios = obtenerPreciosP2PBinanceUSDT('VES', 'BUY', 50);
if ($precios) {
    echo "Precio mínimo USDT P2P Binance: {$precios['min']} VES\n";
    echo "Precio máximo USDT P2P Binance: {$precios['max']} VES\n";
}
