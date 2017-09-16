<?php
namespace Controller;
use Model;

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