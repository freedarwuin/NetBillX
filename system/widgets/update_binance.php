<?php

class update_binance
{
    public static function getWidget($data)
    {
        global $ui;

        $cacheFile = __DIR__ . '/cache/binance_ves_buy.json';
        $cacheTTL  = 60; // 1 minuto

        // Crear carpeta cache si no existe
        if (!is_dir(__DIR__ . '/cache')) {
            mkdir(__DIR__ . '/cache', 0755, true);
        }

        // Si existe cache válido, usarlo
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheTTL)) {
            $json = file_get_contents($cacheFile);
            $data = json_decode($json, true);
        } else {

            $metodos = ['ZINLI', 'BANPLUS', 'MERCANTIL', 'BNC', 'BFC', 'PROVINCIAL'];

            $resultados = [];

            foreach ($metodos as $metodo) {
                $resultados[$metodo] = self::obtenerPreciosBinanceP2P(
                    'USDT',
                    'VES',
                    'BUY',
                    20,
                    [$metodo]
                );
            }

            $resultados['TODOS'] = self::obtenerPreciosBinanceP2P(
                'USDT',
                'VES',
                'BUY',
                20,
                []
            );

            $data = [
                'timestamp' => date('Y-m-d H:i:s'),
                'resultados' => $resultados
            ];

            file_put_contents($cacheFile, json_encode($data));
        }

        // Promedio general
        $promedioGeneral = $data['resultados']['TODOS']['avg'] ?? null;

        $ui->assign('binance_avg', $promedioGeneral);
        $ui->assign('binance_data', $data['resultados']);
        $ui->assign('binance_time', $data['timestamp']);

        return $ui->fetch('widget/binance_rate.tpl');
    }

    private static function obtenerPreciosBinanceP2P($asset, $fiat, $tradeType, $rows, $payTypes)
    {
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
            "accept: application/json",
            "content-type: application/json",
            "origin: https://p2p.binance.com",
            "referer: https://p2p.binance.com/",
            "user-agent: Mozilla/5.0"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            curl_close($ch);
            return ['error' => 'Error CURL'];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (empty($data['data'])) {
            return ['error' => 'Sin resultados'];
        }

        $precios = array_map(function($o){
            return floatval($o['adv']['price']);
        }, $data['data']);

        return [
            'min' => round(min($precios), 2),
            'max' => round(max($precios), 2),
            'avg' => round(array_sum($precios) / count($precios), 2),
            'ofertas' => count($precios),
            'payType' => $payTypes[0] ?? 'TODOS'
        ];
    }
}