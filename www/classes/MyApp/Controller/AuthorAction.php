<?php
namespace MyApp\Controller;

use MyApp\Author as Author;

/**
 * REST API для действий над автором новостей
 *
 * @package Controller
 */
class AuthorAction
{
    use \MyApp\JsonErrorResponseTrait;

    /**
     * Маппер для объекта Author
     *
     * @var Author\AuthorMapper
     */

    protected $authorMapper;

    /**
     * Создание контроллера апи
     *
     * @param PDO $db подключение в БД
     */
    public function __construct($db)
    {
        $this->authorMapper = new Author\AuthorMapper($db);
    }

    /**
     * Вызов нужного метода апи
     *
     * @param ServerRequestInterface $request запрос
     *
     * @param array $args параметры запроса
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $args = [])
    {
        switch ($request->getMethod()) {
            case 'GET':
                return $this->getAuthor($response, $args);
            case 'DELETE':
                return $this->deleteAuthor($response, $args);
            case 'PUT':
                return $this->updateAuthor($request, $response, $args);
            case 'POST':
                return $this->addAuthor($request, $response);
        }
        return $response;
    }

    /**
     * Получение автора по его ID.
     * Возвращает данные автора в json-формате
     * {
     *   "id": 15,
     *   "name": "Стас",
     *   "nameAblative": "Со Стасом Соколовым",
     *   "avatar": {
     *      "fileName": "/preview/image.jpg",
     *      "width": "30",
     *      "height": "50"
     *   }
     * }
     *
     * @param ResponseInterface $response ответ
     * @param array $args параметры запроса
     * @return ResponseInterface ответ с json данными автора
     */
    private function getAuthor($response, $args)
    {
        if (empty($args['id']) || !filter_var($args['id'], FILTER_VALIDATE_INT)) {
            return $this->prepareErrorResponse($response, 400, "Неверный запрос. ID должен быть целым числом");
        }

        $author = $this->authorMapper->getAuthor($args['id']);
        if (empty($author)) {
            return $this->prepareErrorResponse($response);
        }

        return $response->withJson($author->toArray());
    }

    /**
     * Удаление автора по его ID
     *
     * @param ResponseInterface $response ответ
     * @param array $args параметры запроса
     * @return ResponseInterface пустой ответ
     */
    private function deleteAuthor($response, $args)
    {
        if (empty($args['id']) || !filter_var($args['id'], FILTER_VALIDATE_INT)) {
            return $this->prepareErrorResponse($response, 400, "Неверный запрос. ID должен быть целым числом");
        }

        if ($this->authorMapper->deleteAuthor($args['id'])) {
            return $response->withStatus(204);
        }

        return $this->prepareErrorResponse($response);
    }

    /**
     * Обновление автора по его ID
     *
     * @param ServerRequestInterface $request запрос
     * @param ResponseInterface $response ответ
     * @param array $args параметры запроса
     * @return ResponseInterface ответ с id автора
     */
    private function updateAuthor($request, $response, $args)
    {
        if (empty($args['id']) || !filter_var($args['id'], FILTER_VALIDATE_INT)) {
            return $this->prepareErrorResponse($response, 400, "Неверный запрос. ID должен быть целым числом");
        }

        $input = json_decode($request->getBody(), true);

        try {
            $author['id'] = intval($args['id']);
            $author = Author\Author::createFromArray($input);

            if (!$this->authorMapper->saveAuthor($author)) {
                return $this->prepareErrorResponse($response);
            }

        } catch (Author\Exception $e) {
            return $this->prepareErrorResponse($response, 400, $e->getMessage());
        }

        return $response->withJson(['id' => $args['id']]);
    }

    /**
     * Добавление нового автора
     *
     * @param ServerRequestInterface $request запрос
     * @param ResponseInterface $response ответ
     * @return ResponseInterface ответ с id нового автора
     */
    private function addAuthor($request, $response)
    {
        $input = json_decode($request->getBody(), true);

        try {
            $author = Author\Author::createFromArray($input);

            $id = $this->authorMapper->saveAuthor($author);

        } catch (Author\Exception $e) {
            return $this->prepareErrorResponse($response, 400, $e->getMessage());
        }

        if (empty($id)) {
            return $this->prepareErrorResponse($response);
        }

        return $response->withJson(['id' => $id]);
    }
}