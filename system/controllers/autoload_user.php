<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

/**
 * used for ajax
 **/

_auth();

$action = $routes['1'];
$user = User::_info();

switch ($action) {
    case 'isLogin':
        $bill = ORM::for_table('tbl_user_recharges')->where('id', $routes['2'])->where('username', $user['username'])->findOne();
        if ($bill['type'] == 'Hotspot' && $bill['status'] == 'on') {
            $p = ORM::for_table('tbl_plans')->find_one($bill['plan_id']);
            $dvc = Package::getDevice($p);
            if ($_app_stage != 'demo') {
                try {
                    if (file_exists($dvc)) {
                        require_once $dvc;
                        if ((new $p['device'])->online_customer($user, $bill['routers'])) {
                            die('<a href="' . getUrl('home&mikrotik=logout&id=' . $bill['id']) . '" onclick="return confirm(\'' . Lang::T('Disconnect Internet?') . '\')" class="btn btn-success btn-xs btn-block">' . Lang::T('You are Online, Logout?') . '</a>');
                        } else {
                            if (!empty($_SESSION['nux-mac']) && !empty($_SESSION['nux-ip'])) {
                                die('<a href="' . getUrl('home&mikrotik=login&id=' . $bill['id']) . '" onclick="return confirm(\'' . Lang::T('Connect to Internet?') . '\')" class="btn btn-danger btn-xs btn-block">' . Lang::T('Not Online, Login now?') . '</a>');
                            } else {
                                die(Lang::T('-'));
                            }
                        }
                    } else {
                        die(Lang::T('-'));
                    }
                } catch (Exception $e) {
                    die(Lang::T('Failed to connect to device'));
                }
            }
            die(Lang::T('-'));
        } else {
            die('--');
        }
        break;
    case 'bw_name':
        $bw = ORM::for_table('tbl_bandwidth')->select("name_bw")->find_one($routes['2']);
        echo $bw['name_bw'];
        die();
    case 'inbox_unread':
        $count =  ORM::for_table('tbl_customers_inbox')->where('customer_id', $user['id'])->whereRaw('date_read is null')->count('id');
        if ($count > 0) {
            echo $count;
        }
        die();
    case 'inbox':
        $inboxs = ORM::for_table('tbl_customers_inbox')->selects(['id', 'subject', 'date_created'])->where('customer_id', $user['id'])->whereRaw('date_read is null')->order_by_desc('date_created')->limit(10)->find_many();
        foreach ($inboxs as $inbox) {
            echo '<li><a href="' . getUrl('mail/view/' . $inbox['id']) . '">' . $inbox['subject'] . '<br><sub class="text-muted">' . Lang::dateTimeFormat($inbox['date_created']) . '</sub></a></li>';
        }
        die();
    case 'language':
        $select = _get('select');
        $folders = [];
        $files = scandir('system/lan/');
        foreach ($files as $file) {
            if (is_file('system/lan/' . $file) && !in_array($file, ['index.html', 'country.json', '.DS_Store'])) {
                $file = str_replace(".json", "", $file);
                if(!empty($file)){
                    echo '<li><a href="' . getUrl('accounts/language-update-post&lang=' . $file) . '">';
                    if($select == $file){
                        echo '<span class="glyphicon glyphicon-ok"></span> ';
                    }
                    echo ucwords($file) . '</a></li>';
                }
            }
        }
        die();
    default:
        die();
}
