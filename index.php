<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

session_start();

if(isset($_GET['nux-mac']) && !empty($_GET['nux-mac'])){
    $_SESSION['nux-mac'] = $_GET['nux-mac'];
}

if(isset($_GET['nux-ip']) && !empty($_GET['nux-ip'])){
    $_SESSION['nux-ip'] = $_GET['nux-ip'];
}

if(isset($_GET['nux-router']) && !empty($_GET['nux-router'])){
    $_SESSION['nux-router'] = $_GET['nux-router'];
}

//get chap id and chap challenge
if(isset($_GET['nux-key']) && !empty($_GET['nux-key'])){
    $_SESSION['nux-key'] = $_GET['nux-key'];
}
//get mikrotik hostname
if(isset($_GET['nux-hostname']) && !empty($_GET['nux-hostname'])){
    $_SESSION['nux-hostname'] = $_GET['nux-hostname'];
}
require_once 'system/vendor/autoload.php';
require_once 'system/boot.php';
App::_run();