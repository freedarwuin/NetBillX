<?php
/**
 *  PHP Mikrotik Billing (https://github.com/freedarwuin/NetBillX/)
 *  by https://wa.me/584224512433
 **/

if(function_exists($routes[1])){
    call_user_func($routes[1]);
}else{
    r2(getUrl('dashboard'), 'e', 'Function not found');
}