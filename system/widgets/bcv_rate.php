<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        // Ruta al JSON generado por cron_bcv.php
        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $bcv_history = [];

        // DEBUG: quitar al final
        // echo "DEBUG BCV\n";

        if (file_exists($tmpFile)) {
            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {
                $bcv_rate = $data['bcv_rate'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];

                // Tomar solo los últimos 9 registros (más recientes)
                $bcv_history = array_slice($bcv_history, 0, 9);

                // DEBUG: quitar al final
                /*
                echo "bcv_rate: ";
                var_dump($bcv_rate);
                echo "\nbcv_history:\n";
                var_dump($bcv_history);
                */
            }
        }

        // Asignar variables al UI/Smarty
        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        // Retornar el tpl del widget
        return $ui->fetch('widget/bcv_rate.tpl');
    }
}