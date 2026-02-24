<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $bcv_history = [];
        $chart_labels = [];
        $chart_values = [];

        if (file_exists($tmpFile)) {

            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {

                $bcv_rate = $data['bcv_rate'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];

                // Tomar solo los últimos 9 registros
                $bcv_history = array_slice($bcv_history, 0, 9);

                // ==========================
                // Preparar datos para gráfico
                // ==========================

                // Invertimos para mostrar del más antiguo al más reciente
                $history_for_chart = array_reverse($bcv_history);

                foreach ($history_for_chart as $day) {
                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));
                    $chart_values[] = (float)$day['rate'];
                }
            }
        }

        $ui->assign([
            'bcv_rate'     => $bcv_rate,
            'bcv_history'  => $bcv_history,
            'chart_labels' => json_encode($chart_labels),
            'chart_values' => json_encode($chart_values)
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}