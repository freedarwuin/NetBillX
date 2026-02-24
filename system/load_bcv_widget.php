<?php
require '../config.php';
require '../libs/Smarty.class.php';

$smarty = new Smarty();

// Ruta al JSON
$tmpFile = __DIR__ . '/bcv_data.json';

// Depuración: comprobar existencia y contenido
echo "<pre>";
echo "Archivo existe: "; var_dump(file_exists($tmpFile));
echo "\nContenido crudo:\n"; var_dump(file_get_contents($tmpFile));
echo "</pre>";

// Leer JSON
$data = json_decode(file_get_contents($tmpFile), true);

// Depuración: ver variables
echo "<pre>";
var_dump($data);
echo "</pre>";

// Asignar a Smarty solo si JSON es válido
if (is_array($data)) {
    $smarty->assign('bcv_rate', $data['bcv_rate']);
    $smarty->assign('bcv_history', $data['bcv_history']);
} else {
    $smarty->assign('bcv_rate', null);
    $smarty->assign('bcv_history', []);
}

// Renderizar widget
$smarty->display('../ui/ui/widget/bcv_rate.tpl');