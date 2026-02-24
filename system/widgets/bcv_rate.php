<?php

class bcv_rate_widget
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $bcv_history = [];

        if (file_exists($tmpFile)) {

            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {

                $bcv_rate = $data['bcv_rate'] ?? null;
                $api_history = $data['bcv_history'] ?? [];

                // ==============================
                // Generar últimos 9 días calendario
                // ==============================
                $dates_needed = [];
                for ($i = 0; $i < 9; $i++) {
                    $dates_needed[] = date('Y-m-d', strtotime("-$i days"));
                }

                // Mapear historial por fecha
                $history_map = [];
                foreach ($api_history as $day) {
                    $history_map[$day['rate_date']] = $day['rate'];
                }

                $filled_history = [];
                $last_known_rate = $bcv_rate;

                foreach ($dates_needed as $index => $date) {

                    if (isset($history_map[$date])) {
                        $rate = $history_map[$date];
                        $is_real = true;
                    } else {
                        $rate = $last_known_rate;
                        $is_real = false;
                    }

                    // Calcular cambio contra el día anterior
                    if ($index == 0) {
                        $change = 'same';
                    } else {
                        $prev_rate = $filled_history[$index - 1]['rate'];

                        if ($rate > $prev_rate) {
                            $change = 'up';
                        } elseif ($rate < $prev_rate) {
                            $change = 'down';
                        } else {
                            $change = 'same';
                        }
                    }

                    $filled_history[] = [
                        'rate_date' => $date,
                        'rate'      => $rate,
                        'change'    => $change,
                        'is_real'   => $is_real
                    ];

                    $last_known_rate = $rate;
                }

                $bcv_history = $filled_history;
            }
        }

        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}