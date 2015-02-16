<?php

/**
 * Paytoshi Faucet Script
 * 
 * Author: Looptribe
 * Website: http://paytoshi.org
 * Contact: info@paytoshi.org
 */

/*
 * A Slim application does not presume anything about sessions. 
 * If you prefer to use a PHP session, you must configure and 
 * start a native PHP session with session_start() before you instantiate the Slim application.
 * 
 * You should also disable PHP’s session cache limiter so that PHP 
 * does not send conflicting cache expiration headers with the HTTP response. 
 */
session_cache_limiter(false);
session_start();

require_once __DIR__.'/vendor/autoload.php';
$app = new Looptribe\Paytoshi\App();
$app->run();
