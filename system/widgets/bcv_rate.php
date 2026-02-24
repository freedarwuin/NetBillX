<?php
class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui;

        $bcvFile = __DIR__ . '/../bcv_data.json';

        // Si quieres probar directamente en el navegador
        $debug = $param === 'debug';

        if (!file_exists($bcvFile)) {
            $bcvData = ['bcv_rate' => null, 'bcv_history' => []];
        } else {
            $bcvData = json_decode(file_get_contents($bcvFile), true);
        }

        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

        // Modo depuración: mostrar plantilla directamente
        if ($debug) {
            $ui->display('ui/widget/bcv_rate_debug.tpl');
        }
    }
}

// Permite ejecutar directamente: http://192.168.100.2/system/widgets/bcv_rate.php?debug
if (php_sapi_name() !== 'cli' && isset($_GET['debug'])) {
    global $ui; // NetBillX debe inicializar $ui
    $widget = new bcv_rate();
    $widget->run_command('debug');
}