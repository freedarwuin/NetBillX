<?php

use PEAR2\Net\RouterOS;

class pppoe
{
    // Información del plugin
    public function description()
    {
        return [
            'title' => 'Mikrotik PPPOE',
            'description' => 'Conexión entre NetBillX y Mikrotik PPPOE',
            'author' => 'freedarwuin',
            'url' => [
                'Github' => 'https://github.com/freedarwuin/NetBillX/',
                'WhatsApp' => 'https://wa.me/584224512433',
                'Donate' => 'https://paypal.me/DPedroa'
            ]
        ];
    }

    // ======== CLIENTE ========
    private function getRouterInfo($name)
    {
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    private function getClient($ip, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $iport = explode(":", $ip);
        return new RouterOS\Client($iport[0], $user, $pass, $iport[1] ?? null);
    }

    // ======== CLIENTE PPPoE ========
    public function add_customer($customer, $plan)
    {
        $mikrotik = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $cid = $this->getIdByCustomer($customer, $client);

        $isExp = ORM::for_table('tbl_plans')->select("id")->where('plan_expired', $plan['id'])->find_one();

        if (empty($cid)) {
            $this->addPpoeUser($client, $plan, $customer, $isExp);
        } else {
            $setRequest = new RouterOS\Request('/ppp/secret/set');
            $setRequest->setArgument('numbers', $cid);
            $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
            $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
            $setRequest->setArgument('profile', $plan['name_plan']);
            $setRequest->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id'])));

            if (!empty($customer['pppoe_ip']) && !$isExp) {
                $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
            }

            $client->sendSync($setRequest);
        }
    }

    public function remove_customer($customer, $plan)
    {
        $mikrotik = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        $this->removePpoeUser($client, $customer['pppoe_username'] ?? $customer['username']);
        $this->removePpoeActive($client, $customer['pppoe_username'] ?? $customer['username']);
    }

    public function change_username($plan, $from, $to)
    {
        $mikrotik = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $from));
        $cid = $client->sendSync($printRequest)->getProperty('.id');

        if (!empty($cid)) {
            $setRequest = new RouterOS\Request('/ppp/secret/set');
            $setRequest->setArgument('numbers', $cid);
            $setRequest->setArgument('name', $to);
            $client->sendSync($setRequest);
            $this->removePpoeActive($client, $from);
        }
    }

    public function online_customer($customer, $router_name)
    {
        $router = $this->getRouterInfo($router_name);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $namesToCheck = [$customer['username']];
        if (!empty($customer['pppoe_username'])) $namesToCheck[] = $customer['pppoe_username'];

        foreach ($namesToCheck as $name) {
            $printRequest = new RouterOS\Request('/ppp/active/print');
            $printRequest->setQuery(RouterOS\Query::where('name', $name));
            $id = $client->sendSync($printRequest)->getProperty('.id');
            if (!empty($id)) return $id;
        }
        return null;
    }

    // ======== PLANES ========
    public function add_plan($plan)
    {
        $mikrotik = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        $bw = ORM::for_table("tbl_bandwidth")->find_one($plan['id_bw']);
        $rate = ($bw['rate_up'] ?? 0) . ($bw['rate_up_unit'] === 'Kbps' ? 'K' : 'M') . "/" . ($bw['rate_down'] ?? 0) . ($bw['rate_down_unit'] === 'Kbps' ? 'K' : 'M');
        if (!empty($bw['burst'])) $rate .= ' ' . $bw['burst'];

        $pool = ORM::for_table("tbl_pool")->where("pool_name", $plan['pool'])->find_one();
        $addRequest = new RouterOS\Request('/ppp/profile/add');
        $client->sendSync($addRequest
            ->setArgument('name', $plan['name_plan'])
            ->setArgument('local-address', $pool['local_ip'] ?: $pool['pool_name'])
            ->setArgument('remote-address', $pool['pool_name'])
            ->setArgument('rate-limit', $rate)
        );
    }

    public function remove_plan($plan)
    {
        $mikrotik = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        $printRequest = new RouterOS\Request('/ppp/profile/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $plan['name_plan']));
        $profileID = $client->sendSync($printRequest)->getProperty('.id');

        if ($profileID) {
            $removeRequest = new RouterOS\Request('/ppp/profile/remove');
            $removeRequest->setArgument('numbers', $profileID);
            $client->sendSync($removeRequest);
        }
    }

    // ======== POOL ========
    public function add_pool($pool)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $mikrotik = $this->getRouterInfo($pool['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);

        $addRequest = new RouterOS\Request('/ip/pool/add');
        $addRequest->setArgument('name', $pool['pool_name']);
        $addRequest->setArgument('ranges', $pool['range_ip']);
        $client->sendSync($addRequest);
    }

    public function removePpoeUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/secret/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }

    public function removePpoeActive($client, $username)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $printRequest = new RouterOS\Request('/ppp/active/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/active/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }

    public function getIdByCustomer($customer, $client)
    {
        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $customer['username']));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if (!$id && !empty($customer['pppoe_username'])) {
            $printRequest = new RouterOS\Request('/ppp/secret/print');
            $printRequest->setQuery(RouterOS\Query::where('name', $customer['pppoe_username']));
            $id = $client->sendSync($printRequest)->getProperty('.id');
        }

        return $id;
    }

    public function addPpoeUser($client, $plan, $customer, $isExp = false)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/add');
        $setRequest->setArgument('service', 'pppoe');
        $setRequest->setArgument('profile', $plan['name_plan']);
        $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
        $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
        $setRequest->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email'] . ' | ' . implode(', ', User::getBillNames($customer['id'])));
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }
        $client->sendSync($setRequest);
    }
}
