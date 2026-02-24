<?php

class bcv_rate
{
    public function getWidget()
    {
        global $ui;

        $tmpFile = __DIR__ . '/../bcv_data.json';
        echo "DEBUG BCV\n📂 Leyendo archivo JSON en: $tmpFile\n";

        $bcv_rate = null;
        $bcv_history = [];

        if (file_exists($tmpFile)) {
            $json = file_get_contents($tmpFile);
            $data = json_decode($json, true);

            if ($data) {
                $bcv_rate = $data['bcv_rate'] ?? null;
                $bcv_history = $data['bcv_history'] ?? [];
            }
        }

        echo "✅ Datos leídos correctamente:\n";
        var_dump($bcv_rate);
        var_dump($bcv_history);

        // Asignar variables al UI/Smarty
        $ui->assign([
            'bcv_rate'    => $bcv_rate,
            'bcv_history' => $bcv_history
        ]);

        // Retornar el tpl del widget
        return $ui->fetch('widget/bcv_rate.tpl');
    }
}