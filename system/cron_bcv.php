<?php
include "../config.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    $dbh = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $apiKey = "TU_NUEVA_API_KEY";
    $url = "https://api.dolarvzla.com/public/bcv/exchange-rate";

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "x-dolarvzla-key: $apiKey"
        ]
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception("Error cURL: " . curl_error($ch));
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!$data) {
        throw new Exception("JSON inválido");
    }

    // 🔎 Detectar tasa automáticamente
    $rate = null;

    if (isset($data['rate'])) {
        $rate = $data['rate'];
    } elseif (isset($data['price'])) {
        $rate = $data['price'];
    } elseif (isset($data['data']['rate'])) {
        $rate = $data['data']['rate'];
    } elseif (isset($data['data'][0]['rate'])) {
        $rate = $data['data'][0]['rate'];
    }

    if (!$rate) {
        throw new Exception("No se encontró el campo de tasa en la respuesta");
    }

    echo "TASA DETECTADA: " . $rate . "<br>";

    $today = date('Y-m-d');

    $stmt = $dbh->prepare("
        INSERT INTO bcv_rate (rate, rate_date, created_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            rate = VALUES(rate),
            updated_at = NOW()
    ");

    $stmt->execute([$rate, $today]);

    echo "✅ Guardado correctamente en base de datos.";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}