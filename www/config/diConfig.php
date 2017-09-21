<?php
/**
 * Конфиг с описанием сервисов для di-контейнера
 */

$diConfig['db'] = function ($c) {
    $db = $c['settings']['mysql'];
    if (empty($db['host']) || empty($db['dbname']) || empty($db['user']) || empty($db['pass'])) {
        throw new \Exception('Не указаны настройки для подключения к базе данных');
    }
    $pdo = new \PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $pdo;
};

$diConfig['MyApp\Controller\AuthorAction'] = function($c) {
    return new MyApp\Controller\AuthorAction($c->get('db'));
};

$diConfig['phpErrorHandler'] = function($c) {
    return new MyApp\Handler\ErrorHandler();
};

$diConfig['errorHandler'] = function($c) {
    return new MyApp\Handler\ErrorHandler();
};

$diConfig['notAllowedHandler'] = function($c) {
    return new MyApp\Handler\NotAllowedHandler();
};

$diConfig['notFoundHandler'] = function($c) {
    return new MyApp\Handler\NotFoundHandler();
};
