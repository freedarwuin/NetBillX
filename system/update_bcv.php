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

    // Verificar si ya existe la tasa de hoy
    $stmt = $dbh->prepare("SELECT id FROM bcv_rate WHERE DATE(created_at)=CURDATE()");
    $stmt->execute();
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $stmt = $dbh->prepare("UPDATE bcv_rate SET rate=? WHERE id=?");
        $stmt->execute([$rate, $exists]);
        echo "✅ Tasa BCV actualizada a $rate Bs (Hoy)";
    } else {
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Tasa BCV insertada: $rate Bs (Hoy)";
    }
} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
?>