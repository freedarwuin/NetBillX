<?php
require_once __DIR__ . '/../autoad/Smarty.class.php'; // si tu proyecto tiene Smarty aquí
$ui = new Smarty();

$bcvFile = __DIR__ . '/../bcv_data.json';
$bcvData = json_decode(file_get_contents($bcvFile), true);

$ui->assign('bcv_rate', $bcvData['bcv_rate'] ?? null);
$ui->assign('bcv_history', $bcvData['bcv_history'] ?? []);

$ui->display('ui/widget/bcv_rate.tpl');