<?php
include "../config.php";

try {
    $dbh = new PDO(
        "mysql:host=$db_host;dbname=$db_name",
        $db_user,
        $db_pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Obtener tasa desde la API
    $json = file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");
    $data = json_decode($json, true);
    $rate = $data['promedio'] ?? $data['valor'] ?? null;

    if (!$rate) {
        exit; // Si no hay tasa, termina silenciosamente
    }

    $today = date('Y-m-d');

    // Insertar o actualizar tasa
    $stmt = $dbh->prepare("
        INSERT INTO bcv_rate (rate, rate_date, created_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            rate = VALUES(rate),
            updated_at = NOW()
    ");

    $stmt->execute([$rate, $today]);

} catch (PDOException $e) {
    // Cron silencioso (sin mostrar errores en navegador)
    error_log($e->getMessage());
}