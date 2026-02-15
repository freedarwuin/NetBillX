<?php

use PEAR2\Net\RouterOS;

class pppoe
{
    // ===== Información del plugin =====
    public function description()
    {
        return [
            'title' => 'Mikrotik PPPOE',
            'description' => 'Gestión de clientes PPPoE en Mikrotik desde NetBillX',
            'author' => 'freedarwuin',
            'url' => [
                'Github' => 'https://github.com/freedarwuin/NetBillX/',
                'WhatsApp' => 'https://wa.me/584224512433?text=Hola%2C%20uso%20NetBillX%20y%20necesito%20soporte.',
                'Donate' => 'https://paypal.me/DPedroa'
            ]
        ];
    }

    // ===== Cliente Mikrotik =====
    private function getClient($ip, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return null;

        $parts = explode(':', $ip);
        $port = $parts[1] ?? null;
        return new RouterOS\Client($parts[0], $user, $pass, $port);
    }

    private function getRouterInfo($name)
    {
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    // ===== Gestión de clientes =====
    public function add_customer($customer, $plan)
    {
        global $isChangePlan;

        $router = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);
        $cid = $this->getIdByCustomer($customer, $client);
        $isExp = ORM::for_table('tbl_plans')->where('plan_expired', $plan['id'])->find_one();

        if (!$cid) {
            $this->addPppoeUser($client, $plan, $customer, $isExp);
        } else {
            $this->updateExistingCustomer($client, $plan, $customer, $cid, $isExp);
        }
    }

    public function sync_customer($customer, $plan)
    {
        $this->add_customer($customer, $plan);
    }

    public function remove_customer($customer, $plan)
    {
        $router = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        if (!empty($plan['plan_expired'])) {
            $expiredPlan = ORM::for_table("tbl_plans")->find_one($plan['plan_expired']);
            if ($expiredPlan) {
                $this->add_customer($customer, $expiredPlan);
                $this->removePppoeActive($client, $customer['username']);
                if (!empty($customer['pppoe_username'])) {
                    $this->removePppoeActive($client, $customer['pppoe_username']);
                }
                return;
            }
        }

        $this->removePppoeUser($client, $customer['username']);
        if (!empty($customer['pppoe_username'])) {
            $this->removePppoeUser($client, $customer['pppoe_username']);
        }
        $this->removePppoeActive($client, $customer['username']);
        if (!empty($customer['pppoe_username'])) {
            $this->removePppoeActive($client, $customer['pppoe_username']);
        }
    }

