<?php
class bcv_rate
{
    public function run_command($param = null)
    {
        global $ui;
        $bcvFile = __DIR__ . '/../bcv_data.json';
        if (!file_exists($bcvFile)) {
            $ui->assign('bcv_rate', null);
            $ui->assign('bcv_history', []);
            return;
        }

        $bcvData = json_decode(file_get_contents($bcvFile), true);
        $ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
        $ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);
    }
}