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
    // 4️⃣ Obtener histórico BCV y USDT últimos 20 días
    // ===============================
    $today = date('Y-m-d');
    $from  = date('Y-m-d', strtotime('-20 days'));

    $bcvList  = callAPI("https://api.dolarvzla.com/public/bcv/exchange-rate/list?from=$from&to=$today", $apiKey);
    $usdtList = callAPI("https://api.dolarvzla.com/public/usdt/exchange-rate/list?from=$from&to=$today", $apiKey);

    $bcv_history = [];
    $previousBCV = null;
    $lastUsdt = null;

    if (isset($bcvList['rates']) && is_array($bcvList['rates'])) {

        // ordenar descendente (de más reciente a más antiguo)
        usort($bcvList['rates'], fn($a,$b) => strcmp($b['date'],$a['date']));

        foreach ($bcvList['rates'] as $row) {
            if (!isset($row['usd'], $row['date'])) continue;

            $rateBCV = (float)$row['usd'];
            $rateEUR = isset($row['eur']) ? (float)$row['eur'] : null;
            $date    = substr($row['date'],0,10); // YYYY-MM-DD

            // Rellenar USDT con último valor disponible
            $usdtRate = $lastUsdt;
            foreach ($usdtList['rates'] ?? [] as $u) {
                $uDate = substr($u['date'],0,10);
                if ($uDate === $date) {
                    $usdtRate = isset($u['average']) ? (float)$u['average'] : $lastUsdt;
                    break;
                }
            }
            $lastUsdt = $usdtRate;

            // Determinar cambio BCV
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
    // 5️⃣ Tasa actual (la más reciente)
    // ===============================
    $latest = $bcv_history[0];
    $bcv_rate  = $latest['rate'];
    $usdt_rate = $latest['usdt'];
    $eur_rate  = $latest['eur'];
    $rate_date = $latest['rate_date'];

    // ===============================
    // 6️⃣ Guardar JSON
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'    => $bcv_rate,
        'usdt_rate'   => $usdt_rate,
        'eur_rate'    => $eur_rate,
        'rate_date'   => $rate_date,
        'bcv_history' => $bcv_history
    ], JSON_PRETTY_PRINT));

    echo "BCV + USDT actualizado correctamente\n";

    // ===============================
    // 7️⃣ Enviar tasa por WhatsApp
    // ===============================

    // Obtener teléfono
    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting='phone' LIMIT 1");
    $stmt->execute();
    $phone = $stmt->fetchColumn();

    if (!$phone) {
        throw new Exception("No existe teléfono configurado en tbl_appconfig.");
    }

    // Obtener plantilla wa_url
    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting='wa_url' LIMIT 1");
    $stmt->execute();
    $wa_url_template = $stmt->fetchColumn();

    if (!$wa_url_template) {
        throw new Exception("No existe wa_url configurado.");
    }

    // Formatear tasas
    $bcv_format  = number_format($bcv_rate, 4, ',', '.');
    $usdt_format = $usdt_rate ? number_format($usdt_rate, 4, ',', '.') : 'N/D';

    // Construir mensaje
    $message = "💱 Tasa Oficial BCV\n"
             . "Fecha: $rate_date\n"
             . "BCV: $bcv_format Bs/USD\n"
             . "USDT: $usdt_format Bs/USD\n"
             . "Sistema NetBillX";

    // Codificar mensaje
    $message_encoded = urlencode($message);

    // Reemplazar variables en URL
    $wa_url = str_replace(
        ['[number]', '[text]'],
        [$phone, $message_encoded],
        $wa_url_template
    );

    // Enviar petición
    $response = file_get_contents($wa_url);

    if ($response === false) {
        throw new Exception("No se pudo enviar mensaje WhatsApp.");
    }

    echo "WhatsApp enviado correctamente\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}