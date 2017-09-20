<?php
namespace MyApp\Handler;

/**
 * Class ErrorHandler
 * @package MyApp
 */
class ErrorHandler
{
    use \MyApp\JsonErrorResponseTrait;

    public function __invoke($request, $response, $error)
    {
        if ($error instanceof \Exception) {
            return $this->prepareErrorResponse($response, 500, $error->getMessage());
        }

        return $this->prepareErrorResponse($response, 500, 'Internal server error');
    }
}