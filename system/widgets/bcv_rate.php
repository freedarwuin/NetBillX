<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $rate_date = null;
        $bcv_history = [];

        $chart_labels = [];
        $chart_values = [];

        $variation_percent = 0;
        $variacion_texto = "Sin variación";

        if (file_exists($tmpFile)) {

            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {

                $bcv_rate    = $data['bcv_rate'] ?? null;
                $rate_date   = $data['rate_date'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];

                // Tomar últimos 9 registros
                $bcv_history = array_slice($bcv_history, 0, 9);

                // Invertir para gráfico (antiguo → reciente)
                $history_for_chart = array_reverse($bcv_history);

                foreach ($history_for_chart as $day) {
                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));
                    $chart_values[] = (float)$day['rate'];
                }

                // ==========================
                // Calcular variación REAL
                // ==========================

                if (count($chart_values) > 1) {

                    $ayer = $chart_values[count($chart_values) - 2];
                    $hoy  = $chart_values[count($chart_values) - 1];

                    if ($ayer > 0) {
                        $variation_percent = (($hoy - $ayer) / $ayer) * 100;
                    }

                    $variation_percent = round($variation_percent, 2);

                    if ($variation_percent > 0) {
                        $variacion_texto = "⬆ Subió {$variation_percent}%";
                    } elseif ($variation_percent < 0) {
                        $variacion_texto = "⬇ Bajó {$variation_percent}%";
                    } else {
                        $variacion_texto = "➖ Sin cambio";
                    }
                }
            }
        }

        // ==========================
        // Estado expiración API
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

                $dolarvzla_api_expiration = date('d/m/Y H:i', $expirationTime);

                $dolarvzla_api_expired = $expirationTime < time();

                $dolarvzla_api_expiring_soon =
                    $expirationTime > time() &&
                    $expirationTime <= strtotime('+3 days');
            }
        }

        // ==========================
        // Asignar variables a Smarty
        // ==========================

        $ui->assign([
            'bcv_rate'     => $bcv_rate,
            'rate_date'    => $rate_date,
            'bcv_history'  => $bcv_history,
            'chart_labels' => json_encode($chart_labels),
            'chart_values' => json_encode($chart_values),

            'variacion_valor' => $variation_percent,
            'variacion_texto' => $variacion_texto,

            'dolarvzla_api_expiration'    => $dolarvzla_api_expiration,
            'dolarvzla_api_expired'       => $dolarvzla_api_expired,
            'dolarvzla_api_expiring_soon' => $dolarvzla_api_expiring_soon
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}