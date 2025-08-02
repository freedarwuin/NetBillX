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
    $stmt = $dbh->prepare("SELECT id, rate FROM bcv_rate WHERE DATE(created_at)=CURDATE() ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($row['rate'] != $rate) {
            // La tasa cambió -> insertamos NUEVO registro
            $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
            $stmt->execute([$rate]);
            echo "✅ Nueva tasa BCV detectada: $rate Bs (Se insertó otro registro)";
        } else {
            echo "ℹ️ La tasa BCV de hoy ya está actualizada ($rate Bs). No se insertó nada.";
        }
    } else {
        // No hay registro de hoy -> insertamos el primero
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Tasa BCV insertada: $rate Bs (Primer registro del día)";
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
?>