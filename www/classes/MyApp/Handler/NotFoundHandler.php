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
     * @param ServerRequestInterface $request запрос
     * @param ResponseInterface $response ответ
     * @return ResponseInterface ответ с ошибкой
     */
    public function __invoke($request, $response)
    {
        return $this->prepareErrorResponse($response, 404, 'Сервис не найден');
    }
}