<?php

include "../config.php";

try {

    $dbh = new PDO(
        "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // 1️⃣ Obtener teléfono
    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting='phone' LIMIT 1");
    $stmt->execute();
    $phone = $stmt->fetchColumn();

    if (!$phone) {
        throw new Exception("No existe teléfono configurado.");
    }

    // 2️⃣ Obtener plantilla wa_url
    $stmt = $dbh->prepare("SELECT value FROM tbl_appconfig WHERE setting='wa_url' LIMIT 1");
    $stmt->execute();
    $wa_url_template = $stmt->fetchColumn();

    if (!$wa_url_template) {
        throw new Exception("No existe wa_url configurado.");
    }

    // 3️⃣ Mensaje de prueba
    $message = "Prueba de envio WhatsApp desde NetBillX - " . date("Y-m-d H:i:s");

    // Importante: codificar mensaje para URL
    $message_encoded = urlencode($message);

    // 4️⃣ Reemplazar variables
    $wa_url = str_replace(
        ['[number]', '[text]'],
        [$phone, $message_encoded],
        $wa_url_template
    );

    // 5️⃣ Enviar solicitud
    $response = file_get_contents($wa_url);

    if ($response === false) {
        throw new Exception("No se pudo conectar con el servidor WhatsApp.");
    }

    echo "Mensaje enviado correctamente\n";
    echo "Respuesta del servidor:\n";
    echo $response;

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}