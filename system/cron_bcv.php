<?php
/**
 * cron_bcv.php
 * Genera bcv_data.json con tasas BCV y USDT
 * Ejecutar vía cron cada hora o cada día
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
    // 2️⃣ Obtener API Key desde tbl_appconfig
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
        throw new Exception("No existe 'dolarvzla_api_key' en tbl_appconfig.");
    }

    $apiKey = trim($row['value']);

    // ===============================
    // 3️⃣ Función para llamar API
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
            throw new Exception("API respondió con código HTTP $httpCode");
        }

        return json_decode($response, true);
    }

    // ===============================
    // 4️⃣ Obtener tasa actual (CURRENT)
    // ===============================
    $bcvCurrent = callAPI(
        "https://api.dolarvzla.com/public/bcv/exchange-rate",
        $apiKey
    );

    if (!isset($bcvCurrent['current']['usd'])) {
        throw new Exception("No se pudo obtener tasa actual BCV.");
    }

    $current_usd  = (float)$bcvCurrent['current']['usd'];
    $current_eur  = isset($bcvCurrent['current']['eur']) ? (float)$bcvCurrent['current']['eur'] : null;
    $current_date = substr($bcvCurrent['current']['date'], 0, 10);


    // ===============================
    // 5️⃣ Obtener histórico últimos 20 días
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    $bcvList  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtList = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    $bcv_history = [];
    $previousBCV = null;

    // ===============================
    // Indexar USDT por fecha (OPTIMIZADO)
    // ===============================
    $usdtIndexed = [];
    if (isset($usdtList['rates'])) {
        foreach ($usdtList['rates'] as $u) {
            $uDate = substr($u['date'], 0, 10);
            $usdtIndexed[$uDate] = isset($u['average']) ? (float)$u['average'] : null;
        }
    }

    // ===============================
    // Procesar BCV histórico
    // ===============================
    if (isset($bcvList['rates']) && is_array($bcvList['rates'])) {

        usort($bcvList['rates'], fn($a,$b) => strcmp($b['date'],$a['date']));

        $lastUsdt = null;

        foreach ($bcvList['rates'] as $row) {

            if (!isset($row['usd'], $row['date'])) continue;

            $rateBCV = (float)$row['usd'];
            $rateEUR = isset($row['eur']) ? (float)$row['eur'] : null;
            $date    = substr($row['date'], 0, 10);

            // Obtener USDT indexado
            $usdtRate = $usdtIndexed[$date] ?? $lastUsdt;
            $lastUsdt = $usdtRate;

            // Determinar cambio
            $change = 'same';
            if ($previousBCV !== null) {
                if ($rateBCV > $previousBCV) $change = 'up';
                elseif ($rateBCV < $previousBCV) $change = 'down';
            }

            $bcv_history[] = [
                'rate'      => $rateBCV,
                'usdt'      => $usdtRate,
                'eur'       => $rateEUR,
                'rate_date' => $date,
                'change'    => $change
            ];

            $previousBCV = $rateBCV;
        }
    }

    if (count($bcv_history) === 0) {
        throw new Exception("No se pudo obtener histórico BCV.");
    }


    // ===============================
    // 6️⃣ Tasa principal desde CURRENT
    // ===============================
    $bcv_rate  = $current_usd;
    $eur_rate  = $current_eur;
    $rate_date = $current_date;

    // USDT lo tomamos del día más reciente del histórico
    $usdt_rate = $bcv_history[0]['usdt'] ?? null;


    // ===============================
    // 7️⃣ Guardar JSON
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'    => $bcv_rate,
        'usdt_rate'   => $usdt_rate,
        'eur_rate'    => $eur_rate,
        'rate_date'   => $rate_date,
        'bcv_history' => $bcv_history
    ], JSON_PRETTY_PRINT));

    // ===============================
    // 7️⃣ Enviar tasa por WhatsApp
    // ===============================

    // Obtener phone, country_code_phone y wa_url
    $stmt = $dbh->prepare("
        SELECT setting, value
        FROM tbl_appconfig
        WHERE setting IN ('phone','country_code_phone','wa_url')
    ");
    $stmt->execute();
    $configData = $stmt->fetchAll();

    $phone = null;
    $countryCode = null;
    $wa_url_template = null;

    foreach ($configData as $row) {
        if ($row['setting'] === 'phone') {
            $phone = preg_replace('/\D/', '', $row['value']); // solo números
        }
        if ($row['setting'] === 'country_code_phone') {
            $countryCode = preg_replace('/\D/', '', $row['value']);
        }
        if ($row['setting'] === 'wa_url') {
            $wa_url_template = $row['value'];
        }
    }

    if (!$phone) {
        throw new Exception("No existe teléfono configurado en tbl_appconfig.");
    }

    if (!$countryCode) {
        throw new Exception("No existe country_code_phone configurado.");
    }

    if (!$wa_url_template) {
        throw new Exception("No existe wa_url configurado.");
    }

    // ===============================
    // Normalizar número
    // ===============================

    // Si ya comienza con código país
    if (strpos($phone, $countryCode) === 0) {
        $normalizedPhone = $phone;
    } else {

        // Si comienza con 0 → eliminar primer 0
        if (strpos($phone, '0') === 0) {
            $phone = substr($phone, 1);
        }

        $normalizedPhone = $countryCode . $phone;
    }

    // ===============================
    // Formatear tasas
    // ===============================
    $bcv_format  = number_format($bcv_rate, 4, ',', '.');
    $usdt_format = $usdt_rate ? number_format($usdt_rate, 4, ',', '.') : 'N/D';

    // ===============================
    // Formatear fecha venezolana
    // ===============================
    $dateObj = new DateTime($rate_date);

    $dias = [
        'Sunday'    => 'Domingo',
        'Monday'    => 'Lunes',
        'Tuesday'   => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday'  => 'Jueves',
        'Friday'    => 'Viernes',
        'Saturday'  => 'Sábado'
    ];

    $dayName = $dias[$dateObj->format('l')];

    // Formato venezolano
    $fecha_ve = $dateObj->format('d/m/Y');

    // ===============================
    // Construir mensaje
    // ===============================
    $message = "💱 *Actualizacion Tasa Oficial BCV*\n\n"
             . "📅 *$dayName $fecha_ve - 07:00 AM*\n\n"
             . "💵 *Dolar BCV:* $bcv_format Bs/USD\n"
             . ($usdt_rate ? "💰 *USDT Promedio:* $usdt_format Bs/USD\n" : "")
             . "\n"
             . "📊 Variacion respecto al dia anterior: "
             . ($bcv_history[0]['change'] === 'up' ? "⬆ Subio" :
                ($bcv_history[0]['change'] === 'down' ? "⬇ Bajo" : "➖ Sin cambio"))
             . "\n\n"
             . "🏢 Sistema NetBillX\n"
             . "Grafica y datos actualizados automaticamente.";

    $message_encoded = urlencode($message);

    // ===============================
    // Generar URL final
    // ===============================
    $wa_url = str_replace(
        ['[number]', '[text]'],
        [$normalizedPhone, $message_encoded],
        $wa_url_template
    );

    // ===============================
    // Enviar mensaje
    // ===============================
    $response = file_get_contents($wa_url);

    if ($response === false) {
        throw new Exception("No se pudo enviar mensaje WhatsApp.");
    }

    echo "WhatsApp enviado correctamente al $normalizedPhone\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}