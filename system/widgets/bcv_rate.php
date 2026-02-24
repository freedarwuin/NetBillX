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

        // ==========================
        // Calcular variación porcentual
        // ==========================
        $variation_percent = 0;

        if (count($chart_values) > 1) {
            $first = $chart_values[0];
            $last  = $chart_values[count($chart_values) - 1];

            if ($first > 0) {
                $variation_percent = (($last - $first) / $first) * 100;
            }
        }

        $ui->assign([
            'variation_percent' => round($variation_percent, 2)
        ]);

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

        // ==========================
        // Estado de expiración API DolarVzla
        // ==========================

        $dolarvzla_api_expiration = null;
        $dolarvzla_api_expired = false;
        $dolarvzla_api_expiring_soon = false;

        $configExp = ORM::for_table('tbl_appconfig')
            ->where('setting', 'dolarvzla_api_expiration')
            ->find_one();

        if ($configExp && !empty($configExp->value)) {

            $expirationTime = strtotime($configExp->value);

            if ($expirationTime !== false) {

                // Formatear para mostrar
                $dolarvzla_api_expiration = date('d/m/Y H:i', $expirationTime);

                // Ya vencida
                $dolarvzla_api_expired = $expirationTime < time();

                // Vence en los próximos 3 días
                $dolarvzla_api_expiring_soon =
                    $expirationTime > time() &&
                    $expirationTime <= strtotime('+3 days');
            }
        }

        $ui->assign([
            'bcv_rate'     => $bcv_rate,
            'bcv_history'  => $bcv_history,
            'chart_labels' => json_encode($chart_labels),
            'chart_values' => json_encode($chart_values),

            // Nuevas variables
            'dolarvzla_api_expiration'    => $dolarvzla_api_expiration,
            'dolarvzla_api_expired'       => $dolarvzla_api_expired,
            'dolarvzla_api_expiring_soon' => $dolarvzla_api_expiring_soon
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}