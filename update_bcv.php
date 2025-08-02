<?php
// Conexión a la base de datos
include 'config.php';
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 1️⃣ Buscar si ya existe la tasa de hoy
$stmt = $pdo->prepare("SELECT rate FROM bcv_rate WHERE DATE(created_at) = CURDATE() ORDER BY id DESC LIMIT 1");
$stmt->execute();
$bcv_rate = $stmt->fetchColumn();

// 2️⃣ Si no existe la tasa de hoy, llamar a la API y guardarla
if (!$bcv_rate) {
    $api_url = "https://ve.dolarapi.com/v1/dolares/oficial";
    $json = file_get_contents($api_url);
    if ($json !== false) {
        $data = json_decode($json, true);
        if (isset($data['precio'])) {
            $bcv_rate = $data['precio'];
            // Guardar en la base de datos
            $insert = $pdo->prepare("INSERT INTO bcv_rate (rate, created_at) VALUES (?, NOW())");
            $insert->execute([$bcv_rate]);
        } else {
            $bcv_rate = "Error API";
        }
    } else {
        $bcv_rate = "Sin conexión";
    }
}

// 3️⃣ Mostrar la tasa actual
echo "<div style='background:#f5f5f5;padding:10px;margin-bottom:10px;border:1px solid #ccc;'>
        <strong>Tasa BCV del día:</strong> <span style='color:green;font-weight:bold;'>$bcv_rate Bs/USD</span>
      </div>";
?>
