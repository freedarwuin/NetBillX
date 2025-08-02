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

    // Verificar si hay un registro hoy
    $stmt = $dbh->prepare("SELECT id, rate FROM bcv_rate ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || $row['rate'] != $rate) {
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Nueva tasa BCV insertada: $rate Bs";
    } else {
        echo "ℹ️ La tasa BCV no cambió ($rate Bs). No se insertó nada.";
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
?>