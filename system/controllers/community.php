<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

_admin();
$ui->assign('_title', 'Community');
$ui->assign('_system_menu', 'community');

$action = $routes['1'];
$ui->assign('_admin', $admin);

switch ($action) {
    case 'rollback':
        $ui->assign('_title', 'Rollback Update');
        $masters = json_decode(Http::getData("https://api.github.com/repos/freedarwuin/NetBillX/commits?per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);
        $devs = json_decode(Http::getData("https://api.github.com/repos/freedarwuin/NetBillX/commits?sha=Development&per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);

        $ui->assign('masters', $masters);
        $ui->assign('devs', $devs);
        $ui->display('admin/rollback.tpl');
        break;
    default:
        $ui->display('admin/community.tpl');
}