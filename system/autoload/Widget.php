<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/


/**
 * Validator class
 */
class Widget
{

    public static function rows($rows, $result){
        $result .= '<div class="row">';
        foreach($rows as $row){

        }
        $result .= '</div>';
    }

    public static function columns($cols, $result){
        $c = count($cols);
        switch($c){
            case 1:
                $result .= '<div class="col-md-12">';
                break;
            case 2:
                $result .= '<div class="col-md-6">';
                break;
            case 3:
                $result .= '<div class="col-md-4">';
                break;
            case 4:
                $result .= '<div class="col-md-4">';
                break;
            case 5:
                $result .= '<div class="col-md-4">';
                break;
            default:
                $result .= '<div class="col-md-1">';
                break;
        }

        foreach($cols as $col){
        }
        $result .= '</div>';
    }
}