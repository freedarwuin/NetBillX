<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

_admin();
$ui->assign('_title', 'Pages');
$ui->assign('_system_menu', 'pages');

$action = $routes['1'];
$ui->assign('_admin', $admin);

if (strpos($action, "-reset") !== false) {
    if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
        _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
    }
    $action = str_replace("-reset", "", $action);
    $path = "$PAGES_PATH/" . str_replace(".", "", $action) . ".html";
    $temp = "pages_template/" . str_replace(".", "", $action) . ".html";
    if (file_exists($temp)) {
        if (!copy($temp, $path)) {
            file_put_contents($path, Http::getData('https://raw.githubusercontent.com/freedarwuin/NetBillX/master/pages_template/' . $action . '.html'));
        }
    } else {
        file_put_contents($path, Http::getData('https://raw.githubusercontent.com/freedarwuin/NetBillX/master/pages_template/' . $action . '.html'));
    }
    r2(getUrl('pages/') . $action);
} else if (strpos($action, "-post") === false) {
    if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
        _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
    }
    $path = "$PAGES_PATH/" . str_replace(".", "", $action) . ".html";
    $ui->assign("action", $action);
    //echo $path;
    run_hook('view_edit_pages'); #HOOK
    if (!file_exists($path)) {
        $temp = "pages_template/" . str_replace(".", "", $action) . ".html";
        if (file_exists($temp)) {
            if (!copy($temp, $path)) {
                touch($path);
            }
        } else {
            touch($path);
        }
    }
    if (file_exists($path)) {
        if ($action == 'Voucher') {
            if (!file_exists("$PAGES_PATH/vouchers/")) {
                mkdir("$PAGES_PATH/vouchers/");
                if (file_exists("pages_template/vouchers/")) {
                    File::copyFolder("pages_template/vouchers/", "$PAGES_PATH/vouchers/");
                }
            }
            $ui->assign("vouchers", scandir("$PAGES_PATH/vouchers/"));
        }
        $html = file_get_contents($path);
        $ui->assign("htmls", str_replace(["<div", "</div>"], "", $html));
        $ui->assign("writeable", is_writable($path));
        $ui->assign("pageHeader", str_replace('_', ' ', $action));
        $ui->assign("PageFile", $action);
        $ui->display('admin/settings/page.tpl');
    } else
        $ui->display('admin/404.tpl');
} else {
    if (!in_array($admin['user_type'], ['SuperAdmin', 'Admin'])) {
        _alert(Lang::T('You do not have permission to access this page'), 'danger', "dashboard");
    }
    $action = str_replace("-post", "", $action);
    $path = "$PAGES_PATH/" . str_replace(".", "", $action) . ".html";
    if (file_exists($path)) {
        $html = _post("html");
        run_hook('save_pages'); #HOOK
        if (file_put_contents($path, $html)) {
            if (_post('template_save') == 'yes') {
                if (!empty(_post('template_name'))) {
                    file_put_contents("$PAGES_PATH/vouchers/" . _post('template_name') . '.html', $html);
                }
            }
            r2(getUrl('pages/') . $action, 's', Lang::T("Saving page success"));
        } else {
            r2(getUrl('pages/') . $action, 'e', Lang::T("Failed to save page, make sure i can write to folder pages, <i>chmod 664 pages/*.html<i>"));
        }
    } else
        $ui->display('admin/404.tpl');
}
