<?php
function obtenerPreciosP2PBinanceUSDT($fiat = 'VES', $tradeType = 'BUY', $rows = 50)
{
    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";

    $payload = [
        "asset" => "USDT",
        "fiat" => $fiat,
        "merchantCheck" => false,
        "page" => 1,
        "payTypes" => [],
        "publisherType" => null,
        "rows" => $rows,
        "tradeType" => strtoupper($tradeType) // BUY o SELL
    ];

    $headers = [
        'Content-Type: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        'Origin: https://p2p.binance.com',
        'Referer: https://p2p.binance.com/'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo '❌ Error CURL: ' . curl_error($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);

    if (empty($data['data'])) {
        echo "⚠️ No se encontraron datos de ofertas P2P.\n";
        return null;
    }

    // Extraer precios
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

// ▶️ Ejecutar la función
$resultado = obtenerPreciosP2PBinanceUSDT('VES', 'BUY', 30); // Puedes cambiar a 'SELL'

if ($resultado) {
    echo "📊 Precios Binance P2P USDT/VES (BUY):\n";
    echo "🔻 Mínimo: " . $resultado['min'] . " Bs\n";
    echo "🔺 Máximo: " . $resultado['max'] . " Bs\n";
    echo "📈 Promedio: " . $resultado['avg'] . " Bs\n";
    echo "📦 Ofertas procesadas: " . $resultado['cantidad'] . "\n";
} else {
    echo "❌ No se pudieron obtener los datos.";
}
