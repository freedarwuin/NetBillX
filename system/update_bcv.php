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

    // Verificar último registro de hoy
    $stmt = $dbh->prepare("SELECT id, rate FROM bcv_rate WHERE DATE(created_at)=CURDATE() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // No hay registro hoy → insertar el primero
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Insertado primer rate de hoy: $rate Bs";
    } elseif ($row['rate'] == $rate) {
        // El rate es igual → actualizar la hora
        $stmt = $dbh->prepare("UPDATE bcv_rate SET created_at=NOW() WHERE id=?");
        $stmt->execute([$row['id']]);
        echo "ℹ️ Rate igual ($rate Bs) → actualizado timestamp";
    } else {
        // El rate cambió → insertar nuevo
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Nuevo rate detectado e insertado: $rate Bs";
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
?>
