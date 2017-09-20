<?php
namespace MyApp\Handler;

/**
 * Class ExceptionHandler
 * @package MyApp
 */
class NotFoundHandler
{
    use \MyApp\JsonErrorResponseTrait;

    public function __invoke($request, $response)
    {
        return $this->prepareErrorResponse($response, 404, 'Service not found!');
    }
}