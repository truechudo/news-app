<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Bootstrap {

    private $_app;

    private $_config;

    public function __construct(array $config) {
        $this->_config = $config;
        $this->_app = new \Slim\App(['settings' => $this->_config]);
    }

    private function _init()
    {
        $container = $this->_app->getContainer();
        $container['db'] = function ($c) {
            $db = $c['settings']['mysql'];
            $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return $pdo;
        };
    }

    public function run()
    {
        $this->_init();

        $app = $this->_app;
        $app->group('/api/v1/author', function () use ($app) {

            $app->map(['GET', 'DELETE', 'PUT', 'POST'], '[/{id}]', Controller\AuthorAction::class);

        })->add(function ($request, $response, $next) {
            $response = $next($request, $response);
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        });

        $app->run();
    }

}