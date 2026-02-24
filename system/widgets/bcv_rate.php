<?php
/**
 * Widget: Tasa BCV
 * Lee el JSON generado por cron_bcv.php y asigna variables a Smarty
 */

class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui; // instancia de Smarty usada por el proyecto

        // Ruta correcta al JSON
        $bcvFile = __DIR__ . '/../bcv_data.json';

        if (!file_exists($bcvFile)) {
            die("Error: JSON no existe en $bcvFile");
        }

        // Leer y decodificar JSON
        $bcvData = json_decode(file_get_contents($bcvFile), true);
        if (!$bcvData) {
            die("Error: JSON vacío o mal formado");
        }

        // Asignar a Smarty
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

        // Mostrar la plantilla
        $ui->display('ui/widget/bcv_rate.tpl');
    }
}