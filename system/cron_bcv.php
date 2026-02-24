<?php
/**
 * Script para obtener la tasa BCV y asignar a Smarty directamente
 * Ubicación: system/cron_bcv.php
 */

require '../config.php'; // Configuración de Smarty

$apiKey = "TU_API_KEY_REAL";

/**
 * Obtener datos BCV
 */
function getBCVData($apiKey, $debug = false)
{
    $bcv_rate = null;
    $bcv_history = [];

    $callAPI = function($url) use ($apiKey, $debug) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "x-dolarvzla-key: $apiKey"
            ],
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            $err = curl_error($ch);
            curl_close($ch);
            if ($debug) echo "CURL Error: $err\n";
            return null;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode !== 200) {
            if ($debug) echo "HTTP Code: $httpCode\nResponse: $response\n";
            return null;
        }
        return json_decode($response, true);
    };

    // Tasa actual
    $current = $callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate");
    if (isset($current['current']['usd'])) {
        $bcv_rate = (float)$current['current']['usd'];
    }

    // Histórico 9 días
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-8 days'));
    $list = $callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today");

    if (isset($list['rates']) && is_array($list['rates'])) {
        $previousRate = null;
        usort($list['rates'], function($a,$b){ return strcmp($b['date'],$a['date']); });
        foreach($list['rates'] as $row){
            if(!isset($row['usd'],$row['date'])) continue;
            $rate = (float)$row['usd'];
            $date = $row['date'];
            $change = 'same';
            if($previousRate!==null){
                if($rate>$previousRate) $change='up';
                elseif($rate<$previousRate) $change='down';
            }
            $bcv_history[] = ['rate'=>$rate,'rate_date'=>$date,'change'=>$change];
            $previousRate = $rate;
        }
    }

    return ['bcv_rate'=>$bcv_rate,'bcv_history'=>$bcv_history];
}

// Obtener datos BCV
$bcvData = getBCVData($apiKey);

// Asignar a Smarty
$smarty->assign('bcv_rate', $bcvData['bcv_rate']);
$smarty->assign('bcv_history', $bcvData['bcv_history']);

// Renderizar plantilla directamente (opcional)
$smarty->display('../ui/ui/widget/bcv_rate.tpl');