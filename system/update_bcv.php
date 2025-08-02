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

    // Último registro de la tabla
    $stmt = $dbh->query("SELECT rate, created_at FROM bcv_rate ORDER BY id DESC LIMIT 1");
    $last = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$last) {
        // Si no hay registros, insertar el primero
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Primera tasa BCV registrada: $rate Bs";
        exit;
    }

    $lastRate = $last['rate'];
    $lastDate = date('Y-m-d', strtotime($last['created_at']));
    $today = date('Y-m-d');

    // ✅ Lógica:
    // - Si el rate es diferente al último → INSERTA (aunque sea el mismo día)
    // - Si el rate es igual al último → NO hace nada
    if ($rate != $lastRate) {
        $stmt = $dbh->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
        $stmt->execute([$rate]);
        echo "✅ Nueva tasa detectada ($rate Bs) → Insertada";
    } else {
        echo "ℹ️ La tasa no cambió ($rate Bs). No se insertó nada.";
    }

} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage();
}
