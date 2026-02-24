<?php
/**
 * Widget: Tasa BCV
 * Lee el JSON y asigna a Smarty, sin llamar a display()
 */

class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui; // instancia de Smarty/UILoader de NetBillX

        $bcvFile = __DIR__ . '/../bcv_data.json';
        if (!file_exists($bcvFile)) {
            $ui->assign('bcv_rate', null);
            $ui->assign('bcv_history', []);
            return;
        }

        $bcvData = json_decode(file_get_contents($bcvFile), true);
        if (!is_array($bcvData)) {
            $ui->assign('bcv_rate', null);
            $ui->assign('bcv_history', []);
            return;
        }

        // Asignar variables a la plantilla
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);
    }
}