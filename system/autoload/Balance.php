<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/
/**
 *  This script is for managing user balance
 **/

class Balance
{

    public static function plus($id_customer, $amount)
    {
        $c = ORM::for_table('tbl_customers')->where('id', $id_customer)->find_one();
        $c->balance = $amount + $c['balance'];
        $c->save();
    }

    public static function transfer($id_customer, $phoneTarget, $amount)
    {
        global $config;
        if (Balance::min($id_customer, $amount)) {
            return Balance::plusByPhone($phoneTarget, $amount);
        } else {
            return false;
        }
    }

    public static function min($id_customer, $amount)
    {
        $c = ORM::for_table('tbl_customers')->where('id', $id_customer)->find_one();
        $c->balance = $c['balance'] - $amount;
        $c->save();
        return true;
    }

    public static function plusByPhone($phone_customer, $amount)
    {
        $c = ORM::for_table('tbl_customers')->where('username', $phone_customer)->find_one();
        if ($c) {
            $c->balance = $amount + $c['balance'];
            $c->save();
            return true;
        }
        return false;
    }

    public static function minByPhone($phone_customer, $amount)
    {
        $c = ORM::for_table('tbl_customers')->where('username', $phone_customer)->find_one();
        if ($c && $c['balance'] >= $amount) {
            $c->balance = $c['balance'] - $amount;
            $c->save();
            return true;
        } else {
            return false;
        }
    }
}
