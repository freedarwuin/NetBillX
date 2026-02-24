<?php
include "../config.php";

try {

    $dbh = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

    // ================================
    // CONFIG API
    // ================================
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
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $httpCode !== 200) {
        error_log("Error API DolarVzla: " . curl_error($ch));
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    $data = json_decode($response, true);

    if (!isset($data['rate'])) {
        error_log("Respuesta inesperada API DolarVzla");
        exit;
    }

    $rate = $data['rate'];
    $today = date('Y-m-d');

    // ================================
    // INSERT / UPDATE
    // ================================
    $stmt = $dbh->prepare("
        INSERT INTO bcv_rate (rate, rate_date, created_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            rate = VALUES(rate),
            updated_at = NOW()
    ");

    $stmt->execute([$rate, $today]);

} catch (Exception $e) {
    error_log($e->getMessage());
}