<?php
namespace Controller;
use Model;

/**
 * @SWG\Swagger(
 *      @SWG\Info(
 *          version="1.0.0",
 *          title="Author API"
 *      ),
 *      host="localhost",
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
     *      description="Not Found"
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
            json_encode([
                'response' => [
                    'id' => $author['id'],
                    'name' => $author['name'],
                    'nameAblative' => $author['nameAblative'],
                    'avatar' => [
                        'fileName' => $author['avatar'],
                        'width' => $author['width'],
                        'height' => $author['height']
                    ]
                ]
            ])
        );
    }

    /**
     * @SWG\Delete(
     *  path="/author/{id}",
     *  summary="Get Author",
     *  description="Returns a author based on a single ID",
     *  @SWG\Parameter(
     *      name="id",
     *      in="path",
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
     *      description="Not Found"
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
                return $response->getBody()->write(
                    json_encode([
                        'response' => []
                    ])
                );
            }
        }
        return $this->_prepareErrorResponse($response);
    }

    private function _updateAuthor($request, $response, $args) {

        $authorId = $args['id'] ?? NULL;

        if (!empty($authorId)) {
            $author = $this->_authorModel->getAutor($authorId);
        }

        if (empty($author)) {
            return $this->_prepareErrorResponse($response);
        }

        $input = json_decode($request->getBody());

        $errors = [];
        if (
            isset($input->avatar->width) && !filter_var($input->avatar->width, FILTER_VALIDATE_INT)
        ) {
            $errors['width'] = 'Ширина картинки должна быть целым числом';
        }

        if (
            isset($input->avatar->height) && !filter_var($input->avatar->height, FILTER_VALIDATE_INT)
        ) {
            $errors['height'] = 'Высота картинки должна быть целым числом';
        }

        if (
            isset($input->name) && empty($input->name)
        ) {
            $errors['name'] = 'Имя не может быть пустым';
        }

        if (
            isset($input->nameAblative) && empty($input->nameAblative)
        ) {
            $errors['nameAblative'] = 'Имя в творительном падеже не может быть пустым';
        }

        if (!empty($errors)) {
            return $this->_prepareErrorResponse($response, 400, $errors);
        }

        $authorUpdateData = [
            'name' => $input->name ?? $author['name'],
            'nameAblative' => $input->nameAblative ?? $author['nameAblative'],
            'avatar' => $input->avatar->fileName ?? $author['avatar'],
            'width' => $input->avatar->width ?? $author['width'],
            'height' => $input->avatar->height ?? $author['height']
        ];

        if (!$this->_authorModel->updateAuthor($authorId, $authorUpdateData)) {
            return $this->_prepareErrorResponse($response);
        }

        return $response->getBody()->write(
            json_encode([
                'response' => [
                    'id' => $authorId
                ]
            ])
        );
    }

    private function _addAuthor($request, $response) {

        $input = json_decode($request->getBody());

        $errors = [];
        if (
            isset($input->avatar->width) && !filter_var($input->avatar->width, FILTER_VALIDATE_INT)
        ) {
            $errors['width'] = 'Ширина картинки должна быть целым числом';
        }

        if (
            isset($input->avatar->height) && !filter_var($input->avatar->height, FILTER_VALIDATE_INT)
        ) {
            $errors['height'] = 'Высота картинки должна быть целым числом';
        }

        if (empty($input->name)) {
            $errors['name'] = 'Имя не может быть пустым';
        }

        if (empty($input->nameAblative)) {
            $errors['nameAblative'] = 'Имя в творительном падеже не может быть пустым';
        }

        if (!empty($errors)) {
            return $this->_prepareErrorResponse($response, 400, $errors);
        }

        $authorData = [
            'name' => $input->name ?? '',
            'nameAblative' => $input->nameAblative ?? '',
            'avatar' => $input->avatar->fileName ?? '',
            'width' => $input->avatar->width ?? 0,
            'height' => $input->avatar->height ?? 0
        ];

        $id = $this->_authorModel->addAuthor($authorData);

        if (empty($id)) {
            return $this->_prepareErrorResponse($response);
        }

        return $response->getBody()->write(
            json_encode([
                'response' => [
                    'id' => $id
                ]
            ])
        );
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