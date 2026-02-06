<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

_admin();
$ui->assign('_title', Lang::T('Dashboard'));
$ui->assign('_admin', $admin);

if (isset($_GET['refresh'])) {
    $files = scandir($CACHE_PATH);
    foreach ($files as $file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (is_file($CACHE_PATH . DIRECTORY_SEPARATOR . $file) && $ext == 'temp') {
            unlink($CACHE_PATH . DIRECTORY_SEPARATOR . $file);
        }
    }
    r2(getUrl('dashboard'), 's', 'Data Refreshed');
}

$tipeUser = _req("user");
if (empty($tipeUser)) {
    $tipeUser = 'Admin';
}
$ui->assign('tipeUser', $tipeUser);

$reset_day = $config['reset_day'];
if (empty($reset_day)) {
    $reset_day = 1;
}
// First day of month calculation
if (date("d") >= $reset_day) {
    $start_date = date('Y-m-' . $reset_day);
} else {
    $start_date = date('Y-m-' . $reset_day, strtotime("-1 MONTH"));
}

$current_date = date('Y-m-d');
$ui->assign('start_date', $start_date);
$ui->assign('current_date', $current_date);

$tipeUser = $admin['user_type'];
if (in_array($tipeUser, ['SuperAdmin', 'Admin'])) {
    $tipeUser = 'Admin';
}

// Cargar widgets
$widgets = ORM::for_table('tbl_widgets')->where("enabled", 1)->where('user', $tipeUser)->order_by_asc("orders")->findArray();
$count = count($widgets);
for ($i = 0; $i < $count; $i++) {
    try {
        if (file_exists($WIDGET_PATH . DIRECTORY_SEPARATOR . $widgets[$i]['widget'] . ".php")) {
            require_once $WIDGET_PATH . DIRECTORY_SEPARATOR . $widgets[$i]['widget'] . ".php";
            $widgets[$i]['content'] = (new $widgets[$i]['widget'])->getWidget($widgets[$i]);
        } else {
            $widgets[$i]['content'] = "Widget not found";
        }
    } catch (Throwable $e) {
        $widgets[$i]['content'] = $e->getMessage();
    }
}

// Obtener timezone desde tbl_appconfig
$timezone = null;
try {
    $config_timezone = ORM::for_table('tbl_appconfig')->where('setting', 'timezone')->find_one();
    if ($config_timezone) {
        $timezone = $config_timezone->value;
    }
} catch (Exception $e) {
    $timezone = null;
}

// Obtener la tasa BCV más reciente (si existe)
$bcv_rate = null;
try {
    $latest_rate = ORM::for_table('bcv_rate')->order_by_desc('created_at')->limit(1)->find_one();
    if ($latest_rate) {
        $bcv_rate = $latest_rate->rate;
    }
} catch (Exception $e) {
    $bcv_rate = null;
}

$ui->assign('widgets', $widgets);
$ui->assign('timezone', $timezone);
$ui->assign('bcv_rate', $bcv_rate);

run_hook('view_dashboard'); #HOOK
$ui->display('admin/dashboard.tpl');
