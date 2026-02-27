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
        $chart_usdt_values = [];  // Nuevo array para USDT

        $variation_percent = 0;
        $variacion_texto = "Sin variación";

        $variation_percent_euro = 0;
        $variacion_texto_euro = "Sin variación";

        if (file_exists($tmpFile)) {

            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {

                $bcv_rate    = $data['bcv_rate'] ?? null;
                $euro_rate   = $data['eur_rate'] ?? null; // 👈 IMPORTANTE
                $rate_date   = $data['rate_date'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];

                // Ordenar cronológicamente los registros por fecha (más antiguo primero)
                usort($bcv_history, function($a, $b) {
                    return strtotime($a['rate_date']) - strtotime($b['rate_date']);
                });

                // Tomar los 9 últimos registros (para mostrar solo las últimas 9 fechas)
                $bcv_history = array_slice($bcv_history, -9);

                // Invertir el array para mostrar el más reciente a la derecha
                $history_for_chart = array_reverse($bcv_history);

                foreach ($history_for_chart as $day) {

                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));
                    $chart_values[] = isset($day['rate']) ? (float)$day['rate'] : 0;
                    $chart_euro_values[] = isset($day['eur']) ? (float)$day['eur'] : 0;
                    $chart_usdt_values[] = isset($day['usdt']) ? (float)$day['usdt'] : 0;
                }

                // ========================== Variación USD ==========================
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

                // ========================== Variación EUR ==========================
                if (count($chart_euro_values) > 1) {
                    $ayer_eur = $chart_euro_values[count($chart_euro_values) - 2];
                    $hoy_eur  = $chart_euro_values[count($chart_euro_values) - 1];

                    if ($ayer_eur > 0) {
                        $variation_percent_euro = (($hoy_eur - $ayer_eur) / $ayer_eur) * 100;
                    }

                    $variation_percent_euro = round($variation_percent_euro, 2);

                    if ($variation_percent_euro > 0) {
                        $variacion_texto_euro = "⬆ Subió {$variation_percent_euro}%";
                    } elseif ($variation_percent_euro < 0) {
                        $variacion_texto_euro = "⬇ Bajó {$variation_percent_euro}%";
                    } else {
                        $variacion_texto_euro = "➖ Sin cambio";
                    }
                }
            }
        }

        // ========================== Estado expiración API ==========================
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

        // ========================== Asignar a Smarty ==========================
        $ui->assign([
            'bcv_rate'     => $bcv_rate,
            'euro_rate'    => $euro_rate,
            'rate_date'    => $rate_date,
            'bcv_history'  => $bcv_history,
            'chart_labels' => json_encode($chart_labels),
            'chart_values' => json_encode($chart_values),
            'chart_euro_values' => json_encode($chart_euro_values),
            'chart_usdt_values' => json_encode($chart_usdt_values), // Asignar valores de USDT
            'variacion_valor' => $variation_percent,
            'variacion_texto' => $variacion_texto,
            'variacion_valor_euro' => $variation_percent_euro,
            'variacion_texto_euro' => $variacion_texto_euro,
            'dolarvzla_api_expiration' => $dolarvzla_api_expiration,
            'dolarvzla_api_expired' => $dolarvzla_api_expired,
            'dolarvzla_api_expiring_soon' => $dolarvzla_api_expiring_soon
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}