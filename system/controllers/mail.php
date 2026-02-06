<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

_auth();
$action = $routes['1'];
$user = User::_info();
$ui->assign('_user', $user);

switch ($action) {
    case 'view':
        $mail = ORM::for_table('tbl_customers_inbox')->where('customer_id', $user['id'])->find_one($routes['2']);
        if(!$mail){
            r2(getUrl('mail'), 'e', Lang::T('Message Not Found'));
        }
        if($mail['date_read'] == null){
            $mail->date_read = date('Y-m-d H:i:s');
            $mail->save();
        }
        $next = ORM::for_table('tbl_customers_inbox')->select("id")->where('customer_id', $user['id'])->where_gt("id", $routes['2'])->order_by_asc("id")->find_one();
        $prev = ORM::for_table('tbl_customers_inbox')->select("id")->where('customer_id', $user['id'])->where_lt("id", $routes['2'])->order_by_desc("id")->find_one();

        $ui->assign('next', $next['id']);
        $ui->assign('prev', $prev['id']);
        $ui->assign('mail', $mail);
        $ui->assign('tipe', 'view');
        $ui->assign('_system_menu', 'inbox');
        $ui->assign('_title', Lang::T('Inbox'));
        $ui->display('customer/inbox.tpl');
        break;
    case 'delete':
        if($routes['2']){
            if(ORM::for_table('tbl_customers_inbox')->where('customer_id', $user['id'])->where('id', $routes['2'])->find_one()->delete()){
                r2(getUrl('mail'), 's', Lang::T('Mail Deleted Successfully'));
            }else{
                r2(getUrl('home'), 'e', Lang::T('Failed to Delete Message'));
            }
            break;
        }
    default:
        $q = _req('q');
        $limit = 40;
        $p = (int) _req('p', 0);
        $offset = $p * $limit;
        $query = ORM::for_table('tbl_customers_inbox')->where('customer_id', $user['id'])->order_by_desc('date_created');
        $query->limit($limit)->offset($offset);
        if(!empty($q)){
            $query->whereRaw("(subject like '%$q%' or body like '%$q%')");
        }
        $mails = $query->find_array();
        $ui->assign('tipe', '');
        $ui->assign('q', $q);
        $ui->assign('p', $p);
        $ui->assign('mails', $mails);
        $ui->assign('_system_menu', 'inbox');
        $ui->assign('_title', Lang::T('Inbox'));
        $ui->display('customer/inbox.tpl');
}