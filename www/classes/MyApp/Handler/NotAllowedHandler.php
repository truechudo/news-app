<?php
namespace MyApp\Handler;

/**
 * Обработка ошибки Method Not Allowed
 *
 * @package MyApp
 */
class NotAllowedHandler
{
    use \MyApp\JsonErrorResponseTrait;

    /**
     * Возвращает 405 ошибку, если запрашиваемый HTTP-метод не поддерживается
     *
     * @param ServerRequestInterface $request запрос
     * @param ResponseInterface $response ответ
     * @return ResponseInterface ответ с ошибкой
     * @param array $methods разрешенные методы
     * @return ResponseInterface ответ с ошибкой
     */
    public function __invoke($request, $response, $methods)
    {
        return $this->prepareErrorResponse($response, 405, 'Метод должен быть один из: ' . implode(', ', $methods));
    }
}