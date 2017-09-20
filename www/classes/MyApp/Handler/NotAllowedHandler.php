<?php
namespace MyApp\Handler;

/**
 * Class NotAllowedHandler
 * @package MyApp
 */
class NotAllowedHandler
{
    use \MyApp\JsonErrorResponseTrait;

    public function __invoke($request, $response, $methods)
    {
        return $this->prepareErrorResponse($response, 405, 'Method must be one of: ' . implode(', ', $methods));
    }
}