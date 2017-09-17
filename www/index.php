<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'config/config.php';

try {
    $app = new \Slim\App(['settings' => $config]);

    $container = $app->getContainer();

    $container['db'] = function ($c) {
        $db = $c['settings']['mysql'];
        $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };

    $app->get('/api/swagger.json', function (Request $request, Response $response) {

        try {
            $swagger = \Swagger\scan('classes/Controller/');
        } catch (Exception $e) {
            echo $e;
        }

        $response->getBody()->write($swagger);

    })->add(function ($request, $response, $next) {
        $response = $next($request, $response);
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    });

    $app->group('/api/v1/author', function () use ($app) {

        $app->map(['GET', 'DELETE', 'PUT', 'POST'], '[/{id}]', Controller\AuthorAction::class);

    })->add(function ($request, $response, $next) {
        $response = $next($request, $response);
        return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
    });

    $app->run();

} catch (Exception $e) {
    echo $e;
}

function mydebug($obj)
{
    echo "<hr><pre>";
    var_dump($obj);
    echo "</pre><hr>";
}
