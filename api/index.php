<?php
/**
 * User: admin
 * Date: 2021/1/23
 * Email: <zbseoag@163.com>
 */
use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Http\Response;

$app = new Micro();


$loader = new Loader();

$loader->registerNamespaces(['Store\Toys' => __DIR__ . '/models/',]);
$loader->register();

$di = new FactoryDefault();
$di->set('db', function () {
    return new Mysql([
        'host'     => 'localhost',
        'username' => 'asimov',
        'password' => 'zeroth',
        'dbname'   => 'robotics',
    ]);
});



// Retrieves all robots
$app->get('/api/robots', function () use ($app) {

    $phql = 'SELECT * FROM Store\Toys\Robots ORDER BY name';
    $robots = $app->modelsManager->executeQuery($phql);

    foreach ($robots as $robot) {
        $data[] = [
            'id'   => $robot->id,
            'name' => $robot->name,
        ];
    }
    echo json_encode($data);

});



$app->get('/api/robots/search/{name}', function ($name) use ($app) {
    $phql = 'SELECT * FROM Store\Toys\Robots WHERE name LIKE :name: ORDER BY name';

    $robots = $app->modelsManager->executeQuery($phql, ['name' => '%' . $name . '%']);

    foreach ($robots as $robot) {
        $data[] = [
            'id'   => $robot->id,
            'name' => $robot->name,
        ];
    }

    echo json_encode($data);

});


$app->get('/api/robots/{id:[0-9]+}', function ($id) use ($app) {

    $phql = 'SELECT * FROM Store\Toys\Robots WHERE id = :id:';
    $robot = $app->modelsManager->executeQuery($phql, ['id' => $id,])->getFirst();

    // Create a response
    $response = new Response();

    if ($robot === false) {
        $response->setJsonContent(['status' => 'NOT-FOUND']);
    } else {
        $response->setJsonContent([
            'status' => 'FOUND',
            'data'   => [
                'id'   => $robot->id,
                'name' => $robot->name
            ]
        ]);
    }
    return $response;
});


$app->post('/api/robots', function () use ($app) {
        $robot = $app->request->getJsonRawBody();

        $phql = 'INSERT INTO Store\Toys\Robots (name, type, year) VALUES (:name:, :type:, :year:)';

        $status = $app->modelsManager->executeQuery($phql, [
            'name' => $robot->name,
            'type' => $robot->type,
            'year' => $robot->year,
        ]);

        $response = new Response();

        if ($status->success() === true) {

            $response->setStatusCode(201, 'Created');
            $robot->id = $status->getModel()->id;

            $response->setJsonContent([
                'status' => 'OK',
                'data'   => $robot,
            ]);
        } else {
            // Change the HTTP status
            $response->setStatusCode(409, 'Conflict');

            // Send errors to the client
            $errors = [];

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent([
                'status'   => 'ERROR',
                'messages' => $errors,
            ]);
        }
        return $response;
    }
);

$app->put(
    '/api/robots/{id:[0-9]+}',
    function ($id) use ($app) {
        $robot = $app->request->getJsonRawBody();

        $phql = 'UPDATE Store\Toys\Robots SET name = :name:, type = :type:, year = :year: WHERE id = :id:';

        $status = $app->modelsManager->executeQuery(
            $phql,
            [
                'id'   => $id,
                'name' => $robot->name,
                'type' => $robot->type,
                'year' => $robot->year,
            ]
        );

        // Create a response
        $response = new Response();

        // Check if the insertion was successful
        if ($status->success() === true) {
            $response->setJsonContent(
                [
                    'status' => 'OK'
                ]
            );
        } else {

            $response->setStatusCode(409, 'Conflict');

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent(
                [
                    'status'   => 'ERROR',
                    'messages' => $errors,
                ]
            );
        }

        return $response;
    }
);


$app->delete('/api/robots/{id:[0-9]+}', function ($id) use ($app) {

        $phql = 'DELETE FROM Store\Toys\Robots WHERE id = :id:';

        $status = $app->modelsManager->executeQuery($phql, ['id' => $id,]);

        $response = new Response();

        if ($status->success() === true) {
            $response->setJsonContent(['status' => 'OK']);
        } else {

            $response->setStatusCode(409, 'Conflict');

            foreach ($status->getMessages() as $message) {
                $errors[] = $message->getMessage();
            }

            $response->setJsonContent([
                'status'   => 'ERROR',
                'messages' => $errors,
           ]);
        }
        return $response;
    }
);


$app->handle();