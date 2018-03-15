<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'think\\worker\\' => array($vendorDir . '/topthink/think-worker/src'),
    'think\\composer\\' => array($vendorDir . '/topthink/think-installer/src'),
    'think\\captcha\\' => array($vendorDir . '/topthink/think-captcha/src'),
    'think\\' => array($baseDir . '/thinkphp/library/think'),
    'Workerman\\' => array($vendorDir . '/workerman/workerman'),
    'Lcobucci\\JWT\\' => array($vendorDir . '/lcobucci/jwt/src'),
    'GatewayWorker\\' => array($vendorDir . '/workerman/gateway-worker/src'),
    'GatewayClient\\' => array($vendorDir . '/workerman/gatewayclient'),
);
