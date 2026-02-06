<?php

/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224514233?text=Hola%2C%20estoy%20usando%20el%20sistema%20NetBillX%20y%20requiero%20soporte.%0A%0ANombre%20completo%3A%0ACiudad%3A%0APa%C3%ADs%3A%0A%0AVersi%C3%B3n%20del%20sistema%3A%0AURL%20del%20sistema%3A%0AUsuario%20administrador%3A%0A%0ADescripci%C3%B3n%20del%20problema%3A%0ADesde%20cu%C3%A1ndo%20ocurre%3A%0AMensaje%20de%20error%20%28si%20aplica%29%3A%0A%0AGracias.
 **/

/**
 *  using proxy, add this variable in config.php
 *  $http_proxy  = '127.0.0.1:3128';
 *  if proxy using authentication, use this parameter
 *  $http_proxyauth = 'user:password';
 **/

class Http
{
    public static function getData($url, $headers = [], $connect_timeout = 3000, $wait_timeout = 3000)
    {
        global $http_proxy, $http_proxyauth, $admin;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $wait_timeout);
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!empty($http_proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $http_proxy);
            if (!empty($http_proxyauth)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $http_proxyauth);
            }
        }
        $server_output = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if ($admin && $error_msg) {
            Message::sendTelegram(
                "Http::getData Error:\n" .
                    _get('_route') . "\n" .
                    "\n$url" .
                    "\n$error_msg"
            );
            return $error_msg;
        }
        return (!empty($server_output)) ? $server_output : $error_msg;
    }

    public static function postJsonData($url, $array_post, $headers = [], $basic = null, $connect_timeout = 3000, $wait_timeout = 3000)
    {
        global $http_proxy, $http_proxyauth, $admin;
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $wait_timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        if (!empty($http_proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $http_proxy);
            if (!empty($http_proxyauth)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $http_proxyauth);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($array_post));
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($basic)) {
            curl_setopt($ch, CURLOPT_USERPWD, $basic);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if ($admin && $error_msg) {
            Message::sendTelegram(
                "Http::postJsonData:\n" .
                    _get('_route') . "\n" .
                    "\n$url" .
                    "\n$error_msg"
            );
            return $error_msg;
        }
        return (!empty($server_output)) ? $server_output : $error_msg;
    }


    public static function postData($url, $array_post, $headers = [], $basic = null, $connect_timeout = 3000, $wait_timeout = 3000)
    {
        global $http_proxy, $http_proxyauth, $admin;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $wait_timeout);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLINFO_HEADER_OUT, false);
        if (!empty($http_proxy)) {
            curl_setopt($ch, CURLOPT_PROXY, $http_proxy);
            if (!empty($http_proxyauth)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $http_proxyauth);
            }
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($array_post));
        if (is_array($headers) && count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($basic)) {
            curl_setopt($ch, CURLOPT_USERPWD, $basic);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
        }
        curl_close($ch);
        if ($admin && $error_msg) {
            Message::sendTelegram(
                "Http::postData Error:\n" .
                    _get('_route') . "\n" .
                    "\n$url" .
                    "\n$error_msg"
            );
            return $error_msg;
        }
        return (!empty($server_output)) ? $server_output : $error_msg;
    }
}
