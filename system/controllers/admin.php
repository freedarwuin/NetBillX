<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Pragma: no-cache");

if (Admin::getID()) {
    r2(getUrl('dashboard'), "s", Lang::T("You are already logged in"));
}

if (isset($routes['1'])) {
    $do = $routes['1'];
} else {
    $do = 'login-display';
}

switch ($do) {
    case 'post':
        $username = _post('username');
        $password = _post('password');
        //csrf token
        $csrf_token = _post('csrf_token');
        if (!Csrf::check($csrf_token)) {
            _alert(Lang::T('Invalid or Expired CSRF Token') . ".", 'danger', "admin");
        }
        run_hook('admin_login'); #HOOK
        if ($username != '' and $password != '') {
            $d = ORM::for_table('tbl_users')->where('username', $username)->find_one();
            if ($d) {
                $d_pass = $d['password'];
                if (Password::_verify($password, $d_pass) == true) {
                    $_SESSION['aid'] = $d['id'];
                    $token = Admin::setCookie($d['id']);
                    $d->last_login = date('Y-m-d H:i:s');
                    $d->save();
                    _log($username . ' ' . Lang::T('Login Successful'), $d['user_type'], $d['id']);
                    if ($isApi) {
                        if ($token) {
                            showResult(true, Lang::T('Login Successful'), ['token' => "a." . $token]);
                        } else {
                            showResult(false, Lang::T('Invalid Username or Password'));
                        }
                    }
                    _alert(Lang::T('Login Successful'), 'success', "dashboard");
                } else {
                    _log($username . ' ' . Lang::T('Failed Login'), $d['user_type']);
                    _alert(Lang::T('Invalid Username or Password') . ".", 'danger', "admin");
                }
            } else {
                _alert(Lang::T('Invalid Username or Password') . "..", 'danger', "admin");
            }
        } else {
            _alert(Lang::T('Invalid Username or Password') . "...", 'danger', "admin");
        }

        break;
    default:
        run_hook('view_login'); #HOOK
        $csrf_token = Csrf::generateAndStoreToken();
        $ui->assign('csrf_token', $csrf_token);
        $ui->display('admin/admin/login.tpl');
        break;
}
