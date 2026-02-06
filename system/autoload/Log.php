<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/


class Log{
    public static function put($type, $description, $userid = '', $username = '')
    {
        $d = ORM::for_table('tbl_logs')->create();
        $d->date = date('Y-m-d H:i:s');
        $d->type = $type;
        $d->description = $description;
        $d->userid = $userid;
        $d->ip = (empty($username)) ? $_SERVER["REMOTE_ADDR"] : $username;
        $d->save();
    }

    public static function arrayToText($array, $start = '', $result = '')
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                $result = Log::arrayToText($v, "$start$k.", $result);
            } else {
                $result .= $start.$k ." : ". strval($v) ."\n";
            }
        }
        return $result;
    }
}