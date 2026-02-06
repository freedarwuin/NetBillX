<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/


class App{
    public static function _run(){
        return true;
    }

    public static function getToken(){
        return md5(microtime());
    }

    public static function setToken($token, $value){
        $_SESSION[$token] = $value;
    }

    public static function getTokenValue($key){
        if(empty($key)){
            return "";
        }
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }else{
            return "";
        }
    }

    public static function getVoucher(){
        return md5(microtime());
    }

    public static function setVoucher($token, $value){
        $_SESSION[$token] = $value;
    }

    public static function getVoucherValue($key){
        if(empty($key)){
            return "";
        }
        if(isset($_SESSION[$key])){
            return $_SESSION[$key];
        }else{
            return "";
        }
    }

}
