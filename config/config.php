<?php
declare(strict_types=1);
use Phalcon\Config;

return new Config([
    'database' => [
        'adapter' => getenv('DB_ADAPTER'),
        'host' => getenv('DB_HOST'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'dbname' => getenv('DB_DBNAME'),
        'charset' => getenv('DB_CHARSET'),
    ],

    'application' => [
        'viewsDir' => getenv('VIEWS_DIR'),
        'baseUri' => getenv('BASE_URI'),
    ],

    'acl' => [

        'private' => [
            'companies' =>      ['index', 'search', 'new', 'edit', 'save', 'create', 'delete'],
            'products' =>       ['index', 'search', 'new', 'edit', 'save', 'create', 'delete'],
            'producttypes' =>   ['index', 'search', 'new', 'edit', 'save', 'create', 'delete'],
            'invoices' =>       ['index', 'profile'],
        ],

        'public' => [
            'index'     => ['index'],
            'about'     => ['index'],
            'register'  => ['index'],
            'errors'    => ['show401', 'show404', 'show500'],
            'session'   => ['index', 'register', 'start', 'end'],
            'contact'   => ['index', 'send'],
            'look'      => ['index'],
        ],

    ],
]);


