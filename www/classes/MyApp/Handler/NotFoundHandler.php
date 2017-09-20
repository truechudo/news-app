<?php
namespace MyApp\Handler;

/**
 * Обработка ошибки, если запрашивается несуществующий адрес
 *
 * @package MyApp
 */
class NotFoundHandler
{
    use \MyApp\JsonErrorResponseTrait;

    /**
     * Возвращает ответ в формате json с кодом ошибки 404
     *
     * @param $request
     * @param $response
     * @return \MyApp\ResponseInterface
     */
    public function __invoke($request, $response)
    {
        return $this->prepareErrorResponse($response, 404, 'Сервис не найден');
    }
}