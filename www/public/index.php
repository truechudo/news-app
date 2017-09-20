<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';
require_once '../config/diConfig.php';

try {

    $bootstrap = new \MyApp\Bootstrap($config, $diConfig);
    $bootstrap->run();

} catch (Exception $e) {
    echo $e;
}