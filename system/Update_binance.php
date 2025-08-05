<?php
// Parámetros configurables
$asset = 'USDT';
$fiat = 'VES';
$tradeType = 'BUY';
$rows = 30; // Número de anuncios a consultar

function obtenerPreciosBinanceP2P($asset, $fiat, $tradeType, $rows = 30) {
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
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ Error CURL: " . curl_error($ch);
        curl_close($ch);
        return null;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['data']) || empty($data['data'])) {
        echo "⚠️ No se encontraron datos de ofertas P2P.";
        return null;
    }

    $precios = array_map(function ($oferta) {
        return floatval($oferta['adv']['price']);
    }, $data['data']);

    $min = min($precios);
    $max = max($precios);
    $avg = array_sum($precios) / count($precios);

    return [
        'min' => round($min, 2),
        'max' => round($max, 2),
        'avg' => round($avg, 2),
        'cantidad' => count($precios),
        'precios' => $precios
    ];
}

// Llamar función y mostrar resultados
$resultado = obtenerPreciosBinanceP2P($asset, $fiat, $tradeType, $rows);

if ($resultado) {
    echo "📊 Precios Binance P2P USDT/$fiat ($tradeType):\n";
    echo "🔻 Mínimo: {$resultado['min']} $fiat\n";
    echo "🔺 Máximo: {$resultado['max']} $fiat\n";
    echo "📈 Promedio: {$resultado['avg']} $fiat\n";
    echo "📦 Ofertas procesadas: {$resultado['cantidad']}\n";
} else {
    echo "❌ No se pudieron obtener los datos.\n";
}
