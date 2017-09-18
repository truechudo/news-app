<?php
require_once '../vendor/autoload.php';
require_once '../config/config.php';

try {

    $bootstrap = new Bootstrap($config);
    $bootstrap->run();

} catch (Exception $e) {
    echo $e;
}

function mydebug($obj)
{
    echo "<hr><pre>";
    var_dump($obj);
    echo "</pre><hr>";
}
