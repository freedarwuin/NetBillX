<?php
// update_cpu.php

class update_cpu
{
    public static function getWidget($data)
    {
        global $ui, $timezone;

        try {

            // Obtener uso actual de CPU
            $currentCpu = self::getCpuUsage();

            // Generar pequeño historial (últimos 10 segundos simulados)
            $history = [];

            for ($i = 0; $i < 10; $i++) {
                $value = self::getCpuUsage();

                $history[] = [
                    'cpu' => $value,
                    'time' => date('H:i:s')
                ];

                usleep(100000); // pequeña pausa 0.1s
            }

            // Ordenar DESC (más reciente primero)
            $history = array_reverse($history);

            // Detectar variación
            for ($i = 0; $i < count($history); $i++) {

                if (isset($history[$i + 1])) {

                    $today = (float)$history[$i]['cpu'];
                    $previous = (float)$history[$i + 1]['cpu'];

                    if ($today > $previous) {
                        $history[$i]['change'] = 'up';
                    } elseif ($today < $previous) {
                        $history[$i]['change'] = 'down';
                    } else {
                        $history[$i]['change'] = 'same';
                    }

                } else {
                    $history[$i]['change'] = 'none';
                }
            }

            $ui->assign('cpu_usage', $currentCpu);
            $ui->assign('cpu_history', $history);
            $ui->assign('timezone', $timezone);

            return $ui->fetch('widget/cpu_usage.tpl');

        } catch (Exception $e) {
            return "Error CPU Widget: " . $e->getMessage();
        }
    }


    private static function getCpuUsage()
    {
        // Linux
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'LIN') {
            $load = sys_getloadavg();
            $cores = (int)shell_exec('nproc');
            if ($cores > 0) {
                return round(($load[0] * 100) / $cores, 2);
            }
        }

        // Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            @exec('wmic cpu get loadpercentage', $output);
            if (isset($output[1])) {
                return (int)trim($output[1]);
            }
        }

        return 0;
    }
}