    public function change_username($plan, $from, $to)
    {
        $router = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $from));
        $cid = $client->sendSync($printRequest)->getProperty('.id');

        if ($cid) {
            $setRequest = new RouterOS\Request('/ppp/secret/set');
            $setRequest->setArgument('numbers', $cid);
            $setRequest->setArgument('name', $to);
            $client->sendSync($setRequest);
            $this->removePppoeActive($client, $from);
        }
    }

    // ===== Planes =====
    public function add_plan($plan)
    {
        $this->update_plan(null, $plan);
    }

    public function update_plan($oldPlan, $newPlan)
    {
        $router = $this->getRouterInfo($newPlan['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        $profileID = null;
        if ($oldPlan) {
            $printRequest = new RouterOS\Request('/ppp/profile/print .proplist=.id', RouterOS\Query::where('name', $oldPlan['name_plan']));
            $profileID = $client->sendSync($printRequest)->getProperty('.id');
        }

        $bw = ORM::for_table("tbl_bandwidth")->find_one($newPlan['id_bw']);
        $unitUp = $bw['rate_up_unit'] === 'Kbps' ? 'K' : 'M';
        $unitDown = $bw['rate_down_unit'] === 'Kbps' ? 'K' : 'M';
        $rate = ($bw['rate_up'] && $bw['rate_down']) ? $bw['rate_up'].$unitUp.'/'.$bw['rate_down'].$unitDown : '';
        if (!empty(trim($bw['burst']))) $rate .= ' '.$bw['burst'];

        $pool = ORM::for_table("tbl_pool")->where("pool_name", $newPlan['pool'])->find_one();

        $setRequest = $profileID ? new RouterOS\Request('/ppp/profile/set') : new RouterOS\Request('/ppp/profile/add');
        if ($profileID) $setRequest->setArgument('numbers', $profileID);

        $setRequest->setArgument('name', $newPlan['name_plan'])
                   ->setArgument('local-address', $pool['local_ip'] ?: $pool['pool_name'])
                   ->setArgument('remote-address', $pool['pool_name'])
                   ->setArgument('rate-limit', $rate)
                   ->setArgument('on-up', $newPlan['on_login'] ?? '')
                   ->setArgument('on-down', $newPlan['on_logout'] ?? '');

        $client->sendSync($setRequest);
    }

    public function remove_plan($plan)
    {
        $router = $this->getRouterInfo($plan['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        $printRequest = new RouterOS\Request('/ppp/profile/print .proplist=.id', RouterOS\Query::where('name', $plan['name_plan']));
        $profileID = $client->sendSync($printRequest)->getProperty('.id');

        if ($profileID) {
            $removeRequest = new RouterOS\Request('/ppp/profile/remove');
            $removeRequest->setArgument('numbers', $profileID);
            $client->sendSync($removeRequest);
        }
    }

    // ===== Pools =====
    public function add_pool($pool)
    {
        $this->update_pool(null, $pool);
    }

    public function update_pool($oldPool, $newPool)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return;

        $router = $this->getRouterInfo($newPool['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        $poolID = null;
        if ($oldPool) {
            $printRequest = new RouterOS\Request('/ip/pool/print .proplist=.id', RouterOS\Query::where('name', $oldPool['pool_name']));
            $poolID = $client->sendSync($printRequest)->getProperty('.id');
        }

        $setRequest = $poolID ? new RouterOS\Request('/ip/pool/set') : new RouterOS\Request('/ip/pool/add');
        if ($poolID) $setRequest->setArgument('numbers', $poolID);

        $setRequest->setArgument('name', $newPool['pool_name'])
                   ->setArgument('ranges', $newPool['range_ip']);

        $client->sendSync($setRequest);
    }

    public function remove_pool($pool)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return;

        $router = $this->getRouterInfo($pool['routers']);
        $client = $this->getClient($router['ip_address'], $router['username'], $router['password']);

        $printRequest = new RouterOS\Request('/ip/pool/print .proplist=.id', RouterOS\Query::where('name', $pool['pool_name']));
        $poolID = $client->sendSync($printRequest)->getProperty('.id');

        if ($poolID) {
            $removeRequest = new RouterOS\Request('/ip/pool/remove');
            $removeRequest->setArgument('numbers', $poolID);
            $client->sendSync($removeRequest);
        }
    }

    // ===== Métodos internos =====
    private function getIdByCustomer($customer, $client)
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

    private function addPppoeUser($client, $plan, $customer, $isExp = false)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/add');
        $setRequest->setArgument('service', 'pppoe');
        $setRequest->setArgument('profile', $plan['name_plan']);
        $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
        $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }
        $setRequest->setArgument('comment', $customer['fullname'].' | '.$customer['email'].' | '.implode(', ', User::getBillNames($customer['id'])));
        $client->sendSync($setRequest);
    }

    private function updateExistingCustomer($client, $plan, $customer, $cid, $isExp)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/set');
        $setRequest->setArgument('numbers', $cid);
        $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
        $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
        $setRequest->setArgument('profile', $plan['name_plan']);
        $setRequest->setArgument('comment', $customer['fullname'].' | '.$customer['email'].' | '.implode(', ', User::getBillNames($customer['id'])));
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        } else {
            $unsetRequest = new RouterOS\Request('/ppp/secret/unset');
            $unsetRequest->setArgument('.id', $cid);
            $unsetRequest->setArgument('value-name','remote-address');
            $client->sendSync($unsetRequest);
        }
        $client->sendSync($setRequest);
    }

    private function removePppoeUser($client, $username)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return;

        $printRequest = new RouterOS\Request('/ppp/secret/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/secret/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }

    private function removePppoeActive($client, $username)
    {
        global $_app_stage;
        if ($_app_stage === 'demo') return;

        $printRequest = new RouterOS\Request('/ppp/active/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if ($id) {
            $removeRequest = new RouterOS\Request('/ppp/active/remove');
            $removeRequest->setArgument('numbers', $id);
            $client->sendSync($removeRequest);
        }
    }
}
