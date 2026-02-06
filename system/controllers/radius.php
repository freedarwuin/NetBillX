<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/
_admin();
$ui->assign('_title', Lang::T('Plugin Manager'));
$ui->assign('_system_menu', 'settings');

$action = $routes['1'];
$ui->assign('_admin', $admin);


if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
    _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
}

switch ($action) {

    case 'nas-add':
        $ui->assign('_system_menu', 'radius');
        $ui->assign('_title', "Network Access Server");
        $ui->assign('routers', ORM::for_table('tbl_routers')->find_many());
        $ui->display('admin/radius/nas-add.tpl');
        break;
    case 'nas-add-post':
        $shortname = _post('shortname');
        $nasname = _post('nasname');
        $secret = _post('secret');
        $ports = _post('ports', null);
        $type = _post('type', 'other');
        $server = _post('server', null);
        $community = _post('community', null);
        $description = _post('description');
        $routers = _post('routers');
        $msg = '';

        if (Validator::Length($shortname, 30, 2) == false) {
            $msg .= 'Name should be between 3 to 30 characters' . '<br>';
        }
        if (empty($ports)) {
            $ports = null;
        }
        if (empty($server)) {
            $server = null;
        }
        if (empty($community)) {
            $community = null;
        }
        if (empty($type)) {
            $type = null;
        }
        $d = ORM::for_table('nas', 'radius')->where('nasname', $nasname)->find_one();
        if ($d) {
            $msg .= 'NAS IP Exists<br>';
        }
        if ($msg == '') {
            require_once $DEVICE_PATH . DIRECTORY_SEPARATOR . "Radius.php";
            if ((new Radius())->nasAdd($shortname, $nasname, $ports, $secret, $routers, $description, $type, $server, $community) > 0) {
                r2(getUrl('radius/nas-list/'), 's', "NAS Added");
            } else {
                r2(getUrl('radius/nas-add/'), 'e', "NAS Added Failed");
            }
        } else {
            r2(getUrl('radius/nas-add'), 'e', $msg);
        }
        break;
    case 'nas-edit':
        $ui->assign('_system_menu', 'radius');
        $ui->assign('_title', "Network Access Server");

        $id  = $routes['2'];
        $d = ORM::for_table('nas', 'radius')->find_one($id);
        if (!$d) {
            $d = ORM::for_table('nas', 'radius')->where_equal('shortname', _get('name'))->find_one();
        }
        if ($d) {
            $ui->assign('routers', ORM::for_table('tbl_routers')->find_many());
            $ui->assign('d', $d);
            $ui->display('admin/radius/nas-edit.tpl');
        } else {
            r2(getUrl('radius/list'), 'e', Lang::T('Account Not Found'));
        }

        break;
    case 'nas-edit-post':
        $id  = $routes['2'];
        $shortname = _post('shortname');
        $nasname = _post('nasname');
        $secret = _post('secret');
        $ports = _post('ports', null);
        $type = _post('type', 'other');
        $server = _post('server', null);
        $community = _post('community', null);
        $description = _post('description');
        $routers = _post('routers');
        $msg = '';

        if (Validator::Length($shortname, 30, 2) == false) {
            $msg .= 'Name should be between 3 to 30 characters' . '<br>';
        }
        if (empty($ports)) {
            $ports = null;
        }
        if (empty($server)) {
            $server = null;
        }
        if (empty($community)) {
            $community = null;
        }
        if (empty($type)) {
            $type = null;
        }
        if ($msg == '') {
            require_once $DEVICE_PATH . DIRECTORY_SEPARATOR . "Radius.php";
            if ((new Radius())->nasUpdate($id, $shortname, $nasname, $ports, $secret, $routers, $description, $type, $server, $community)) {
                r2(getUrl('radius/list/'), 's', "NAS Saved");
            } else {
                r2(getUrl('radius/nas-add'), 'e', 'NAS NOT Exists');
            }
        } else {
            r2(getUrl('radius/nas-add'), 'e', $msg);
        }
        break;
    case 'nas-delete':
        $id  = $routes['2'];
        $d = ORM::for_table('nas', 'radius')->find_one($id);
        if ($d) {
            $d->delete();
        } else {
            r2(getUrl('radius/nas-list'), 'e', 'NAS Not found');
        }
    default:
        $ui->assign('_system_menu', 'radius');
        $ui->assign('_title', "Network Access Server");
        $name = _post('name');
        if (empty($name)) {
            $query = ORM::for_table('nas', 'radius');
            $nas = Paginator::findMany($query);
        } else {
            $query = ORM::for_table('nas', 'radius')
                ->where_like('nasname', $search)
                ->where_like('shortname', $search)
                ->where_like('description', $search);
            $nas = Paginator::findMany($query, ['name' => $name]);
        }
        $ui->assign('name', $name);
        $ui->assign('nas', $nas);
        $ui->display('admin/radius/nas.tpl');
}
