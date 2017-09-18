<?php
namespace Controller;
use Model;

/**
 * @SWG\Swagger(
 *      @SWG\Info(
 *          version="1.0.0",
 *          title="Author API"
 *      ),
 *      host="test.local",
 *      basePath="/api/v1",
 *      schemes={"http"},
 *      consumes={"application/json"},
 *      produces={"application/json"}
 * )
 *
 * Class AuthorAction
 * @package Controller
 */
class AuthorAction {

    protected $_container;

    protected $_authorModel;

    public function __construct($container) {

        $this->_container = $container;
        $this->_authorModel = new Model\Author($this->_container->db);
    }

    public function __invoke($request, $response, $args = []) {

        switch ($request->getMethod()) {
            case 'GET':
                return $this->_getAuthor($response, $args);
            case 'DELETE':
                return $this->_deleteAuthor($response, $args);
            case 'PUT':
                return $this->_updateAuthor($request, $response, $args);
            case 'POST':
                return $this->_addAuthor($request, $response);
        }
        return $response;
    }

    /**
     * @SWG\Get(
     *  path="/author/{id}",
     *  summary="Get Author",
     *  description="Returns a author based on a single ID",
     *  @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID of author to return",
     *      required=true,
     *      type="integer",
     *      format="int32"
     *  ),
     *  @SWG\Response(
     *      response=200,
     *      description="Success result"
     *  ),
     *  @SWG\Response(
     *      response=404,
     *      description="Author not found"
     *  )
     * )
     *
     * @param $response
     * @param $args
     * @return mixed
     */
    private function _getAuthor($response, $args) {

        if (!empty($args['id'])) {
            $author = $this->_authorModel->getAutor($args['id']);
        }

        if (empty($author)) {
            return $this->_prepareErrorResponse($response);
        }

        return $response->getBody()->write(
            json_encode($author)
        );
    }

    /**
     * @SWG\Delete(
     *  path="/author/{id}",
     *  summary="Delete Author",
     *  description="Deletes a author",
     *  @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID of author to delete",
     *      required=true,
     *      type="integer",
     *      format="int32"
     *  ),
     *  @SWG\Response(
     *      response=200,
     *      description="Success result"
     *  ),
     *  @SWG\Response(
     *      response=404,
     *      description="Author not found"
     *  )
     * )
     *
     * @param $response
     * @param $args
     * @return mixed
     */
    private function _deleteAuthor($response, $args) {

        if (!empty($args['id'])) {
            if ($this->_authorModel->deleteAuthor($args['id'])) {
                return $response->getBody()->write('');
            }
        }
        return $this->_prepareErrorResponse($response);
    }

    /**
     * @SWG\Put(
     *  path="/author/{id}",
     *  summary="Update Author",
     *  description="Updates a author based on a single ID",
     *  @SWG\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID of author to update",
     *      required=true,
     *      type="integer",
     *      format="int32"
     *  ),
     *  @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      description="Author object",
     *      required=true,
     *      @SWG\Schema(ref="#definitions/Author")
     *  ),
     *  @SWG\Response(
     *      response=200,
     *      description="Success result"
     *  ),
     *  @SWG\Response(
     *      response=405,
     *      description="Validation error"
     *  ),
     *  @SWG\Response(
     *      response=404,
     *      description="Author not found"
     *  )
     * )
     *
     * @param $response
     * @param $args
     * @return mixed
     */
    private function _updateAuthor($request, $response, $args) {

        $input = json_decode($request->getBody());

        $errors = $this->_validateAuthor((array)$input, false);

        if (!empty($errors)) {
            return $this->_prepareErrorResponse($response, 405, $errors);
        }

        $authorId = $args['id'] ?? NULL;

        if (empty($authorId)) {
            return $this->_prepareErrorResponse($response);
        }

        if (!$this->_authorModel->updateAuthor($authorId, (array)$input)) {
            return $this->_prepareErrorResponse($response);
        }

        return $response->getBody()->write(
            json_encode(['id' => $authorId])
        );
    }

    /**
     * @SWG\Post(
     *  path="/author/{id}",
     *  summary="Add new Author",
     *  description="Adds a new author",
     *  @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      description="Author object",
     *      required=true,
     *      @SWG\Schema(ref="#definitions/Author")
     *  ),
     *  @SWG\Response(
     *      response=200,
     *      description="Success result"
     *  ),
     *  @SWG\Response(
     *      response=405,
     *      description="Validation error"
     *  ),
     *  @SWG\Response(
     *      response=404,
     *      description="Author not found"
     *  )
     * )
     *
     * @param $response
     * @param $args
     * @return mixed
     */
    private function _addAuthor($request, $response) {

        $input = json_decode($request->getBody());

        $errors = $this->_validateAuthor((array)$input);

        if (!empty($errors)) {
            return $this->_prepareErrorResponse($response, 405, $errors);
        }

        $id = $this->_authorModel->addAuthor((array)$input);

        if (empty($id)) {
            return $this->_prepareErrorResponse($response);
        }

        return $response->getBody()->write(
            json_encode(['id' => $id])
        );
    }

    private function _validateAuthor($input, $newAuthor = true) {
        $errors = [];

        if (empty($input) && !$newAuthor) {
            $errors[] = 'Нет параметров для обновления';
        }

        if ((isset($input['name']) || $newAuthor) && empty($input['name'])) {
            $errors[] = 'Имя не может быть пустым';
        }

        if ((isset($input['nameAblative']) || $newAuthor) && empty($input['nameAblative'])) {
            $errors[] = 'Имя в творительном падеже не может быть пустым';
        }

        if (!isset($input['avatar'])) {
            return $errors;
        }

        if (empty($input['avatar']['fileName'])) {
            $errors[] = 'Путь до файла не может быть пустым';
        }

        if (empty($input['avatar']['width'])) {
            $errors[] = 'Ширина картинки не задана';
        } elseif (!filter_var($input['avatar']['width'], FILTER_VALIDATE_INT)) {
            $errors[] = 'Ширина картинки должна быть целым числом';
        }

        if (empty($input['avatar']['height'])) {
            $errors[] = 'Высота картинки не задана';
        } elseif (!filter_var($input['avatar']['height'], FILTER_VALIDATE_INT)) {
            $errors[] = 'Высота картинки должна быть целым числом';
        }

        return $errors;
    }


    private function _prepareErrorResponse($response, $errorCode = 404, $errorStatus = 'Not Found') {
        $response->getBody()->write(
            json_encode([
                'error' => [
                    'status' => $errorStatus,
                    'errorCode' => $errorCode
                ]
            ])
        );
        return $response->withStatus($errorCode);
    }
}