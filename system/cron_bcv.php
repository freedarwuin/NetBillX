<?php
/**
 * cron_bcv.php
 * Genera bcv_data.json con tasas BCV y USDT
 * Solo envía WhatsApp si cambia la tasa BCV
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config.php";

$tmpFile = __DIR__ . '/bcv_data.json';

try {

    // ===============================
    // 1️⃣ Conexión DB
    // ===============================
    $dbh = new PDO(
        "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // ===============================
    // 2️⃣ Obtener API Key
    // ===============================
    $stmt = $dbh->prepare("
        SELECT value
        FROM tbl_appconfig
        WHERE setting = 'dolarvzla_api_key'
        LIMIT 1
    ");
    $stmt->execute();
    $row = $stmt->fetch();

    if (!$row || empty($row['value'])) {
        throw new Exception("No existe 'dolarvzla_api_key'.");
    }

    $apiKey = trim($row['value']);

    // ===============================
    // 3️⃣ Función API
    // ===============================
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
            throw new Exception("API HTTP $httpCode");
        }

        return json_decode($response, true);
    }

    // ===============================
    // 4️⃣ Tasa actual
    // ===============================
    $bcvCurrent = callAPI(
        "https://api.dolarvzla.com/public/bcv/exchange-rate",
        $apiKey
    );

    if (!isset($bcvCurrent['current']['usd'])) {
        throw new Exception("No se pudo obtener tasa actual.");
    }

    $bcv_rate  = (float)$bcvCurrent['current']['usd'];
    $eur_rate  = isset($bcvCurrent['current']['eur']) ? (float)$bcvCurrent['current']['eur'] : null;
    $rate_date = substr($bcvCurrent['current']['date'], 0, 10);

    // ===============================
    // 5️⃣ Histórico
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    $bcvList  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtList = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    $bcv_history = [];
    $usdtIndexed = [];

    if (isset($usdtList['rates'])) {
        foreach ($usdtList['rates'] as $u) {
            $uDate = substr($u['date'], 0, 10);
            $usdtIndexed[$uDate] = isset($u['average']) ? (float)$u['average'] : null;
        }
    }

    if (isset($bcvList['rates']) && is_array($bcvList['rates'])) {

        usort($bcvList['rates'], function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        $lastUsdt = null;

        foreach ($bcvList['rates'] as $rowRate) {

            if (!isset($rowRate['usd'], $rowRate['date'])) continue;

            $rateBCV = (float)$rowRate['usd'];
            $rateEUR = isset($rowRate['eur']) ? (float)$rowRate['eur'] : null;
            $date    = substr($rowRate['date'], 0, 10);

            $usdtRate = $usdtIndexed[$date] ?? $lastUsdt;
            $lastUsdt = $usdtRate;

            $bcv_history[] = [
                'rate'      => $rateBCV,
                'usdt'      => $usdtRate,
                'eur'       => $rateEUR,
                'rate_date' => $date
            ];
        }
    }

    if (count($bcv_history) < 2) {
        throw new Exception("Histórico insuficiente para calcular variación.");
    }

    $usdt_rate = $bcv_history[0]['usdt'] ?? null;

    // ===============================
    // 6️⃣ Variación
    // ===============================
    $ayer_rate = $bcv_history[1]['rate'];
    $diferencia = $bcv_rate - $ayer_rate;
    $porcentaje = ($ayer_rate != 0) ? ($diferencia / $ayer_rate) * 100 : 0;

    $variacion_texto = "➖ Sin cambio";

    if ($diferencia > 0) {
        $variacion_texto = "⬆ Subió +" .
            number_format($diferencia, 4, ',', '.') .
            " Bs (" . number_format($porcentaje, 2, ',', '.') . "%)";
    } elseif ($diferencia < 0) {
        $variacion_texto = "⬇ Bajó " .
            number_format(abs($diferencia), 4, ',', '.') .
            " Bs (" . number_format(abs($porcentaje), 2, ',', '.') . "%)";
    }

    // ===============================
    // 7️⃣ Cambio
    // ===============================
    $old_rate = null;

    if (file_exists($tmpFile)) {
        $oldData = json_decode(file_get_contents($tmpFile), true);
        if (isset($oldData['bcv_rate'])) {
            $old_rate = (float)$oldData['bcv_rate'];
        }
    }

    $rate_changed = ($old_rate === null || round($old_rate, 4) !== round($bcv_rate, 4));

    // ===============================
    // 8️⃣ Guardar JSON
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate' => $bcv_rate
    ], JSON_PRETTY_PRINT));

    // ===============================
    // 9️⃣ WhatsApp
    // ===============================
    if ($rate_changed) {

        $response = file_get_contents($wa_url);
        if ($response === false) {
            throw new Exception("No se pudo enviar mensaje WhatsApp.");
        }

        echo "WhatsApp enviado porque la tasa cambió\n";

    } else {
        echo "La tasa no cambió. No se envía WhatsApp.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}