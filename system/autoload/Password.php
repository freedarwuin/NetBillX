<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

class Password
{

    public static function _crypt($password)
    {
        return sha1($password);
    }

    public static function _verify($user_input, $hashed_password)
    {
        if (sha1($user_input) == $hashed_password) {
            return true;
        }
        return false;
    }
    public static function _uverify($user_input, $hashed_password)
    {
        if ($user_input == $hashed_password) {
            return true;
        }
        return false;
    }
    public static function _gen()
    {
        $pass = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz@#!123456789', 8)), 0, 8);
        return $pass;
    }

    /**
     * verify CHAP password
     * @param string $realPassword
     * @param string $CHAPassword
     * @param string $CHAPChallenge
     * @return bool
     */
    public static function chap_verify($realPassword, $CHAPassword, $CHAPChallenge){
        $CHAPassword = substr($CHAPassword, 2);
        $chapid = substr($CHAPassword, 0, 2);
        $result = hex2bin($chapid) . $realPassword . hex2bin(substr($CHAPChallenge, 2));
        $response = $chapid . md5($result);
        return ($response != $CHAPassword);
    }
}
