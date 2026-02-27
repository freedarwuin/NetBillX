<?php
class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $eur_rate = null;
        $rate_date = null;
        $bcv_history = [];

        $chart_labels = [];
        $chart_values = [];
        $chart_euro_values = [];
        $chart_usdt_values = [];

        if (file_exists($tmpFile)) {
            $data = json_decode(file_get_contents($tmpFile), true);
            if ($data) {
                $bcv_rate  = $data['bcv_rate'] ?? null;
                $eur_rate  = $data['eur_rate'] ?? null;
                $rate_date = $data['rate_date'] ?? null;
                $bcv_history = array_slice($data['bcv_history'] ?? [], 0, 9);

                $history_for_chart = array_reverse($bcv_history);
                $lastUsdt = null;

                foreach ($history_for_chart as $day) {
                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));
                    $chart_values[] = isset($day['rate']) ? (float)$day['rate'] : 0;
                    $chart_euro_values[] = isset($day['eur']) ? (float)$day['eur'] : 0;

                    if (isset($day['usdt']) && $day['usdt'] !== null) {
                        $lastUsdt = (float)$day['usdt'];
                    }
                    $chart_usdt_values[] = $lastUsdt ?? 0;
                }
            }
        }

        $ui->assign([
            'bcv_rate'          => $bcv_rate,
            'eur_rate'          => $eur_rate,
            'rate_date'         => $rate_date,
            'bcv_history'       => $bcv_history,
            'chart_labels'      => json_encode($chart_labels),
            'chart_values_usd'  => json_encode($chart_values),
            'chart_values_eur'  => json_encode($chart_euro_values),
            'chart_values_usdt' => json_encode($chart_usdt_values)
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}