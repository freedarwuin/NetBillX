<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $euro_rate = null;
        $rate_date = null;
        $bcv_history = [];

        $chart_labels = [];
        $chart_values = [];
        $chart_euro_values = [];
        $chart_usdt_values = []; // Nueva variable para los valores de USDT

        if (file_exists($tmpFile)) {

            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {

                $bcv_rate    = $data['bcv_rate'] ?? null;
                $euro_rate   = $data['eur_rate'] ?? null;
                $rate_date   = $data['rate_date'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];

                // Tomar últimos 9 registros
                $bcv_history = array_slice($bcv_history, 0, 9);

                // Invertir para gráfico (antiguo → reciente)
                $history_for_chart = array_reverse($bcv_history);

                foreach ($history_for_chart as $day) {

                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));

                    // Asegurarnos de que no haya valores nulos en la gráfica
                    $chart_values[] = isset($day['rate']) ? (float)$day['rate'] : 0;
                    $chart_euro_values[] = isset($day['eur']) ? (float)$day['eur'] : 0;
                    $chart_usdt_values[] = isset($day['usdt']) ? (float)$day['usdt'] : 0;
                }

                // Asignar a las variables para el gráfico
                $ui->assign([
                    'chart_labels'       => json_encode($chart_labels),
                    'chart_values'       => json_encode($chart_values),
                    'chart_euro_values'  => json_encode($chart_euro_values),
                    'chart_usdt_values'  => json_encode($chart_usdt_values),
                ]);
            }
        }

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}