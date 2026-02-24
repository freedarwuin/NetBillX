<?php
/**
 * system/load_bcv_widget.php
 *
 * Carga el widget BCV usando Smarty leyendo el JSON generado por cron_bcv.php
 */

require '../config.php';             // Configuración de tu Smarty
require '../libs/Smarty.class.php';  // Ruta a Smarty

$smarty = new Smarty();

// Archivo JSON generado por cron_bcv.php
$tmpFile = __DIR__ . '/bcv_data.json';

// Leer datos del JSON
if (file_exists($tmpFile)) {
    $data = json_decode(file_get_contents($tmpFile), true);
    if (!is_array($data)) {
        $data = ['bcv_rate' => null, 'bcv_history' => []];
    }
} else {
    $data = ['bcv_rate' => null, 'bcv_history' => []];
}

// Asignar variables a Smarty
$smarty->assign('bcv_rate', $data['bcv_rate']);
$smarty->assign('bcv_history', $data['bcv_history']);

// Renderizar el widget
$smarty->display('../ui/ui/widget/bcv_rate.tpl');