<?php

class update_binance
{
    public static function getWidget($data)
    {
        global $db_name, $db_user, $db_pass, $ui;

        try {
            $dbh = new PDO(
                "mysql:host=127.0.0.1;dbname={$db_name};charset=utf8mb4",
                $db_user,
                $db_pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Últimos 10 registros
            $stmt = $dbh->query("
                SELECT rate_date, avg_rate, min_rate, max_rate, offers
                FROM binance_rate
                ORDER BY rate_date DESC
                LIMIT 10
            ");

            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$history) {
                $ui->assign('binance_rate', null);
                return $ui->fetch('widget/binance_rate.tpl');
            }

            $current = $history[0]['avg_rate'];

            // Detectar variación
            for ($i = 0; $i < count($history); $i++) {

                if (isset($history[$i + 1])) {

                    $today = (float)$history[$i]['avg_rate'];
                    $prev  = (float)$history[$i + 1]['avg_rate'];

                    if ($today > $prev) {
                        $history[$i]['change'] = 'up';
                    } elseif ($today < $prev) {
                        $history[$i]['change'] = 'down';
                    } else {
                        $history[$i]['change'] = 'same';
                    }

                } else {
                    $history[$i]['change'] = 'none';
                }
            }

            $ui->assign('binance_rate', $current);
            $ui->assign('binance_history', $history);

            return $ui->fetch('widget/binance_rate.tpl');

        } catch (PDOException $e) {
            return "Error BD: " . $e->getMessage();
        }
    }
}