<?php

use PEAR2\Net\RouterOS;

class MikrotikPppoe
{
    // Descripción
    function description()
    {
        return [
            'title' => 'Mikrotik PPPOE',
            'description' => 'To handle connection between NetBillX with Mikrotik PPPOE',
            'author' => 'freedarwuin',
            'url' => [
                'Github' => 'https://github.com/freedarwuin/NetBillX/',
                'WhatsApp' => 'https://wa.me/584224512433',
                'Donate' => 'https://paypal.me/DPedroa'
            ]
        ];
    }

    // ======== CLIENTE ========
    function getClient($ip, $user, $pass)
    {
        global $_app_stage;
        if ($_app_stage == 'demo') return null;

        $iport = explode(":", $ip);
        try {
            return new RouterOS\Client($iport[0], $user, $pass, ($iport[1] ?? null));
        } catch (\Exception $e) {
            error_log("Error Mikrotik Client: " . $e->getMessage());
            return null;
        }
    }

    // ======== INFO ROUTER ========
    function info($name)
    {
        return ORM::for_table('tbl_routers')->where('name', $name)->find_one();
    }

    // ======== ADD CUSTOMER ========
    function add_customer($customer, $plan)
    {
        global $isChangePlan;

        $mikrotik = $this->info($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) return false;

        // Asegurar que el profile existe
        $this->ensureProfile($client, $plan);

        // Obtener ID si ya existe
        $cid = $this->getIdByCustomer($customer, $client);
        $isExp = ORM::for_table('tbl_plans')->select("id")->where('plan_expired', $plan['id'])->find_one();

        if (empty($cid)) {
            return $this->addPpoeUser($client, $plan, $customer, $isExp);
        } else {
            return $this->updatePpoeUser($client, $customer, $plan, $cid, $isExp);
        }
    }

    // ======== SINCRONIZAR CUSTOMER ========
    function sync_customer($customer, $plan)
    {
        return $this->add_customer($customer, $plan);
    }

