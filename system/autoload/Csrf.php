<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/


class Csrf
{
    private static $tokenExpiration = 1800; // 30 minutes

    public static function generateToken($length = 16)
    {
        return bin2hex(random_bytes($length));
    }

    public static function validateToken($token, $storedToken)
    {
        return hash_equals($token, $storedToken);
    }

    public static function check($token)
    {
        global $config, $isApi;
        if($config['csrf_enabled'] == 'yes' && !$isApi) {
            if (isset($_SESSION['csrf_token'], $_SESSION['csrf_token_time'], $token)) {
                $storedToken = $_SESSION['csrf_token'];
                $tokenTime = $_SESSION['csrf_token_time'];

                if (time() - $tokenTime > self::$tokenExpiration) {
                    self::clearToken();
                    return false;
                }

                return self::validateToken($token, $storedToken);
            }
            return false;
        }
        return true;
    }

    public static function generateAndStoreToken()
    {
        $token = self::generateToken();
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();
        return $token;
    }

    public static function clearToken()
    {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    }
}
