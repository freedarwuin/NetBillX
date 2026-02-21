<?php

class widget_bcv_rate
{
    public static function getWidget($data)
    {
        global $config, $ui;

        try {
            $dbh = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']}",
                $config['db_user'],
                $config['db_pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                ]
            );

            // Obtener tasa desde la API
            $json = @file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");

            if (!$json) {
                return "❌ No se pudo conectar con la API.";
            }

            $apiData = json_decode($json, true);
            $rate = $apiData['promedio'] ?? $apiData['valor'] ?? null;

            if (!$rate) {
                return "❌ La API no devolvió una tasa válida.";
            }

            $today = date('Y-m-d');

            $stmt = $dbh->prepare("
                INSERT INTO bcv_rate (rate, rate_date, created_at)
                VALUES (?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                rate = IF(rate <> VALUES(rate), VALUES(rate), rate),
                updated_at = IF(rate <> VALUES(rate), NOW(), updated_at)
            ");

            $stmt->execute([$rate, $today]);

            $message = ($stmt->rowCount() > 0)
                ? "✅ Tasa BCV actualizada: {$rate} Bs"
                : "ℹ️ La tasa BCV de hoy ya estaba registrada ({$rate} Bs)";

            // Puedes pasar datos al template si quieres
            $ui->assign('bcv_rate', $rate);
            $ui->assign('bcv_message', $message);

            return $ui->fetch('widget/bcv_rate.tpl');

        } catch (PDOException $e) {
            return "❌ Error en la base de datos: " . $e->getMessage();
        }
    }
}