<?php

class bcv_rate
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

                // ======================================
                // Rellenar últimos 9 días calendario
                // ======================================
                $dates_needed = [];
                for ($i = 0; $i < 9; $i++) {
                    $dates_needed[] = date('Y-m-d', strtotime("-$i days"));
                }

                $history_map = [];
                foreach ($api_history as $day) {
                    $history_map[$day['rate_date']] = $day;
                }

                $filled_history = [];
                $last_known_rate = $bcv_rate;

                foreach ($dates_needed as $d) {
                    if (isset($history_map[$d])) {
                        $filled_history[] = $history_map[$d];
                        $last_known_rate = $history_map[$d]['rate'];
                    } else {
                        $filled_history[] = [
                            'rate_date' => $d,
                            'rate' => $last_known_rate,
                            'change' => 'same'
                        ];
                    }
                }

                // Ordenar de más reciente a más antiguo
                $bcv_history = $filled_history;
            }
        }

        // Asignar variables al UI/Smarty
        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        // Retornar el tpl
        return $ui->fetch('widget/bcv_rate.tpl');
    }
}