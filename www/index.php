<?php
//echo phpinfo();
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'config/config.php';

try {
    $app = new \Slim\App(['settings' => $config]);

    $container = $app->getContainer();

    $container['db'] = function ($c) {
        $db = $c['settings']['mysql'];
        $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    };

    $app->get('/', function (Request $request, Response $response) {

        $response->getBody()->write('Main Page');

        return $response;
    });

    $app->get('/api/v1/author/{id}', function (Request $request, Response $response, $args) {

        $authorId = (int) $args['id'];
        $query = $this->db->prepare("SELECT * FROM author WHERE id = :id LIMIT 1");
        $query->execute(['id' => $authorId]);
        $author = $query->fetch();

        $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        if (empty($author)) {

            $errorCode = 404;
            echo json_encode([
                'error' => [
                    'status' => 'Not Found',
                    'errorCode' => $errorCode
                ]
            ]);

            return $response->withStatus($errorCode);
        }

        echo json_encode([
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
        ]);
    });

    $app->delete('/api/v1/author/{id}', function (Request $request, Response $response, $args) {

        $authorId = (int) $args['id'];
        $query = $this->db->prepare("DELETE FROM author WHERE id = :id LIMIT 1");
        $query->execute(['id' => $authorId]);

        $response->withHeader('Content-Type', 'application/json; charset=utf-8');

        echo json_encode([
            'response' => []
        ]);

    });

    $app->put('/api/v1/author/{id}', function (Request $request, Response $response, $args) {

        $input = json_decode($request->getBody());

        mydebug($input);

    });

    $app->post('/api/v1/author/{id}', function (Request $request, Response $response, $args) {

        $input = json_decode($request->getBody());

        mydebug($input);
    });

    $app->run();

} catch (Exception $e) {
    echo $e;
}

function mydebug($obj)
{
    echo "<hr><pre>";
    var_dump($obj);
    echo "</pre><hr>";
}
