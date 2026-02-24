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

    $apiKey = "e87ea1d5447c431f93e6088c963b9f6f01a416edbe5a810dfc8e8d7149bafd0d";
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

    if (!isset($data['current']['usd'], $data['current']['date'])) {
        throw new Exception("Estructura inesperada en la API");
    }

    $rate = $data['current']['usd'];
    $rateDate = $data['current']['date'];

    echo "Tasa BCV detectada: $rate <br>";
    echo "Fecha: $rateDate <br>";

    $stmt = $dbh->prepare("
        INSERT INTO bcv_rate (rate, rate_date, created_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            rate = VALUES(rate),
            updated_at = NOW()
    ");

    $stmt->execute([$rate, $rateDate]);

    echo "✅ Guardado correctamente.";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
}