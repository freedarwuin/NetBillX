<?php
/**
 * Depuración directa de BCV
 * URL: http://192.168.100.2/system/widgets/bcv_rate.php
 */

// Ruta al JSON
$bcvFile = __DIR__ . '/../bcv_data.json';

// Leer JSON
$bcvData = file_exists($bcvFile) ? json_decode(file_get_contents($bcvFile), true) : [];
$bcv_rate    = $bcvData['bcv_rate'] ?? null;
$bcv_history = $bcvData['bcv_history'] ?? [];

// Inicializar Smarty solo para depuración
require_once __DIR__ . '/../../ui/autoload.php'; // Ajusta según tu NetBillX
$smarty = new Smarty();
$smarty->setTemplateDir(__DIR__ . '/../../ui/widget/');
$smarty->setCompileDir(__DIR__ . '/../../ui/cache/');

// Asignar variables
$smarty->assign('bcv_rate', $bcv_rate);
$smarty->assign('bcv_history', $bcv_history);

// Mostrar plantilla de depuración
$smarty->display('bcv_rate.tpl');