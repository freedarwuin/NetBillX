<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

_admin();
$ui->assign('_title', Lang::T('Custom Fields'));
$ui->assign('_system_menu', 'settings');

$action = $routes['1'];
$ui->assign('_admin', $admin);

$fieldPath = $UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer_field.json";

switch ($action) {
    case 'save':
        print_r($_POST);
        $datas = [];
        $count = count($_POST['name']);
        for($n=0;$n<$count;$n++){
            if(!empty($_POST['name'][$n])){
                $datas[] = [
                    'order' => $_POST['order'][$n],
                    'name' => Text::alphanumeric(strtolower(str_replace(" ", "_", $_POST['name'][$n])), "_"),
                    'type' => $_POST['type'][$n],
                    'placeholder' => $_POST['placeholder'][$n],
                    'value' => $_POST['value'][$n],
                    'register' => $_POST['register'][$n],
                    'required' => $_POST['required'][$n]
                ];
            }
        }
        if(count($datas)>1){
            usort($datas, function ($item1, $item2) {
                return $item1['order'] <=> $item2['order'];
            });
        }
        if(file_put_contents($fieldPath, json_encode($datas))){
            r2(getUrl('customfield'), 's', 'Successfully saved custom fields!');
        }else{
            r2(getUrl('customfield'), 'e', 'Failed to save custom fields!');
        }
    default:
        $fields = [];
        if(file_exists($fieldPath)){
            $fields = json_decode(file_get_contents($fieldPath), true);
        }
        $ui->assign('fields', $fields);
        $ui->display('admin/settings/customfield.tpl');
        break;
}