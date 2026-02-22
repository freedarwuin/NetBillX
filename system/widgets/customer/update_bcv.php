<?php
// update_bcv.php

class update_bcv
{
    public static function getWidget($data)
    {
        global $db_host, $db_user, $db_pass, $db_name, $ui, $timezone;

        try {
            $dbh = new PDO(
                "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Obtener últimos 9 registros ordenados DESC
            $stmt = $dbh->prepare("
                SELECT rate_date, rate
                FROM bcv_rate
                ORDER BY rate_date DESC
                LIMIT 6
            ");
            $stmt->execute();
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$history) {
                $ui->assign('bcv_rate', null);
                $ui->assign('bcv_history', []);
                return $ui->fetch('widget/bcv_rate.tpl');
            }

            // La tasa actual es el primer registro
            $currentRate = $history[0]['rate'];

            // Calcular variaciones comparando contra el día anterior
            for ($i = 0; $i < count($history); $i++) {

                if (isset($history[$i + 1])) {

                    $todayRate = (float)$history[$i]['rate'];
                    $yesterdayRate = (float)$history[$i + 1]['rate'];

                    if ($todayRate > $yesterdayRate) {
                        $history[$i]['change'] = 'up';
                    } elseif ($todayRate < $yesterdayRate) {
                        $history[$i]['change'] = 'down';
                    } else {
                        $history[$i]['change'] = 'same';
                    }

                } else {
                    $history[$i]['change'] = 'none';
                }
            }

            $ui->assign('bcv_rate', $currentRate);
            $ui->assign('bcv_history', $history);
            $ui->assign('timezone', $timezone);

            return $ui->fetch('widget/customers/bcv_rate.tpl');

        } catch (PDOException $e) {
            return "Error en la base de datos: " . $e->getMessage();
        }
    }
}