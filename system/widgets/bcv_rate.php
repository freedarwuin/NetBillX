<?php
class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui;

        // Ruta al JSON generado por cron_bcv.php
        $bcvFile = __DIR__ . '/../bcv_data.json';

        // Leer JSON
        if (file_exists($bcvFile)) {
            $bcvData = json_decode(file_get_contents($bcvFile), true);
        } else {
            $bcvData = [];
        }

        // Asignar variables a Smarty para el tpl
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

        // Mostrar plantilla del widget
        $ui->display('ui/widget/bcv_rate.tpl');
    }
}