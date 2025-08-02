<?php
include "../init.php";

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $pdo->prepare("SELECT rate FROM bcv_rate WHERE DATE(created_at)=CURDATE() ORDER BY id DESC LIMIT 1");
    $stmt->execute();
    $rate = $stmt->fetchColumn();

    if (!$rate) {
        $api_url = "https://ve.dolarapi.com/v1/dolares/oficial";
        $json = @file_get_contents($api_url);
        $data = json_decode($json, true);
        if (isset($data['precio'])) {
            $rate = $data['precio'];
            $insert = $pdo->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
            $insert->execute([$rate]);
            echo "✅ Tasa BCV actualizada: {$rate} Bs/USD\n";
        } else {
            echo "❌ Error al obtener la tasa desde la API\n";
        }
    } else {
        echo "ℹ️ Tasa BCV ya existe: {$rate} Bs/USD\n";
    }
} catch (PDOException $e) {
    echo "❌ Error en la base de datos: " . $e->getMessage() . "\n";
}
