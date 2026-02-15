<?php

use PEAR2\Net\RouterOS;

class MikrotikPppoe
{
    protected $routers = []; // almacena los datos de routers
    protected $clients = []; // almacena instancias de RouterOS\Client

    function __construct()
    {
        // opcional: precargar routers desde BD si quieres
        $allRouters = ORM::for_table('tbl_routers')->find_many();
        foreach ($allRouters as $r) {
            $this->routers[$r['name']] = $r;
        }
    }

    // devuelve cliente conectado a un router, lo crea solo si no existe
    protected function client($routerName)
    {
        if (isset($this->clients[$routerName])) {
            return $this->clients[$routerName];
        }

        $router = $this->info($routerName);
        if (!$router) {
            throw new Exception("Router '$routerName' no encontrado en BD");
        }

        $ipPort = explode(":", $router['ip_address']);
        $ip = $ipPort[0];
        $port = $ipPort[1] ?? 8728;

        $client = new RouterOS\Client($ip, $router['username'], $router['password'], $port);
        $this->clients[$routerName] = $client;

        return $client;
    }

    function description()
    {
        return [
            'title' => 'Mikrotik PPPOE',
            'description' => 'Gestión de conexión entre NetBillX y Mikrotik PPPOE',
            'author' => 'freedarwuin',
            'url' => [
                'Github' => 'https://github.com/freedarwuin/NetBillX/',
                'WhatsApp' => 'https://wa.me/584224512433',
                'Donate' => 'https://paypal.me/DPedroa'
            ]
        ];
    }

    // info router
    function info($name)
    {
        if (isset($this->routers[$name])) {
            return $this->routers[$name];
        }
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    // --------------------------------------
    // Métodos de cliente PPPOE
    // --------------------------------------

    function add_customer($customer, $plan)
    {
        global $isChangePlan;
        $client = $this->client($plan['routers']);

        $cid = $this->getIdByCustomer($customer, $client);
        $isExp = ORM::for_table('tbl_plans')->select("id")->where('plan_expired', $plan['id'])->find_one();

        if (empty($cid)) {
            $this->addPpoeUser($client, $plan, $customer, $isExp);
        } else {
            $setRequest = new RouterOS\Request('/ppp/secret/set');
            $setRequest->setArgument('numbers', $cid);
            $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
            $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
            $setRequest->setArgument('profile', $plan['name_plan']);
            $setRequest->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id'])));

            $unsetIP = true;
            if (!empty($customer['pppoe_ip']) && !$isExp) {
                $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
                $unsetIP = false;
            }
            $client->sendSync($setRequest);

            if ($unsetIP) {
                $unsetRequest = new RouterOS\Request('/ppp/secret/unset');
                $unsetRequest->setArgument('.id', $cid);
                $unsetRequest->setArgument('value-name', 'remote-address');
                $client->sendSync($unsetRequest);
            }

            if (!empty($isChangePlan) && $isChangePlan) {
                $this->removePpoeActive($client, $customer['username']);
                if (!empty($customer['pppoe_username'])) {
                    $this->removePpoeActive($client, $customer['pppoe_username']);
                }
            }
        }
    }

    function sync_customer($customer, $plan)
    {
        $this->add_customer($customer, $plan);
    }

    function remove_customer($customer, $plan)
    {
        $client = $this->client($plan['routers']);
        if (!empty($plan['plan_expired'])) {
            $p = ORM::for_table("tbl_plans")->find_one($plan['plan_expired']);
            if ($p) {
                $this->add_customer($customer, $p);
                $this->removePpoeActive($client, $customer['username']);
                if (!empty($customer['pppoe_username'])) {
                    $this->removePpoeActive($client, $customer['pppoe_username']);
                }
                return;
            }
        }
        $this->removePpoeUser($client, $customer['username']);
        if (!empty($customer['pppoe_username'])) {
            $this->removePpoeUser($client, $customer['pppoe_username']);
        }
        $this->removePpoeActive($client, $customer['username']);
        if (!empty($customer['pppoe_username'])) {
            $this->removePpoeActive($client, $customer['pppoe_username']);
        }
    }

    function getIdByCustomer($customer, $client)
    {
        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $customer['username']));
        $id = $client->sendSync($printRequest)->getProperty('.id');
        if (empty($id) && !empty($customer['pppoe_username'])) {
            $printRequest = new RouterOS\Request('/ppp/secret/print');
            $printRequest->setQuery(RouterOS\Query::where('name', $customer['pppoe_username']));
            $id = $client->sendSync($printRequest)->getProperty('.id');
        }
        return $id;
    }

    // --------------------------------------
    // Métodos internos de PPPoE
    // --------------------------------------

    function addPpoeUser($client, $plan, $customer, $isExp = false)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/add');
        $setRequest->setArgument('service', 'pppoe');
        $setRequest->setArgument('profile', $plan['name_plan']);
        $setRequest->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id'])));
        $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
        $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }
        $client->sendSync($setRequest);
    }

    function removePpoeUser($client, $username)
    {
        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/secret/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }

    function removePpoeActive($client, $username)
    {
        $onlineRequest = new RouterOS\Request('/ppp/active/print');
        $onlineRequest->setArgument('.proplist', '.id');
        $onlineRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($onlineRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/active/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }
}
