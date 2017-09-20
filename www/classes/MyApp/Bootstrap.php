<?php
namespace MyApp;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class Bootstrap
{
    private $app;
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->app = new \Slim\App(['settings' => $this->config]);
    }

    private function init()
    {
        $container = $this->app->getContainer();
        $container['db'] = function ($c) {
            $db = $c['settings']['mysql'];
            $pdo = new \PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            return $pdo;
        };

        $container['MyApp\Controller\AuthorAction'] = function($c) {
            return new Controller\AuthorAction($c->get('db'));
        };

        $container['errorHandler'] = function($c) {
            return new Handler\ErrorHandler();
        };

        $container['notAllowedHandler'] = function($c) {
            return new Handler\NotAllowedHandler();
        };

        $container['notFoundHandler'] = function($c) {
            return new Handler\NotFoundHandler();
        };

        $container['phpErrorHandler'] = function($c) {
            return new Handler\ErrorHandler();
        };
    }

    public function run()
    {
        $this->init();

        $this->app->group('/api/v1/author', function () {
            $this->map(['GET', 'DELETE', 'PUT', 'POST'], '[/{id}]', Controller\AuthorAction::class);
        });
        $this->app->run();

    }
}