    // ======== REMOVE CUSTOMER ========
    function remove_customer($customer, $plan)
    {
        $mikrotik = $this->info($plan['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) return false;

        if (!empty($plan['plan_expired'])) {
            $p = ORM::for_table("tbl_plans")->find_one($plan['plan_expired']);
            if ($p) {
                $this->add_customer($customer, $p);
                $this->removePpoeActive($client, $customer['username']);
                if (!empty($customer['pppoe_username'])) {
                    $this->removePpoeActive($client, $customer['pppoe_username']);
                }
                return true;
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

        return true;
    }

    // ======== ADD / UPDATE PROFILE ========
    private function ensureProfile($client, $plan)
    {
        $printRequest = new RouterOS\Request('/ppp/profile/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $plan['name_plan']));
        $profileID = $client->sendSync($printRequest)->getProperty('.id');

        if (empty($profileID)) {
            // Crear pool si no existe
            $this->ensurePool($client, $plan['pool']);

            $bw = ORM::for_table("tbl_bandwidth")->find_one($plan['id_bw']);
            $rate = $this->formatRate($bw);

            $pool = ORM::for_table("tbl_pool")->where("pool_name", $plan['pool'])->find_one();

            $addRequest = new RouterOS\Request('/ppp/profile/add');
            $addRequest->setArgument('name', $plan['name_plan'])
                       ->setArgument('local-address', $pool['local_ip'] ?? $pool['pool_name'])
                       ->setArgument('remote-address', $pool['pool_name'])
                       ->setArgument('rate-limit', $rate);
            $client->sendSync($addRequest);
        }
    }

    private function ensurePool($poolName)
    {
        $mikrotik = $this->info($poolName['routers']);
        $client = $this->getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
        if (!$client) return false;

        $printRequest = new RouterOS\Request('/ip/pool/print');
        $printRequest->setQuery(RouterOS\Query::where('name', $poolName['pool_name']));
        $poolID = $client->sendSync($printRequest)->getProperty('.id');

        if (empty($poolID)) {
            $addRequest = new RouterOS\Request('/ip/pool/add');
            $addRequest->setArgument('name', $poolName['pool_name'])
                       ->setArgument('ranges', $poolName['range_ip']);
            $client->sendSync($addRequest);
        }
    }

    private function formatRate($bw)
    {
        $unitUp = ($bw['rate_up_unit'] == 'Kbps') ? 'K' : 'M';
        $unitDown = ($bw['rate_down_unit'] == 'Kbps') ? 'K' : 'M';

        $rate = ($bw['rate_up'] == '0' || $bw['rate_down'] == '0') ? '' :
                "{$bw['rate_up']}{$unitUp}/{$bw['rate_down']}{$unitDown}";

        if (!empty(trim($bw['burst']))) $rate .= ' ' . $bw['burst'];
        return $rate;
    }

    // ======== ADD PPPOE USER ========
    private function addPpoeUser($client, $plan, $customer, $isExp = false)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/add');
        $setRequest->setArgument('service', 'pppoe');
        $setRequest->setArgument('profile', $plan['name_plan']);
        $setRequest->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email']);
        $setRequest->setArgument('name', $customer['pppoe_username'] ?? $customer['username']);
        $setRequest->setArgument('password', $customer['pppoe_password'] ?? $customer['password']);
        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }

        try {
            $client->sendSync($setRequest);
            return true;
        } catch (\Exception $e) {
            error_log("Error adding PPPOE user {$customer['username']}: " . $e->getMessage());
            return false;
        }
    }

    // ======== UPDATE PPPOE USER ========
    private function updatePpoeUser($client, $customer, $plan, $cid, $isExp)
    {
        $setRequest = new RouterOS\Request('/ppp/secret/set');
        $setRequest->setArgument('numbers', $cid)
                   ->setArgument('name', $customer['pppoe_username'] ?? $customer['username'])
                   ->setArgument('password', $customer['pppoe_password'] ?? $customer['password'])
                   ->setArgument('profile', $plan['name_plan'])
                   ->setArgument('comment', $customer['fullname'] . ' | ' . $customer['email']);

        if (!empty($customer['pppoe_ip']) && !$isExp) {
            $setRequest->setArgument('remote-address', $customer['pppoe_ip']);
        }

        try {
            $client->sendSync($setRequest);
            return true;
        } catch (\Exception $e) {
            error_log("Error updating PPPOE user {$customer['username']}: " . $e->getMessage());
            return false;
        }
    }

    // ======== GET ID BY CUSTOMER ========
    function getIdByCustomer($customer, $client)
    {
        $query = new RouterOS\Request('/ppp/secret/print');
        $query->setQuery(RouterOS\Query::where('name', $customer['username']));
        $id = $client->sendSync($query)->getProperty('.id');

        if (empty($id) && !empty($customer['pppoe_username'])) {
            $query->setQuery(RouterOS\Query::where('name', $customer['pppoe_username']));
            $id = $client->sendSync($query)->getProperty('.id');
        }

        return $id;
    }

    // ======== REMOVE PPPOE USER ========
    function removePpoeUser($client, $username)
    {
        $query = new RouterOS\Request('/ppp/secret/print');
        $query->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($query)->getProperty('.id');
        if ($id) {
            $remove = new RouterOS\Request('/ppp/secret/remove');
            $remove->setArgument('numbers', $id);
            $client->sendSync($remove);
        }
    }

    // ======== REMOVE PPPOE ACTIVE ========
    function removePpoeActive($client, $username)
    {
        $query = new RouterOS\Request('/ppp/active/print');
        $query->setQuery(RouterOS\Query::where('name', $username));
        $id = $client->sendSync($query)->getProperty('.id');
        if ($id) {
            $remove = new RouterOS\Request('/ppp/active/remove');
            $remove->setArgument('numbers', $id);
            $client->sendSync($remove);
        }
    }
}
