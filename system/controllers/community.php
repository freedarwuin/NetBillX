<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433
 **/

_admin();
$ui->assign('_title', Lang::T('Community'));
$ui->assign('_system_menu', 'community');

$action = $routes['1'];
$ui->assign('_admin', $admin);

switch ($action) {
    case 'rollback':
        $ui->assign('_title', Lang::T('Rollback Update'));
        $masters = json_decode(Http::getData("https://api.github.com/repos/freedarwuin/NetBillX/commits?per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);
        $devs = json_decode(Http::getData("https://api.github.com/repos/freedarwuin/NetBillX/commits?sha=Development&per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);

        $ui->assign('masters', $masters);
        $ui->assign('devs', $devs);
        $ui->display('admin/rollback.tpl');
        break;
    default:
        $ui->display('admin/community.tpl');
}