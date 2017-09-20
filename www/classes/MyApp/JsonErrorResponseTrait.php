<?php
namespace MyApp;

/**
 * Трейт с методом подготовки ответа от апи с ошибкой в json-формате
 *
 * @package MyApp
 */
trait JsonErrorResponseTrait
{
    /**
     * Возвращает ответ с ошибкой в формате
     * {
     *   error: {
     *      status: Описание ошибки
     *      errorCode: Код ошибки
     *   }
     * }
     *
     * @param ResponseInterface $response запрос
     * @param int $errorCode код ошибки
     * @param string $errorStatus описание ошибки
     * @return ResponseInterface ответ с ошибкой
     */
    protected function prepareErrorResponse($response, $errorCode = 404, $errorStatus = 'Not Found')
    {
        if (!$response instanceof \Psr\Http\Message\ResponseInterface) {
            return;
        }

        return $response->withJson(
            [
                'status' => $errorStatus,
                'errorCode' => $errorCode
            ],
            $errorCode
        );
    }
}