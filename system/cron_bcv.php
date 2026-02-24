<?php
include "../config.php";

$tmpFile = __DIR__ . '/bcv_data.json';

try {

    // ===============================
    // 1️⃣ Si ya existe tasa de hoy → salir
    // ===============================
    if (file_exists($tmpFile)) {
        $existing = json_decode(file_get_contents($tmpFile), true);

        if (
            isset($existing['rate_date']) &&
            $existing['rate_date'] === date('Y-m-d')
        ) {
            echo "Ya actualizado hoy\n";
            exit;
        }
    }

    // ===============================
    // 2️⃣ Conexión DB
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
    // 3️⃣ Obtener API Key
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
    // 4️⃣ Llamar API (UNA SOLA VEZ)
    // ===============================
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.dolarvzla.com/public/bcv/exchange-rate",
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
    $retryAfter = curl_getinfo($ch, CURLINFO_RETRY_AFTER);
    curl_close($ch);

    // ===============================
    // 5️⃣ Manejo inteligente errores
    // ===============================
    if ($httpCode === 429) {
        $wait = $retryAfter ?: 3600;
        throw new Exception("Rate limit activo. Esperar {$wait} segundos.");
    }

    if ($httpCode !== 200) {
        throw new Exception("API respondió con HTTP $httpCode");
    }

    $data = json_decode($response, true);

    if (!isset($data['current']['usd'])) {
        throw new Exception("Respuesta inesperada de la API.");
    }

    $rate = (float)$data['current']['usd'];
    $date = date('Y-m-d');

    // ===============================
    // 6️⃣ Guardar JSON simple
    // ===============================
    file_put_contents($tmpFile, json_encode([
        'bcv_rate'  => $rate,
        'rate_date' => $date
    ], JSON_PRETTY_PRINT));

    echo "BCV actualizado correctamente: {$rate}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}