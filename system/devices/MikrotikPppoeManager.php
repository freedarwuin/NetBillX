<?php

use PEAR2\Net\RouterOS;

class MikrotikPppoeManager
{
    private $client;

    public function __construct($host, $user, $pass, $port = 8728)
    {
        try {
            $this->client = new RouterOS\Client($host, $user, $pass, $port);
        } catch (\Exception $e) {
            throw new Exception("RouterOS connection failed: " . $e->getMessage());
        }
    }

    /* =========================================================
     * VALIDACIONES
     * ========================================================= */

    private function profileExists($profileName)
    {
        $request = new RouterOS\Request('/ppp/profile/print');
        $request->setQuery(RouterOS\Query::where('name', $profileName));

        $responses = $this->client->sendSync($request);

        return count($responses) > 0;
    }

    private function poolExists($poolName)
    {
        $request = new RouterOS\Request('/ip/pool/print');
        $request->setQuery(RouterOS\Query::where('name', $poolName));

        $responses = $this->client->sendSync($request);

        return count($responses) > 0;
    }

    private function staticIpAvailable($ip, $username = null)
    {
        $request = new RouterOS\Request('/ppp/secret/print');
        $request->setQuery(RouterOS\Query::where('remote-address', $ip));

        $responses = $this->client->sendSync($request);

        foreach ($responses as $response) {
            if ($response->getProperty('name') !== $username) {
                return false;
            }
        }

        return true;
    }

    private function getSecretId($username)
    {
        $request = new RouterOS\Request('/ppp/secret/print');
        $request->setQuery(RouterOS\Query::where('name', $username));

        $responses = $this->client->sendSync($request);

        return $responses->getProperty('.id');
    }

    /* =========================================================
     * CREAR O ACTUALIZAR CLIENTE
     * ========================================================= */

    public function createOrUpdate($username, $password, $profile, $ip = null)
    {
        if (!$this->profileExists($profile)) {
            throw new Exception("Profile does not exist in RouterOS.");
        }

        if (!empty($ip)) {

            // Si es IP estática
            if (filter_var($ip, FILTER_VALIDATE_IP)) {

                if (!$this->staticIpAvailable($ip, $username)) {
                    throw new Exception("Static IP already in use.");
                }

                $remoteAddress = $ip;

            } else {
                // Es pool
                if (!$this->poolExists($ip)) {
                    throw new Exception("IP Pool does not exist.");
                }

                $remoteAddress = $ip;
            }

        } else {
            $remoteAddress = null;
        }

        $secretId = $this->getSecretId($username);

        if (!$secretId) {
            return $this->createSecret($username, $password, $profile, $remoteAddress);
        }

        return $this->updateSecret($secretId, $username, $password, $profile, $remoteAddress);
    }

    private function createSecret($username, $password, $profile, $remoteAddress)
    {
        $request = new RouterOS\Request('/ppp/secret/add');
        $request->setArgument('name', $username);
        $request->setArgument('password', $password);
        $request->setArgument('profile', $profile);
        $request->setArgument('service', 'pppoe');

        if (!empty($remoteAddress)) {
            $request->setArgument('remote-address', $remoteAddress);
        }

        $this->client->sendSync($request);

        return true;
    }

    private function updateSecret($id, $username, $password, $profile, $remoteAddress)
    {
        $request = new RouterOS\Request('/ppp/secret/set');
        $request->setArgument('numbers', $id);
        $request->setArgument('name', $username);
        $request->setArgument('password', $password);
        $request->setArgument('profile', $profile);

        if (!empty($remoteAddress)) {
            $request->setArgument('remote-address', $remoteAddress);
        }

        $this->client->sendSync($request);

        $this->disconnectActiveSession($username);

        return true;
    }

    /* =========================================================
     * ELIMINAR CLIENTE
     * ========================================================= */

    public function remove($username)
    {
        $id = $this->getSecretId($username);

        if (!$id) {
            return false;
        }

        $this->disconnectActiveSession($username);

        $request = new RouterOS\Request('/ppp/secret/remove');
        $request->setArgument('numbers', $id);

        $this->client->sendSync($request);

        return true;
    }

    /* =========================================================
     * DESCONECTAR SESIÓN ACTIVA
     * ========================================================= */

    public function disconnectActiveSession($username)
    {
        $request = new RouterOS\Request('/ppp/active/print');
        $request->setQuery(RouterOS\Query::where('name', $username));

        $responses = $this->client->sendSync($request);

        $activeId = $responses->getProperty('.id');

        if ($activeId) {
            $remove = new RouterOS\Request('/ppp/active/remove');
            $remove->setArgument('numbers', $activeId);
            $this->client->sendSync($remove);
        }
    }
}
