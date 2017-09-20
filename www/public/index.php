<?php
/**
 * Точка входа в API
 */
require_once '../vendor/autoload.php';
require_once '../config/config.php';
require_once '../config/diConfig.php';

$bootstrap = new \MyApp\Bootstrap($config, $diConfig);
$bootstrap->run();
