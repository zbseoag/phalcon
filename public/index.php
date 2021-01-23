<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Phalcon\{
    Di\FactoryDefault,
    Mvc\Application
};

try {

    $rootPath = realpath('..');
    require_once $rootPath . '/vendor/autoload.php';

    //Load ENV variables
    Dotenv::createImmutable($rootPath)->load();

    //Init Phalcon Dependency Injection
    $di = new FactoryDefault();
    $di->offsetSet('rootPath', function() use($rootPath){
        return $rootPath;
    });

    // Register Service Providers
    $providers = $rootPath . '/config/providers.php';
    if (!file_exists($providers) || !is_readable($providers)) {
        throw new Exception('File providers.php does not exist or is not readable.');
    }

    foreach (include_once $providers as $item) $di->register(new $item());

    $application = new Application($di);
    $response = $application->handle($_SERVER['REQUEST_URI']);
    $response->send();

} catch (Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
