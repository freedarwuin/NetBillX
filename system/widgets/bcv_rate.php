<?php

class bcv_rate_widget
{
    public function getWidget()
    {
        global $ui;

        // Ruta correcta al JSON
        $tmpFile = __DIR__ . '/../bcv_data.json';

        $bcv_rate = null;
        $bcv_history = [];

        if (!file_exists($tmpFile)) {
            echo "JSON no encontrado en $tmpFile\n";
        } else {
            $json = file_get_contents($tmpFile);
            if (!$json) {
                echo "JSON vacío en $tmpFile\n";
            } else {
                $data = json_decode($json, true);
                if (!$data) {
                    echo "Error al decodificar JSON: " . json_last_error_msg() . "\n";
                } else {
                    $bcv_rate = $data['bcv_rate'] ?? null;
                    $bcv_history = $data['bcv_history'] ?? [];
                }
            }
        }

        // Asignar variables al UI/Smarty
        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        // Retornar el tpl del widget
        return $ui->fetch('ui/widget/bcv_rate.tpl');
    }
}