<?php
class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';
        $bcv_rate = $euro_rate = $rate_date = null;
        $bcv_history = [];
        $chart_labels = $chart_values = $chart_euro_values = $chart_usdt_values = [];

        if (file_exists($tmpFile)) {
            $data = json_decode(file_get_contents($tmpFile), true);
            if ($data) {
                $bcv_rate = $data['bcv_rate'] ?? null;
                $euro_rate = $data['eur_rate'] ?? null;
                $rate_date = $data['rate_date'] ?? null;
                $bcv_history = array_slice($data['bcv_history'] ?? [],0,9);
                $history_for_chart = array_reverse($bcv_history);

                $lastUsdt = null;
                foreach($history_for_chart as $day){
                    $chart_labels[] = date('d/m', strtotime($day['rate_date']));
                    $chart_values[] = $day['rate'] ?? null;
                    $chart_euro_values[] = $day['eur'] ?? null;

                    if (isset($day['usdt']) && $day['usdt'] !== null) $lastUsdt = $day['usdt'];
                    $chart_usdt_values[] = $lastUsdt ?? null;
                }

                // Variación USD
                $variation_percent = $variacion_texto = 0;
                if(count($chart_values)>1){
                    $ayer = $chart_values[count($chart_values)-2];
                    $hoy  = $chart_values[count($chart_values)-1];
                    $variation_percent = $ayer>0 ? (($hoy-$ayer)/$ayer)*100 : 0;
                    $variation_percent = round($variation_percent,2);
                    $variacion_texto = $variation_percent>0 ? "⬆ Subió {$variation_percent}%" : ($variation_percent<0 ? "⬇ Bajó {$variation_percent}%" : "➖ Sin cambio");
                }

                // Variación EUR
                $variation_percent_euro = $variacion_texto_euro = 0;
                if(count($chart_euro_values)>1){
                    $ayer = $chart_euro_values[count($chart_euro_values)-2];
                    $hoy  = $chart_euro_values[count($chart_euro_values)-1];
                    $variation_percent_euro = $ayer>0 ? (($hoy-$ayer)/$ayer)*100 : 0;
                    $variation_percent_euro = round($variation_percent_euro,2);
                    $variacion_texto_euro = $variation_percent_euro>0 ? "⬆ Subió {$variation_percent_euro}%" :
                                            ($variation_percent_euro<0 ? "⬇ Bajó {$variation_percent_euro}%" : "➖ Sin cambio");
                }
            }
        }

        $ui->assign([
            'bcv_rate'=>$bcv_rate,
            'euro_rate'=>$euro_rate,
            'rate_date'=>$rate_date,
            'bcv_history'=>$bcv_history,
            'chart_labels'=>json_encode($chart_labels),
            'chart_values_usd'=>json_encode($chart_values),
            'chart_values_eur'=>json_encode($chart_euro_values),
            'chart_values_usdt'=>json_encode($chart_usdt_values),
            'variacion_valor'=>$variation_percent,
            'variacion_texto_usd'=>$variacion_texto,
            'variacion_valor_eur'=>$variation_percent_euro,
            'variacion_texto_eur'=>$variacion_texto_euro
        ]);

        return $ui->fetch('widget/bcv_rate.tpl');
    }
}