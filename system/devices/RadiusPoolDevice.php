<?php

class RadiusPoolDevice {

    // show Description
    function description()
    {
        return [
            'title' => 'Radius Pool Device',
            'description' => 'Assigns static or dynamic IPs to PPPoE users based on existing pools',
            'author' => 'Dar',
            'url' => [
                'Github' => 'https://github.com/freedarwuin/NetBillX/',
                'WhatsApp' => 'https://wa.me/584224512433',
            ]
        ];
    }

    // Add Customer to device
    function add_customer($customer, $plan)
    {
        // Decide if IP is static or dynamic
        if (!empty($customer['ip_mode']) && $customer['ip_mode'] === 'static' && !empty($customer['static_ip'])) {
            // assign static IP
            $this->upsertCustomerAttr($customer['username'], 'Framed-IP-Address', $customer['static_ip'], ':=');
        } else {
            // assign IP from pool defined in plan
            $this->upsertCustomerAttr($customer['username'], 'Framed-Pool', $plan['pool'], ':=');
            $this->upsertCustomerAttr($customer['username'], 'Framed-IP-Address', '0.0.0.0', ':=');
        }

        // Assign password and plan group
        $this->upsertCustomer($customer['username'], 'Cleartext-Password', $customer['password']);
        $this->upsertCustomer($customer['username'], 'Simultaneous-Use', $plan['shared_users']);
    }

    // Remove Customer to device
    function remove_customer($customer, $plan)
    {
        // reset IP and usage
        $this->upsertCustomerAttr($customer['username'], 'Framed-Pool', '', ':=');
        $this->upsertCustomerAttr($customer['username'], 'Framed-IP-Address', '', ':=');
    }

    // customer change username
    public function change_username($plan, $from, $to)
    {
        // Example: copy attributes to new username
    }

    // Add Plan to device
    function add_plan($plan)
    {
        // Map pool and limits
    }

    // Update Plan to device
    function update_plan($old_name, $plan)
    {
    }

    // Remove Plan from device
    function remove_plan($plan)
    {
    }

    // check if customer is online
    function online_customer($customer, $router_name)
    {
    }

    // make customer online
    function connect_customer($customer, $ip, $mac_address, $router_name)
    {
    }

    // make customer disconnect
    function disconnect_customer($customer, $router_name)
    {
    }

    // ----------------------------
    // Helpers for RADIUS attributes
    // ----------------------------
    private function upsertCustomer($username, $attr, $value, $op = ':=')
    {
        $r = ORM::for_table('radcheck', 'radius')->where_equal('username', $username)->where_equal('attribute', $attr)->find_one();
        if (!$r) {
            $r = ORM::for_table('radcheck', 'radius')->create();
            $r->username = $username;
        }
        $r->attribute = $attr;
        $r->op = $op;
        $r->value = $value;
        $r->save();
        return true;
    }

    private function upsertCustomerAttr($username, $attr, $value, $op = ':=')
    {
        $r = ORM::for_table('radreply', 'radius')->where_equal('username', $username)->where_equal('attribute', $attr)->find_one();
        if (!$r) {
            $r = ORM::for_table('radreply', 'radius')->create();
            $r->username = $username;
        }
        $r->attribute = $attr;
        $r->op = $op;
        $r->value = $value;
        $r->save();
        return true;
    }

}
