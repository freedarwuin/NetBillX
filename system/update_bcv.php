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

    // Buscar registro de HOY
    $stmt = $dbh->prepare("SELECT id, rate FROM bcv_rate WHERE DATE(created_at) = ?");
    $stmt->execute([$today]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // ✅ Si no hay registro hoy → INSERTA
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Tasa BCV insertada para hoy: $rate Bs";
    } else {
        // ✅ Si hay registro hoy → Compara y actualiza solo si cambia
        if ($row['rate'] != $rate) {
            $stmt = $dbh->prepare("UPDATE bcv_rate SET rate = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$rate, $row['id']]);
            echo "✅ Tasa BCV de hoy actualizada: $rate Bs";
        } else {
            echo "ℹ️ La tasa BCV de hoy ($rate Bs) ya está actualizada. No se hizo nada.";
        }
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
