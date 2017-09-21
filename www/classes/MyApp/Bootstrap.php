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

    /**
     * Конструктор, ининализирем приложение \Slim\App
     *
     * @param array $config массив с настройками для \Slim\App
     * @param array $diConfig конфиг для DI-контейнера
     */
    public function __construct(array $config, array $diConfig = [])
    {
        $this->app = new \Slim\App(array_merge(['settings' => $config], $diConfig));
    }

    /**
     * Роутинг приложения
     */
    public function run()
    {
        $this->app->group('/api/v1/author', function () {
            $this->map(['GET', 'DELETE', 'PUT'], '[/{id}]', Controller\AuthorAction::class);
            $this->post('', Controller\AuthorAction::class);
        });
        $this->app->run();
    }
}