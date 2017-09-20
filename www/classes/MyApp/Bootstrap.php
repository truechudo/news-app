<?php
namespace MyApp;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Класс для инициализации приложения
 *
 * @package MyApp
 */
class Bootstrap
{
    private $app;

    public function __construct(array $config, array $diConfig = [])
    {
        $this->app = new \Slim\App(array_merge(['settings' => $config], $diConfig));
    }

    public function run()
    {
        $this->app->group('/api/v1/author', function () {
            $this->map(['GET', 'DELETE', 'PUT', 'POST'], '[/{id}]', Controller\AuthorAction::class);
        });
        $this->app->run();

    }
}