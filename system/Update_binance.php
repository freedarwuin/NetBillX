<?php
function obtenerPreciosBinanceP2P($asset = 'USDT', $fiat = 'VES', $tradeType = 'BUY', $rows = 50, $transAmount = null) {
    $url = "https://p2p.binance.com/bapi/c2c/v2/friendly/c2c/adv/search";
    $payload = [
        'asset' => $asset,
        'fiat' => $fiat,
        'merchantCheck' => false,
        'page' => 1,
        'payTypes' => [],
        'publisherType' => null,
        'rows' => $rows,
        'tradeType' => strtoupper($tradeType)
    ];
    if ($transAmount !== null) {
        $payload['transAmount'] = $transAmount;
    }

    $headers = [
        'Content-Type: application/json',
        'Origin: https://p2p.binance.com',
        'Referer: https://p2p.binance.com',
        'User-Agent: Mozilla/5.0'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ CURL error: " . curl_error($ch);
        curl_close($ch);
        return null;
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (empty($data['data'])) {
        echo "⚠️ No se encontraron ofertas P2P (array 'data' vacío).\n";
        return null;
    }

    $precios = array_map(fn($o) => floatval($o['adv']['price']), $data['data']);
    return [
        'min' => round(min($precios), 2),
        'max' => round(max($precios), 2),
        'avg' => round(array_sum($precios) / count($precios), 2),
        'count' => count($precios)
    ];
}

// Prueba con montos variados:
$result = obtenerPreciosBinanceP2P('USDT', 'VES', 'BUY', 30, 50000);
if ($result) {
    echo "✅ Ref BUY: Mín {$result['min']} Bs – Máx {$result['max']} Bs – Avg {$result['avg']} Bs, ofertas: {$result['count']}\n";
} else {
    echo "❌ Nada devuelto para BUY.\n";
}

// También prueba tipo SELL:
$result2 = obtenerPreciosBinanceP2P('USDT', 'VES', 'SELL', 30, 1000);
if ($result2) {
    echo "✅ Ref SELL: Mín {$result2['min']} Bs – Máx {$result2['max']} Bs – Avg {$result2['avg']} Bs, ofertas: {$result2['count']}\n";
} else {
    echo "❌ Nada devuelto para SELL.\n";
}
