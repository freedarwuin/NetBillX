<?php
/**
 * Widget: Tasa BCV
 * Lee el JSON generado por cron_bcv.php y asigna variables a Smarty
 */

class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui; // usa la instancia de Smarty ya cargada

        // Ruta del JSON generado por cron
        $bcvFile = __DIR__ . '/../bcv_data.json';
        $bcvData = file_exists($bcvFile) ? json_decode(file_get_contents($bcvFile), true) : [];

        // Asignar variables a Smarty
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

        // Mostrar plantilla
        $ui->display('ui/widget/bcv_rate.tpl');
    }
}