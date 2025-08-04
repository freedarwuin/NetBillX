<?php
include "../config.php";

try {
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Obtener tasa de la API
    $json = file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");
    $data = json_decode($json, true);
    $rate = $data['promedio'] ?? $data['valor'] ?? null;

    if (!$rate) {
        echo "❌ Error: No se pudo obtener la tasa de la API";
        exit;
    }

    $today = date('Y-m-d');

    // 🔒 Usamos ON DUPLICATE KEY para evitar duplicados
    $stmt = $dbh->prepare("
        INSERT INTO bcv_rate (rate, rate_date, created_at)
        VALUES (?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        rate = IF(rate <> VALUES(rate), VALUES(rate), rate),
        updated_at = IF(rate <> VALUES(rate), NOW(), updated_at)
    ");
    $stmt->execute([$rate, $today]);

    // Verificar si se insertó o actualizó
    if ($stmt->rowCount() > 0) {
        echo "✅ Tasa BCV procesada: $rate Bs";
    } else {
        echo "ℹ️ La tasa BCV de hoy ($rate Bs) ya estaba registrada. No se modificó.";
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
