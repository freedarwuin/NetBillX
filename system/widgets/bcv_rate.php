<?php
class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui;

        // Ruta al JSON
        $bcvFile = __DIR__ . '/../bcv_data.json';

        // Leer JSON
        if (file_exists($bcvFile)) {
            $bcvData = json_decode(file_get_contents($bcvFile), true);
        } else {
            $bcvData = [];
        }

        // Asignar variables a Smarty
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

        // Mostrar plantilla
        $ui->display('ui/widget/bcv_rate.tpl');
    }
}

// Permite ejecución directa para depuración (opcional)
if (php_sapi_name() !== 'cli' && isset($_GET['debug'])) {
    global $ui;
    $widget = new bcv_rate();
    $widget->run_command('debug');
}