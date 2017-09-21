<?php
namespace MyApp\Handler;

/**
 * Обработка php-ошибок и исключений приложения
 *
 * @package MyApp
 */
class ErrorHandler
{
    use \MyApp\JsonErrorResponseTrait;

    public function __invoke($request, $response, $error)
    {
        if ($error instanceof \Throwable) {
            $this->writeErrorLog($error);
        }

        if ($error instanceof \Exception) {
            return $this->prepareErrorResponse($response, 500, 'Внутренняя ошибка сервера');
        }

        return $this->prepareErrorResponse($response, 500, 'Внутренняя ошибка сервера');
    }

    /**
     * Пишет ошибку в error log
     *
     * @param \Exception|\Throwable $throwable
     *
     * @return void
     */
    protected function writeErrorLog($throwable)
    {
        $message = '';
        $message .= $this->renderThrowableAsText($throwable);
        while ($throwable = $throwable->getPrevious()) {
            $message .= PHP_EOL . 'Previous error:' . PHP_EOL;
            $message .= $this->renderThrowableAsText($throwable);
        }
        error_log($message);
    }

    /**
     * Представляет ошибку в текстовое сообщение
     *
     * @param \Exception|\Throwable $throwable
     *
     * @return string
     */
    protected function renderThrowableAsText($throwable)
    {
        $text = sprintf('Type: %s' . PHP_EOL, get_class($throwable));

        if ($code = $throwable->getCode()) {
            $text .= sprintf('Code: %s' . PHP_EOL, $code);
        }

        if ($message = $throwable->getMessage()) {
            $text .= sprintf('Message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $throwable->getFile()) {
            $text .= sprintf('File: %s' . PHP_EOL, $file);
        }

        if ($line = $throwable->getLine()) {
            $text .= sprintf('Line: %s' . PHP_EOL, $line);
        }

        if ($trace = $throwable->getTraceAsString()) {
            $text .= sprintf('Trace: %s', $trace);
        }

        return $text;
    }
}