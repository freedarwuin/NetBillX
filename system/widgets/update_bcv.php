<?php
// update_bcv.php

class update_bcv
{
    public static function getWidget($data)
    {
        global $db_host, $db_user, $db_pass, $db_name, $ui, $timezone;

        try {
            // Conexión PDO
            $dbh = new PDO(
                "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Obtener tasa actual desde API (solo lectura)
            $json = @file_get_contents("https://ve.dolarapi.com/v1/dolares/oficial");
            $apiData = $json ? json_decode($json, true) : null;
            $rate = $apiData['promedio'] ?? $apiData['valor'] ?? null;

            $message = $rate
                ? "Tasa actual API: {$rate} Bs"
                : "No se pudo obtener la tasa desde la API";

            // Asignar variables al template
            $ui->assign('bcv_rate', $rate);
            $ui->assign('bcv_message', $message);
            $ui->assign('timezone', $timezone);

            // Obtener últimos registros guardados (insertados por el cron)
            $stmtHist = $dbh->prepare("
                SELECT rate_date, rate
                FROM bcv_rate
                ORDER BY rate_date DESC
                LIMIT 9
            ");
            $stmtHist->execute();
            $history = $stmtHist->fetchAll(PDO::FETCH_ASSOC);

            $ui->assign('bcv_history', $history);

            return $ui->fetch('widget/bcv_rate.tpl');

        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getMessage();
        } catch (Exception $e) {
            return "Error general: " . $e->getMessage();
        }
    }
}