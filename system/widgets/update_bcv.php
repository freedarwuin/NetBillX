<?php

class widget_bcv_rate
{
    public static function getWidget($data)
    {
        global $config, $ui, $timezone;

        try {
            $dbh = new PDO(
                "mysql:host={$config['db_host']};dbname={$config['db_name']}",
                $config['db_user'],
                $config['db_pass'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Obtener tasa actual desde API
            $json = @file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");
            $apiData = $json ? json_decode($json, true) : null;
            $rate = $apiData['promedio'] ?? $apiData['valor'] ?? null;

            if ($rate) {
                $today = date('Y-m-d');
                $stmt = $dbh->prepare("
                    INSERT INTO bcv_rate (rate, rate_date, created_at)
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE
                        rate = IF(rate <> VALUES(rate), VALUES(rate), rate),
                        updated_at = IF(rate <> VALUES(rate), NOW(), updated_at)
                ");
                $stmt->execute([$rate, $today]);
                $message = $stmt->rowCount() > 0
                    ? "✅ Tasa BCV actualizada: {$rate} Bs"
                    : "ℹ️ La tasa BCV de hoy ya estaba registrada ({$rate} Bs)";
            } else {
                $message = "❌ No se pudo obtener la tasa de la API";
            }

            // Pasar datos al template
            $ui->assign('bcv_rate', $rate);
            $ui->assign('bcv_message', $message);
            $ui->assign('timezone', $timezone);

            // Obtener últimos 7 días de historial
            $stmtHist = $dbh->prepare("SELECT rate_date, rate FROM bcv_rate ORDER BY rate_date DESC LIMIT 7");
            $stmtHist->execute();
            $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);
            $ui->assign('bcv_history', $history);

            return $ui->fetch('widget/bcv_rate.tpl');

        } catch (PDOException $e) {
            return "❌ Error en la base de datos: " . $e->getMessage();
        }
    }
}