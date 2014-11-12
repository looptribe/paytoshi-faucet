<?php

/**
 * Paytoshi Faucet Script
 * 
 * Author: Looptribe
 * Website: http://paytoshi.org
 * Contact: info@paytoshi.org
 */

require_once __DIR__.'/vendor/autoload.php';
$app = new Looptribe\Paytoshi\App();
$app->run();
