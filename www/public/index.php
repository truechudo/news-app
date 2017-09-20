<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

try {

    $bootstrap = new \MyApp\Bootstrap($config);
    $bootstrap->run();

} catch (Exception $e) {
    echo $e;
}